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
class Apptha_Marketplace_Block_Categorylist extends Mage_Core_Block_Template
{
  protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $seller_category_collection = $this->getSellercategoryproducts();            
        $this->setCollection($seller_category_collection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')                   
                ->setCollection($seller_category_collection);  
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
    //Function to get seller product categories
    function getSellercategoryproducts(){
        $id             = $this->getRequest()->getParam('id');
        $cat_id         = $this->getRequest()->getParam('cat');
        $sort_product   = $this->getRequest()->getParam('sorting');
        $catagory_model = Mage::getModel('catalog/category')->load($cat_id);
        $collection     = Mage::getResourceModel('catalog/product_collection');
                            $collection->addCategoryFilter($catagory_model); //category filter
                            $collection->addAttributeToFilter('status',1); //only enabled product
                            $collection->addAttributeToSelect('*'); //add product attribute to be fetched
                            $collection->addAttributeToFilter('seller_id',$id);
                            $collection->addStoreFilter();         
                            $collection->addAttributeToSort($sort_product);
      return $collection;
    }
    //Function to get particular category information
    function getCategoryinfo(){
        $cat_id     = $this->getRequest()->getParam('cat');   
        $category   = Mage::getModel('catalog/category')->load($cat_id);
        return $category;
    }
} 
