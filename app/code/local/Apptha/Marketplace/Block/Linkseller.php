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
 * @copyright     : Copyright (C) 2013 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Block File
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 26,2014
 * */
/*
 ***********************************************************/
class Apptha_Marketplace_Block_Linkseller extends Mage_Core_Block_Template
{
    //Function to get the seller profile data
    function sellerdisplay($seller_id)
    {
       $collection   = Mage::getModel('marketplace/sellerprofile')->load($seller_id,'seller_id');
       $Store_title  = $collection->getStoreTitle();
       return  $Store_title;               
    }
    //Function to get show profile information
    function sellerprofiledisplay($seller_id)
    {
       $collection    = Mage::getModel('marketplace/sellerprofile')->load($seller_id,'seller_id');
       $Store_profile = $collection->getShowProfile();
       return  $Store_profile;               
    }   
}

