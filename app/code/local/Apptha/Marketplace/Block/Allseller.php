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
class Apptha_Marketplace_Block_Allseller extends Mage_Core_Block_Template
{
    //Function to get all seller collection
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $seller_collection = $this->getallSeller();            
        $this->setCollection($seller_collection);
        $pager = $this->getLayout()                
                ->createBlock('page/html_pager', 'my.pager')                   
                ->setCollection($seller_collection);           
        $this->setChild('pager', $pager);
        $pager->setAvailableLimit(array(10 => 10,20 => 20,30=>30,50=>50));
        $pager->setLimit(10);
        return $this;
    }
   //Function to get the Pagination
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    //Function to get all seller collection
   function getallSeller(){
       $tableName = Mage::getSingleton("core/resource")->getTableName('marketplace_sellerprofile');
       $model = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('customerstatus',1);       
       $model->getSelect()->join( array('t2'=>$tableName), 'e.entity_id = t2.seller_id', array('store_logo' => 't2.store_logo','store_title'=>'t2.store_title'));
       return $model;        
   } 
} 
