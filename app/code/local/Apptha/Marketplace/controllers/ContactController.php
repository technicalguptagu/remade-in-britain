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
class Apptha_Marketplace_ContactController extends Mage_Core_Controller_Front_Action {
    // Initilize index action  
    public function indexAction() {
        Mage::helper('marketplace')->checkMarketplaceKey();
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('*/*/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    // Contact form action
    public function formAction() {
        // Initilize customer and seller group id
        $customer_group_id = $seller_group_id = $customer_status = '';
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $seller_group_id = Mage::helper('marketplace')->getGroupId();
        $customer_status = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customer_group_id != $seller_group_id) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Checking whether customer approved or not  
        if ($customer_status != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    // Email Post action
    public function postAction() {
        // Initilize customer and seller group id
        $customer_group_id = $seller_group_id = $customer_status = '';
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $seller_group_id = Mage::helper('marketplace')->getGroupId();
        $customer_status = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customer_group_id != $seller_group_id) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Checking whether customer approved or not  
        if ($customer_status != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        if (Mage::getStoreConfig('marketplace/admin_approval_seller_registration/contact_admin') == 1) {
            $subject = $message = '';
            $subject = $this->getRequest()->getPost('subject');
            $message = $this->getRequest()->getPost('message');
            if (!empty($subject) && !empty($message)) {
                // Sending email to admin        
                try {
                    $template_id = (int) Mage::getStoreConfig('marketplace/admin_approval_seller_registration/contact_email_template_selection');
                    $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
                    $toMailId = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                    $toName = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                    // Selecting template id
                    if ($template_id) {
                        $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
                    } else {
                        $emailTemplate = Mage::getModel('core/email_template')
                                ->loadDefault('marketplace_admin_approval_seller_registration_contact_email_template_selection');
                    }
                    $seller_id = Mage::getSingleton('customer/session')->getId();
                    $customer = Mage::getModel('customer/customer')->load($seller_id);
                    $seller_info = Mage::getModel('marketplace/sellerprofile')->load($seller_id,'seller_id');                   
                    $selleremail = $customer->getEmail();
                    $recipient = $toMailId;
                    $sellername = $customer->getName();
                    $contactno = $seller_info['contact'];       
                    $emailTemplate->setSenderName($sellername);
                    $emailTemplate->setSenderEmail($selleremail);
                    $emailTemplateVariables = (array('ownername' => $toName, 'sellername' => $sellername, 'selleremail' => $selleremail, 'subject' => $subject, 'message' => $message, 'contactno' => $contactno ));
                    $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                    $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                    $emailTemplate->send($recipient, $sellername, $emailTemplateVariables);
                    Mage::getSingleton('core/session')->addSuccess($this->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                    $this->_redirect('*/*/form');
                } catch (Mage_Core_Exception $e) {
                    // Error message redirect to create new product page
                    Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                    $this->_redirect('*/*/form');;
                } catch (Exception $e) {
                    // Error message redirect to create new product page
                    Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                     $this->_redirect('*/*/form');
                }
            } else {
                Mage::getSingleton('core/session')->addError($this->__('Please enter all required fields'));
                 $this->_redirect('*/*/form');
            }
        }
    }
    // Getting session customer
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }
}