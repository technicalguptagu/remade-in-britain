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
class Apptha_Marketplace_Model_Transaction extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('marketplace/transaction');
    }
    //Function to change the commission status
    public function changeStatus($commission_id)
    {        
        if($commission_id!=''){
            $now = Mage::getModel('core/date')->date('Y-m-d H:i:s', time());
            $collection = Mage::getModel('marketplace/transaction')->load($commission_id,'commission_id')
                    ->setReceivedStatus('1')
                    ->setAcknowledgeDate($now);
            $collection->save();
            return true;
        }
    }
    //Function to get the payment status from seller
    public function getPaymentstatus($_id){
        $collection = Mage::getModel('marketplace/transaction')->load($_id,'commission_id');
        return $collection;
    }
    //Function to get the payment comment from admin
    public function getPaymentcomment($_id){
        $collection = Mage::getModel('marketplace/transaction')->load($_id,'seller_id');
        return $collection;
    }    
} 