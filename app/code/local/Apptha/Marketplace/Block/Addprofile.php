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
class Apptha_Marketplace_Block_Addprofile extends Mage_Core_Block_Template
{
    //Function to get save profile url
    function addprofile(){     
        return  Mage::getUrl('marketplace/seller/saveprofile');
    }
    //Function to display the profile info for edit option
    function editprofile($seller_id){
        $collection   = Mage::getModel('marketplace/sellerprofile')->getCollection()
                        ->addFieldToFilter('seller_id',$seller_id);
        foreach($collection as $data){
           return $data;
        }    
    }
} 