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
class Apptha_Marketplace_Block_Adminhtml_Renderersource_OrderProductdetails extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
  //Function to get product order details
  public function render(Varien_Object $row)
  {
    $value          = $row->getData($this->getColumn()->getIndex());
    $commission     = Mage::getModel('marketplace/commission')->load($value);
    $getProductId   = $commission->getProductId();
    $item_item      = Mage::getModel('catalog/product')->load($getProductId);
    $pId            = Mage::helper("adminhtml")->getUrl('adminhtml/catalog_product/edit', array('id' => $getProductId));
    return '<a href="' . $pId . '" target="_blank" >' . $item_item->getName() . '</a>  X ' . '<strong>' . ($commission->getProductQty()) . '</strong>';
  }
} 