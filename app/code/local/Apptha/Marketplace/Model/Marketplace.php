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
class Apptha_Marketplace_Model_Marketplace extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('marketplace/marketplace');
    }
    //Get Product Collection
    public function getProductCollection()
    {
       $model = Mage::getModel('catalog/product') ;//getting product model
       $collection = $model->getCollection();
       $collection->addAttributeToFilter('status', array('eq'=> 1));
       $collection->addAttributeToFilter('banner',array('neq'=>'no_selection'));
       if(Mage::getStoreConfig('marketplace/product/set_banner')==1){
            $collection->addAttributeToFilter('setbanner',array('eq'=>'1'));
       }
       return $collection;
    }
}