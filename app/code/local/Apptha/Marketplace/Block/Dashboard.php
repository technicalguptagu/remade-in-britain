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
 * @abstract      : Block File
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 26,2014
 * */
/*
 * ********************************************************* */
class Apptha_Marketplace_Block_Dashboard extends Mage_Core_Block_Template
{  
    //Function to get profile url
    function profileUrl()
    {     
        return  Mage::getUrl('marketplace/seller/addprofile');
    }
   //Function to get most viewed product information
   public function mostViewed(){
       $storeId    = Mage::app()->getStore()->getId();
       $products   = Mage::getResourceModel('reports/product_collection')
                    ->addOrderedQty()
                    ->addAttributeToSelect('*')           
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)
                    ->addViewsCount();
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products); 
        $products->setPageSize(5)->setCurPage(1);
        return $products;       
   }
   // Getting sales report collection
    public function advancedSalesReportCollection($db_from,$db_to,$id) { 
        $select_filter = '';
        $data           = $this->getRequest()->getPost();
        if(isset($data['select_filter'])){
            $select_filter  = $data['select_filter'];
        }
        if($select_filter=='today')
        {
          $from    = date("Y-m-d", strtotime("-1 days"));
          $to      = date("Y-m-d", strtotime("mid"));
          $db_from    = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
          $db_to      = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
        }
        $collection = Mage::getModel('marketplace/commission')->getCollection()
                        ->addFieldToFilter('order_status','complete')
                        ->addFieldToFilter('seller_id',$id)
                        ->addFieldToFilter('created_at', array('from' =>$db_from, 'to'=>$db_to));        
        return $collection;
    } 
} 



