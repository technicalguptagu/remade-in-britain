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
class Apptha_Marketplace_Adminhtml_ManagesellerController extends Mage_Adminhtml_Controller_action
{
    protected function _initAction() {
      $this->loadLayout()
      ->_setActiveMenu('marketplace/items')
      ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Seller Manager'));
      return $this;
    }
    public function indexAction() { 
      $this->_initAction()
      ->renderLayout();
    }
    public function editAction() {
       $id     = $this->getRequest()->getParam('id');
       $model  = Mage::getModel('marketplace/marketplace')->load($id);
       if ($model->getId() || $id == 0) {
          $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
          if (!empty($data)) {
              $model->setData($data);
          }
            Mage::register('marketplace_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('marketplace/items');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Seller Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Seller News'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_marketplace_edit'))
                 ->_addLeft($this->getLayout()->createBlock('marketplace/adminhtml_marketplace_edit_tabs'));
            $this->renderLayout();
            } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Seller details does not exist'));
            $this->_redirect('*/*/');
            }
    }
    public function newAction() {
            $this->_forward('edit');
    }
    public function saveAction() {  
        $data = $this->getRequest()->getPost();
        if($data){
                $model = Mage::getModel('marketplace/marketplace');		
                   $model->setData($data)
                   ->setId($this->getRequest()->getParam('id'));
                try {
                        if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                                $model->setCreatedTime(now())
                                        ->setUpdateTime(now());
                        } else {
                                $model->setUpdateTime(now());
                        }
                        $model->save();
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Seller approved successfully.'));
                        Mage::getSingleton('adminhtml/session')->setFormData(false);
                        if ($this->getRequest()->getParam('back')) {
                                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                                return;
                        }
                        $this->_redirect('*/*/');
                        return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Seller details not updated'));
    $this->_redirect('*/*/');
 }
    public function massDeleteAction() {
    $marketplaceIds = $this->getRequest()->getParam('marketplace');
    if(!is_array($marketplaceIds)) {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one seller'));
    } else {
        try {
            foreach ($marketplaceIds as $marketplaceId) {
                Mage::helper('marketplace/marketplace')->deleteSeller($marketplaceId);
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
    public function setcommissionAction() {
         $this->_initAction()
         ->renderLayout();
     }
     public function savecommissionAction() { 
      if( $this->getRequest()->getParam('id') > 0 ) {
         $id = $this->getRequest()->getParam('id');
          $commission = $this->getRequest()->getParam('commission'); 
                try {
                        $collection = Mage::getModel('marketplace/sellerprofile')->load($id, 'seller_id');
                        $getId = $collection->getId();
                        if($getId!=''){                           
                        Mage::getModel('marketplace/sellerprofile')->load($id,'seller_id')
                                ->setCommission($commission)->save();
                        } else
                        {      
                            $collection = Mage::getModel('marketplace/sellerprofile');
                                            $collection->setCommission($commission);
                                            $collection->setSellerId($id);
                                            $collection->save();
                        }                    
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Seller commission saved successfully .'));
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
         $commission = $this->getRequest()->getParam('commission');
                try {
                        $model = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
                        $model->setCustomerstatus('0')
                        ->save();                        
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
                     $customer      = Mage::getModel('customer/customer')->load($id);
                     $recipient     = $customer->getEmail();
                     $cname         = $customer->getName();
                                        $emailTemplate->setSenderName(ucwords($toName));    
                                        $emailTemplate->setSenderEmail($toMailId);         
                     $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));           
                                                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                     $processedTemplate      = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                                $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);   
                     //end email
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Seller approved successfully.'));
                        $this->_redirect('*/*/');
                     } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/*/');
                    }
            }
            $this->_redirect('*/*/');
        }
  public function approveAction() {
     if( $this->getRequest()->getParam('id') > 0 ) {
         $id = $this->getRequest()->getParam('id');
         $commission = $this->getRequest()->getParam('commission');
                try {
                        $model = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
                        $model->setCustomerstatus('1')
                        ->save();                        
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
                     $customer      = Mage::getModel('customer/customer')->load($id);
                     $recipient     = $customer->getEmail();
                     $cname         = $customer->getName();
                                        $emailTemplate->setSenderName(ucwords($toName));    
                                        $emailTemplate->setSenderEmail($toMailId);         
                     $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));           
                                                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                     $processedTemplate      = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                                $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);   
                     //end email
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Seller approved successfully.'));
                        $this->_redirect('*/*/');
                     } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/*/');
                    }
            }
            $this->_redirect('*/*/');
        }
        
     public function disapproveAction() {
           if( $this->getRequest()->getParam('id') > 0 ) {
               $id = $this->getRequest()->getParam('id');
                    try {
                            $model = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
                                    $model->setCustomerstatus('2')
                                    ->save();
                            //send email
                        $template_id    = (int)Mage::getStoreConfig('marketplace/admin_approval_seller_registration/seller_email_template_disapprove'); 
                        $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id'); 
                        $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                        $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                         if ($template_id) {
                           $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
                         } else {  
                           $emailTemplate = Mage::getModel('core/email_template')
                                             ->loadDefault('marketplace_admin_approval_seller_registration_seller_email_template_disapprove'); 
                         }     
                         $customer          = Mage::getModel('customer/customer')->load($id);
                         $recipient         = $customer->getEmail();
                         $cname             = $customer->getName();
                                                $emailTemplate->setSenderName(ucwords($toName));    
                                                $emailTemplate->setSenderEmail($toMailId);         
                         $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));           
                                                    $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                         $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                                $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);    
                         //end email
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Seller disapproved.'));
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
                        $model = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
                                $model->setGroupId(1);
                                $model->save();                          
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Seller successfully deleted'));
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
       Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one seller'));
    } else {
        try {
            foreach ($marketplaceIds as $marketplaceId) {
               Mage::helper('marketplace/marketplace')->approveSellerStatus($marketplaceId);
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
                         $customer          = Mage::helper('marketplace/marketplace')->loadCustomerData($marketplaceId);
                         $recipient         = $customer->getEmail();
                         $cname             = $customer->getName();
                                                $emailTemplate->setSenderName(ucwords($toName));    
                                                $emailTemplate->setSenderEmail($toMailId);         
                         $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));           
                                                   $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                         $processedTemplate      = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                                    $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);   
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
public function massDisapproveAction() {
    $marketplaceIds = $this->getRequest()->getParam('marketplace');
    if(!is_array($marketplaceIds)) {
       Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select at least one seller'));
    } else {
        try {
            foreach ($marketplaceIds as $marketplaceId) {
               Mage::helper('marketplace/marketplace')->disapproveSellerStatus($marketplaceId);
                  //send email
                        $template_id    = (int)Mage::getStoreConfig('marketplace/admin_approval_seller_registration/seller_email_template_disapprove'); 
                        $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id'); 
                        $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                        $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                         if ($template_id) {
                           $emailTemplate = Mage::helper('marketplace/marketplace')->loadEmailTemplate($template_id);
                         } else {  
                           $emailTemplate = Mage::getModel('core/email_template')
                                             ->loadDefault('marketplace_admin_approval_seller_registration_seller_email_template_disapprove'); 
                         }     
                         $customer          = Mage::helper('marketplace/marketplace')->loadCustomerData($marketplaceId);
                         $recipient         = $customer->getEmail();
                         $cname             = $customer->getName();
                                                $emailTemplate->setSenderName(ucwords($toName));    
                                                $emailTemplate->setSenderEmail($toMailId);         
                         $emailTemplateVariables = (array('ownername'=>ucwords($toName),'cname'=>ucwords($cname)));           
                                                    $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                         $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                         $emailTemplate->send($recipient,ucwords($cname),$emailTemplateVariables);    
                         //end email
                    }   
                 Mage::getSingleton('adminhtml/session')->addSuccess(
                 Mage::helper('adminhtml')->__(
                    'A total of %d record(s) is successfully disapproved', count($marketplaceIds)
                )
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }
    $this->_redirect('*/*/index');
} 
} 