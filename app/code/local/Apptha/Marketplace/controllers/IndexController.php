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
class Apptha_Marketplace_IndexController extends Mage_Core_Controller_Front_Action
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
        if(Mage::helper('marketplace')->checkMarketplaceKey()!= ''){
        $msg = Mage::helper('marketplace')->checkMarketplaceKey();
        Mage::app()->getResponse()->setBody($msg);  
        return;
        } else {
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
             
        }
        $this->loadLayout();
             $this->renderLayout(); 
       
    }
    }
    public function bannerAction(){
         Mage::helper('marketplace')->checkMarketplaceKey(); 
        $this->loadLayout();     
        $this->renderLayout();
    }
}