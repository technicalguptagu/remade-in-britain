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
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Paymentmode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    //Function to render payment mode details of seller
    public function render(Varien_Object $row) {
       $value       = $row->getData($this->getColumn()->getIndex());
       $_collection = Mage::getModel('marketplace/sellerprofile')->getCollection()
                        ->addFieldToFilter('seller_id',$value) 
                        ->addFieldToSelect('bank_payment')                
                        ->addFieldToSelect('paypal_id');
       foreach($_collection as $collection)
       {
           $bank_payment = $collection->getBankPayment();
           $paypal_id    = $collection->getPaypalId(); 
           if($bank_payment && $paypal_id)
           {
               $ret = 'Bank Payment:' .$bank_payment.'Paypal Id:'.$paypal_id;
               return $ret;
           }
           elseif($paypal_id){
               $ret = 'Paypal Id:'.$paypal_id;
               return $ret;
           }
            elseif($bank_payment){
                $ret = 'Bank Payment:' .$bank_payment;
                return $ret;
            }
        }
    }
} 