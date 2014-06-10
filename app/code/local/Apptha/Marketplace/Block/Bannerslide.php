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
class Apptha_Marketplace_Block_Bannerslide extends Mage_Core_Block_Template
{
    //Get Product Collection
    public function getProductCollections()
    {
            $getProductCollection = Mage::getModel('marketplace/marketplace')->getProductCollection();
            return $getProductCollection;
    }
    //Function to get most popular products
     public function getMostpopular()
    {
      $_productCollection = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', array('eq' => 1));
        return $_productCollection;
    }
    public function getNewproduct()
    {
    // $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addFieldToFilter('seller_id',$sellerid);
    $storeId = Mage::app()->getStore()->getId();
    $todayDate = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    // $category       = Mage::getModel('catalog/category')->load($catId);
    $collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('news_to_date', array('or' => array(
                    0 => array('date' => true, 'from' => $todayDate),
                    1 => array('is' => new Zend_Db_Expr('null')))
                    ), 'left')
            ->addAttributeToSort('entity_id', 'desc')
            ->addAttributeToFilter('status', array('eq' => 1));
    return $collection;
    }
}