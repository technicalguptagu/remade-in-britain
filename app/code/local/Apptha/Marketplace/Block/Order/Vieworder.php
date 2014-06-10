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
class Apptha_Marketplace_Block_Order_Vieworder extends Mage_Core_Block_Template 
{
 //Function to get particular order information
 function viewOrder($order_id){
     $order = Mage::getModel('marketplace/commission')->getCollection();
                $order->addFieldToSelect('*');
                $order->addFieldToFilter('seller_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
                $order->addFieldToFilter('order_id', $order_id);
     return $order;
 }   
}