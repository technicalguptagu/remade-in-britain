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
class Apptha_Marketplace_Block_Displayseller extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
       $id           = $this->getRequest()->getParam('id');       
       $seller_page  = Mage::getModel('marketplace/sellerprofile')->collectprofile($id);      
       //set Meta information for the seller
       $head         = $this->getLayout()->getBlock('head');
                        $head->setTitle($seller_page->getStoreTitle());
                        $head->setKeywords($seller_page->getMetaKeyword());
                        $head->setDescription($seller_page->getMetaDescription()); 
                        $display_collection = $this->categoryProducts();            
                        $this->setCollection($display_collection);
       $pager        = $this->getLayout()
                        ->createBlock('page/html_pager', 'my.pager')                      
                        ->setCollection($display_collection); 
                 $pager->setAvailableLimit(array(10 => 10,20 => 20,30=>30,50=>50));
                 $pager->setLimit(20);
       $this->setChild('pager', $pager);
       return $this;     
    }
    //Display pagination
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }
       
   //Function to get the Seller products
    function sellerproduct($sellerid){
         $collection = Mage::getModel('catalog/product')->getCollection()
                        ->addFieldToFilter('seller_id',$sellerid)
                        ->joinField('category_id',
                            Mage::getConfig()->getTablePrefix().'catalog_category_product',
                            'category_id',
                            'product_id=entity_id',
                            null,
                            'right');
        $value       = $collection->getData('category_id');
        return $value;                 
    }
    //Get category products
    function categoryProducts(){
        $display_cat_product = $this->getRequest()->getParam('category_name');
        $sort_product        = $this->getRequest()->getParam('sorting');
        $id                  = $this->getRequest()->getParam('id');
        $catagory_model      = Mage::getModel('catalog/category')->load($display_cat_product);
        $collection          = Mage::getResourceModel('catalog/product_collection');
                                $collection->addCategoryFilter($catagory_model); //category filter
                                $collection->addAttributeToFilter('status',1); //only enabled product
                                $collection->addAttributeToSelect('*'); //add product attribute to be fetched
                                $collection->addAttributeToFilter('seller_id',$id);
                                $collection->addStoreFilter();         
                                $collection->addAttributeToSort($sort_product);
        return $collection;
    }
    //Get category Url
    function getCategoryUrl($customer_id,$id){
        return  Mage::getUrl('marketplace/seller/categorylist',array('id'=>$id,'cat'=>$customer_id));
    }
    //Get Url for review form
    function reviewUrl($customer_id,$id,$product_id){
        return  Mage::getUrl('marketplace/seller/reviewform',array('id'=>$id,'cus'=>$customer_id,'product'=>$product_id));
    }
    //Get login url if customer not logged in
    function loginUrl(){
        return  Mage::getUrl('customer/account/login/');
    }
    //Get all reviews link
    function getAllreview($customer_id,$id,$product_id){
        return  Mage::getUrl('marketplace/seller/allreview',array('id'=>$id,'cus'=>$customer_id,'product'=>$product_id));
  }    
} 
