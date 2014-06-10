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
class Apptha_Marketplace_Block_Adminhtml_Paymentinfo extends Mage_Adminhtml_Block_Widget_Grid_Container 
{
    public function __construct() {
        $seller_id         = $this->getRequest()->getParam('id');
        $collection        = Mage::getModel('marketplace/sellerprofile')->load($seller_id, 'seller_id');       
        $seller_title      = $collection['store_title'];     
        $this->_controller = 'adminhtml_paymentinfo';
        $this->_blockGroup = 'marketplace';
        $this->_headerText = Mage::helper('marketplace')->__('Transaction History of '.$seller_title);
        $this->_addButton('button1', array(
            'label'     => Mage::helper('marketplace')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('marketplaceadmin/adminhtml_commission/index') .'\')',
            'class'     => 'back',
        ));
        parent::__construct();
        $this->_removeButton('add');
    }
} 