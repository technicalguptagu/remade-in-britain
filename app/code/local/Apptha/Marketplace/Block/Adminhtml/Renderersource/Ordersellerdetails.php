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
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordersellerdetails extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
       $value       = $row->getData($this->getColumn()->getIndex());
       $customer    = Mage::getModel('customer/customer')->load($value);
       $cId         = Mage::helper("adminhtml")->getUrl('adminhtml/customer/edit', array('id' => $value));
       $hostemail   = "<a title='Click to view detail' target='_blank' href='" . $cId . "'>" . $customer->getEmail() . "</a>";
       return $hostemail;
     }
} 