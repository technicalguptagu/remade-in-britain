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
class Apptha_Marketplace_Block_Adminhtml_Commission_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_objectId   = 'id';
        $this->_blockGroup = 'marketplace';       
        $this->_controller = 'adminhtml_commission';      
    }

    public function getHeaderText()
    {
      $seller_id    = $this->getRequest()->getParam('id');
      $collection   = Mage::getModel('marketplace/sellerprofile')->load($seller_id, 'seller_id');       
      $seller_title = $collection['store_title']; 
      if(!empty($seller_title)){
        return $this->__('Payment Details of '.$seller_title);
      } else {
        return $this->__('Payment Details');
      }
    }
}
