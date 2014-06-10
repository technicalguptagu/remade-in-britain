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
class Apptha_Marketplace_OrderController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }
    /**
     * index action
     */
    public function indexAction() {
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Seller order manage
     */
    public function manageAction() {
        Mage::helper('marketplace')->checkMarketplaceKey();
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
     /**
     * Seller order view
     */
     function vieworderAction(){
        Mage::helper('marketplace')->checkMarketplaceKey();
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
          $this->loadLayout();
          $this->renderLayout();  
      }
     /**
     * Seller transaction view
     */
      function viewtransactionAction(){
        Mage::helper('marketplace')->checkMarketplaceKey();
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
          $this->loadLayout();
          $this->renderLayout();  
      }
       /**
     * Seller payment acknowledgement
     */
      function acknowledgeAction(){
        Mage::helper('marketplace')->checkMarketplaceKey();
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        } 
          $this->loadLayout();
          $this->renderLayout();
          $commission_id = $this->getRequest()->getParam('commissionid');        
          if($commission_id!=''){
          $collection = Mage::getModel('marketplace/transaction')->changeStatus($commission_id);          
          if($collection==1){
              Mage::getSingleton('core/session')->addSuccess($this->__("Payment received status has been updated")); 
              $this->_redirect('marketplace/order/viewtransaction');
          } else  {
             Mage::getSingleton('core/session')->addError($this->__('Payment received status was not updated'));
             $this->_redirect('marketplace/order/viewtransaction'); 
          }
      }
   }
} 