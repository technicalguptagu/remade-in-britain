<?php
/*
 ***********************************************************/
/**
 * @name          : Market Place
 * @version	  : 1.2
 * @package       : apptha
 * @since         : Magento 1.5
 * @subpackage    : Market Place
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2014 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Block File
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 26,2014
 * */
/*
 ***********************************************************/
class Apptha_Marketplace_Block_Customerreview extends Mage_Core_Block_Template
{
    //Function to get all review collection
       protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $review_collection = $this->getCustomer();            
        $this->setCollection($review_collection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')                   
                ->setCollection($review_collection);
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
    //Function to get all review collection
    function getCustomer()
    {
       if (Mage::getSingleton('customer/session')->isLoggedIn()) {
           $customer    = Mage::getSingleton('customer/session')->getCustomer();
           $id          = $customer->getId();
           $store_id    = Mage::app()->getStore()->getId();
           $collection  = Mage::getModel('marketplace/sellerreview')
                            ->getCollection()               
                            ->addFieldToFilter('status',1)
                             ->addFieldToFilter('store_id',$store_id)
                            ->addFieldToFilter('customer_id',$id);
       }     
       return $collection; 
   } 
} 
