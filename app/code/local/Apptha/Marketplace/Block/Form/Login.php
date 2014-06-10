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
class Apptha_Marketplace_Block_Form_Login extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('marketplace')->__('Seller Login'));
        return parent::_prepareLayout();
    }
    //Function to get login data post url
    public function getPostActionUrl()
    {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $secure=strstr($currentUrl,"https");
        if($secure == true)
        {
            return $this->getUrl('*/*/loginPost',array('_secure'=>true));
        }  else {
         return $this->getUrl('*/*/loginPost');
        } 
    }
    //Function to get registration url
    public function getCreateAccountUrl()
    {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $secure=strstr($currentUrl,"https");
        if($secure == true)
        {
            return $this->getUrl('*/*/create',array('_secure'=>true));
        }  else {
            return $this->getUrl('*/*/create');
        }        
    }
    //Function to get forget password url
    public function getForgotPasswordUrl()
    {
       return $this->helper('customer')->getForgotPasswordUrl();
    }
}