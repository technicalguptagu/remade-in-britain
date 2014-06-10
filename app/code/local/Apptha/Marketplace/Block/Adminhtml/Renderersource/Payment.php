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
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Payment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    //Function to pay amount from admin to seller
    public function render(Varien_Object $row) {
       $id = $row->getData();     
      foreach($id as $_id)
      {      
       $_collection    = Mage::getModel('marketplace/transaction')->getCollection()
                            ->addFieldToSelect('seller_commission')
                            ->addFieldToFilter('seller_id', $_id)
                            ->addFieldToFilter('paid', 0);
        $_collection->getSelect()
                ->columns('SUM(	seller_commission) AS seller_commission')
                ->group('seller_id');
        foreach ($_collection as $amount) {
          $total = $amount->getSellerCommission();
        }
        $_collection_paid    = Mage::getModel('marketplace/transaction')->getCollection()
                            ->addFieldToSelect('seller_commission')
                            ->addFieldToFilter('seller_id', $id)
                            ->addFieldToFilter('paid', 1);
        $_collection_paid->getSelect()
                ->columns('SUM(	seller_commission) AS seller_commission')
                ->group('seller_id');
        foreach ($_collection_paid as $amount_paid) {
           $total_paid = $amount_paid->getSellerCommission();
        }
        //echo $total.'data'. $total_paid;exit;
        if(empty($total)&&!empty($total_paid)) {
           $result = Mage::helper('marketplace')->__('Paid'); 
        } elseif(empty($total)&&empty($total_paid)) {
            $result = Mage::helper('marketplace')->__('NIL');   
        }
        else{
            $result = "<a href='".$this->getUrl('*/*/addcomment/', array('id' => $_id))."' title='".Mage::helper('marketplace')->__('click to Pay')."'>".Mage::helper('marketplace')->__('Pay')."</a>";   
        }
        return $result;
      }             
    }
} 