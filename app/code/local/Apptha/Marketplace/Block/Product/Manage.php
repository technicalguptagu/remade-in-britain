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
class Apptha_Marketplace_Block_Product_Manage extends Mage_Core_Block_Template
{
     protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $manage_collection = $this->manageProducts();            
        $this->setCollection($manage_collection);
        $pager  = $this->getLayout()
                    ->createBlock('page/html_pager', 'my.pager')                   
                    ->setCollection($manage_collection);
        $pager->setAvailableLimit(array(10 => 10,20 => 20,30=>30,50=>50));
        $pager->setLimit(20);
        $this->setChild('pager', $pager);
        return $this;
    }
    //Function to get the product details
   public function manageProducts()
   {           
       $entity_ids = $this->getRequest()->getParam('id'); 
       $delete = $this->getRequest()->getPost('multi');
       if(count($entity_ids)>0 && $delete=='delete'){
          foreach($entity_ids as $_entity_ids){
                Mage::register('isSecureArea', true);
                Mage::helper('marketplace/marketplace')->deleteProduct($_entity_ids); 
                Mage::unregister('isSecureArea');                 
            }             
            Mage::getSingleton('core/session')->addSuccess($this->__("selected Products are Deleted Successfully"));          
        }    
        $filter_id      = $this->getRequest()->getParam('filter_id');
        $filter_name    = $this->getRequest()->getParam('filter_name');
        $filter_price   = $this->getRequest()->getParam('filter_price');
        $filter_status  = $this->getRequest()->getParam('filter_status');
        $cusId          = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $products       = Mage::getModel('catalog/product')->getCollection();
                            $products->addAttributeToSelect('*');
                            $products->addAttributeToFilter('seller_id',array('eq' => $cusId));
           if($filter_id!=''){
               $products->addAttributeToFilter('entity_id',array('eq'=>$filter_id));
           }
           if($filter_name!=''){
               $products->addAttributeToFilter('name',array('like' => '%'.$filter_name.'%'));           
           }
           if($filter_price!=''){
               $products->addAttributeToFilter('price',array('eq' =>$filter_price));    
           }
           if($filter_status!=0){
               $products->addAttributeToFilter('status',array('eq' =>$filter_status));    
           }
          $products->addAttributeToSort('entity_id', 'DESC');
          return $products; 
    }
    //Function to display pagination
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    //Function to get multi select url
    public function getmultiselectUrl()
    {
        return  Mage::getUrl('marketplace/product/manage');
    }
    
}