<?php
/*
 * ********************************************************* */
/**
 * @name          : Market Place
 * @version	  : 1.2
 * @package       : apptha
 * @since         : Magento 1.5
 * @subpackage    : Market Place
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2014 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 26,2014
 * */
/*
 * ********************************************************* */
class Apptha_Marketplace_Adminhtml_SellerreviewController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('marketplace/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Seller Review'));
        return $this;
    }   
    public function indexAction() {             
        $this->_initAction()
        ->renderLayout();
    }
    public function massDeleteAction() {
        $marketplaceIds = $this->getRequest()->getParam('marketplace');
        if(!is_array($marketplaceIds)) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one review'));
        } else {
            try {
                foreach ($marketplaceIds as $marketplaceId) {
                     Mage::helper('marketplace/marketplace')->deleteReview($marketplaceId); 
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($marketplaceIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function approveAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            $id = $this->getRequest()->getParam('id');
                 try {
                         $model      = $collection = Mage::getModel('marketplace/sellerreview')->load($this->getRequest()->getParam('id'));
                                        $model->setStatus('1')
                                        ->save();
                     $customeId      = $model->getCustomerId();
                     $sellerId       = $model->getSellerId();
                     //send email
                     $template_id    = (int)Mage::getStoreConfig('marketplace/admin_approval_seller_registration/seller_email_template_selection');
                     $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id'); 
                     $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                     $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                      if ($template_id) {
                        $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
                      } else {  
                        $emailTemplate = Mage::getModel('core/email_template')
                                          ->loadDefault('marketplace_admin_approval_seller_registration_seller_email_template_selection'); 
                      }
                      //Get customer data 
                      $customer         = Mage::getModel('customer/customer')->load($customeId);
                      $recipient        = $customer->getEmail(); 
                      $cname            = $customer->getName();
                                            $emailTemplate->setSenderName(ucwords($toName));    
                                            $emailTemplate->setSenderEmail($toMailId);         
                      $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));           
                                            $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                      $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                            $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);
                      //Get Seller data
                      $seller_data       = Mage::getModel('customer/customer')->load($sellerId);
                      $recipient_seller  = $seller_data->getEmail(); 
                      $cname_seller      = $seller_data->getName();
                      $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname_seller))); 
                      $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                            $emailTemplate->send($recipient_seller,ucwords($cname_seller),$emailTemplateVariables); 
                      //end email
                         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Review approved successfully.'));
                         $this->_redirect('*/*/');
                 } catch (Exception $e) {
                         Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                         $this->_redirect('*/*/');
                 }
         }
         $this->_redirect('*/*/');
 }
  public function pendingAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            $id = $this->getRequest()->getParam('id');
                 try {
                         $model = Mage::getModel('marketplace/sellerreview')->load($this->getRequest()->getParam('id'));
                         $model->setStatus('0')
                         ->save();
                         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Review is Pending.'));
                         $this->_redirect('*/*/');
                 } catch (Exception $e) {
                         Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                         $this->_redirect('*/*/');
                 }
         }
         $this->_redirect('*/*/');
 }
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
                try {				
                        // Reset group id                                                           
                         $model = Mage::getModel('marketplace/sellerreview');
                         $model->setId($this->getRequest()->getParam('id'))->delete(); 
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Review successfully deleted'));
                        $this->_redirect('*/*/');
                } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
        }
        $this->_redirect('*/*/');
    }
   public function massApproveAction() {
        $marketplaceIds = $this->getRequest()->getParam('marketplace');
        if(!is_array($marketplaceIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one review'));
        } else {
            try {
                foreach ($marketplaceIds as $marketplaceId) {
                     $model = Mage::helper('marketplace/marketplace')->approveReview($marketplaceId);                  
                     $customeId = $model->getCustomerId();
                     $sellerId = $model->getSellerId();
                     //send email
                            $template_id    = (int)Mage::getStoreConfig('marketplace/admin_approval_seller_registration/seller_email_template_selection');
                            $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id'); 
                            $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                            $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                             if ($template_id) { 
                               $emailTemplate = Mage::helper('marketplace/marketplace')->loadEmailTemplate($template_id);
                             } else {  
                               $emailTemplate = Mage::getModel('core/email_template')
                                                 ->loadDefault('marketplace_admin_approval_seller_registration_seller_email_template_selection'); 
                             }
                             //Get customer data  
                             $customer = Mage::helper('marketplace/marketplace')->loadCustomerData($customeId);
                             $recipient = $customer->getEmail(); 
                             $cname = $customer->getName();
                             $emailTemplate->setSenderName(ucwords($toName));    
                             $emailTemplate->setSenderEmail($toMailId);         
                             $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));           
                             $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                             $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                             $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);
                             
                             //Get Seller data
                             $seller_data = Mage::helper('marketplace/marketplace')->loadCustomerData($sellerId);
                             $recipient_seller = $seller_data->getEmail(); 
                             $cname_seller = $seller_data->getName();
                             $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname_seller))); 
                             $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                             $emailTemplate->send($recipient_seller,ucwords($cname_seller),$emailTemplateVariables); 
                             
                             //end email
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'A total of %d record(s) is successfully approved', count($marketplaceIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massPendingAction() {
        $marketplaceIds = $this->getRequest()->getParam('marketplace');
        if(!is_array($marketplaceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one review'));
        } else {
            try {
                foreach ($marketplaceIds as $marketplaceId) {
                    Mage::helper('marketplace/marketplace')->approveReview($marketplaceId);                      
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'A total of %d record(s) is pending', count($marketplaceIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
