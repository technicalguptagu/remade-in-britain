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
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Amountremaining extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    //Function to get data of remaining amount from admin
    public function render(Varien_Object $row)
    {
        $return      = '';
        $value       = $row->getData($this->getColumn()->getIndex());
        $_collection = Mage::getModel('marketplace/transaction')->getCollection()
                        ->addFieldToSelect('seller_commission')
                        ->addFieldToFilter('seller_id', $value)
                        ->addFieldToFilter('paid', 0);
        $_collection->getSelect()
                ->columns('SUM(	seller_commission) AS seller_commission')
                ->group('seller_id');
        foreach ($_collection as $amount) 
        {
            $return = $amount->getSellerCommission();
        }
        return Mage::helper('core')->currency($return, true, false);
    }
} 