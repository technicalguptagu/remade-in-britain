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
class Apptha_Marketplace_Block_Order_Viewtransaction extends Mage_Core_Block_Template 
{
   protected function _prepareLayout()
    {
        parent::_prepareLayout();          
        $collection = $this->getTransactionhistory();            
        $this->setCollection($collection);
        $pager = $this->getLayout()
                 ->createBlock('page/html_pager', 'my.pager')                   
                 ->setCollection($collection); 
        $pager->setAvailableLimit(array(10 => 10,20 => 20,30=>30,50=>50));
        $pager->setLimit(10);
        $this->setChild('pager', $pager);       
        return $this;
    }
   //Function to get the Pagination
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }  
    public function getTransactionhistory(){       
        $customer    = Mage::getSingleton("customer/session")->getCustomer();
        $customer_id = $customer->getId();
        $collection  = Mage::getModel('marketplace/transaction')->getCollection()->addFieldToFilter('seller_id',$customer_id)->addFieldToFilter('paid',1)->setOrder('id', 'DESC');       
        return $collection;
    }
    public function getAcknowledge($commission_id){
       return Mage::getUrl('marketplace/order/acknowledge',array('commissionid'=>$commission_id)); 
    }
} 