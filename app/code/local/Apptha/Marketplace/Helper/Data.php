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
 * @abstract      : Helper File
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 26,2014
 * */
/*
 ***********************************************************/
class Apptha_Marketplace_Helper_Data extends Mage_Core_Helper_Abstract
{ 
    //Function to get seller group id
    public function getGroupId()
    {
      $cGroup = Mage::getModel('customer/group')->load('marketseller', 'customer_group_code');
      $result = $cGroup->getCustomerGroupId(); 
      return $result;
    }    
    // Getting selected prdouct types
    public function getSelectedPrdouctType()
    {
      return Mage::getStoreConfig('marketplace/product/producttype');
    }    
    // Getting product custom option configuration
    public function getPrdouctCustomOptions()
    {     
        return Mage::getStoreConfig('marketplace/product/productcustomoptions');
    }    
    // Getting product approval option configuration
    public function getProductApproval()
    {     
        return Mage::getStoreConfig('marketplace/product/productapproval');
    }    
    // Getting product types
    public function getProductTypes()
    {            
        $product_types = array();
        $product_types = array(
        "simple" => "Simple Product",
        "virtual" => "Virtual Product",
        "downloadable" => "Downloadable Product",
        );    
        return $product_types; 
    }        
    //License key
    public function checkMarketplaceKey() 
    {  
        $apikey = Mage::getStoreConfig('marketplace/marketplace/apply_apptha_licensekey');
        $marketplaceApiKey = $this->marketplaceApiKey();
        if ($apikey != $marketplaceApiKey) {           
           $keyerror = base64_decode('PGgzIGlkPSJ0aXRsZS10ZXh0IiBzdHlsZT0iZmxvYXQ6bGVmdDtjb2xvcjpyZWQ7bWFyZ2luOiAyNTBweCA1MTBweDsiPjxhIHN0eWxlPSJjb2xvcjpyZWQ7IiBocmVmPSJodHRwOi8vd3d3LmFwcHRoYS5jb20vY2hlY2tvdXQvY2FydC9hZGQvcHJvZHVjdC8xNTYiIHRhcmdldD0iX2JsYW5rIj5JbnZhbGlkIExpY2Vuc2UgS2V5IC0gQnV5IG5vdzwvYT48L2gzPg==');
            die($keyerror);
        }
    }
    //Function to get the license key
    public function marketplaceApiKey() 
    {
        $code      = $this->genenrateOscdomain(); 
        $domainKey = substr($code, 0, 25) . "CONTUS";
        return $domainKey;
    }
    //Function to get the domain key
    public function domainKey($tkey)
    {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $string_length = strlen($tkey);
        for($i = 0; $i < $string_length; $i++) {
          $key_array[] = $tkey[$i];
        }
        $enc_message = "";
        $kPos = 0;
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $str_len = strlen($chars_str);
        for($i = 0; $i < $str_len; $i++) {
          $chars_array[] = $chars_str[$i];
        }
        $len_message = strlen($message);
        $count = count($key_array);
        for($i = 0; $i < $len_message; $i++) {
                $char   = substr($message, $i, 1);
                $offset = $this->getOffset($key_array[$kPos], $char);
                $enc_message .= $chars_array[$offset];
                $kPos++;
               
                if ($kPos >= $count) {
                        $kPos = 0;
                }
        }
        return $enc_message;
    }
    //Function to get the offset for license key
    public function getOffset($start, $end)
    {
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $str_len = strlen($chars_str);
        for ($i = 0; $i < $str_len; $i++) {
           $chars_array[] = $chars_str[$i];
        }
        for ($i = count($chars_array) - 1; $i >= 0; $i--) {
           $lookupObj[ord($chars_array[$i])] = $i;
        }
        $sNum   = $lookupObj[ord($start)];
        $eNum   = $lookupObj[ord($end)];
        $offset = $eNum - $sNum;
        if ($offset < 0) {
                $offset = count($chars_array) + ($offset);
        }
        return $offset;
    }
    //Function to generate license key
    public function genenrateOscdomain()
    {	
        $subfolder = $matches = '';
        $strDomainName = Mage::app()->getFrontController()->getRequest()->getHttpHost();
        preg_match("/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
        preg_match("/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
        preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder[2], $matches);
        if (isset($matches['domain']))
        {
           $customerurl = $matches['domain'];
        } else {
           $customerurl = "";
        }
        $customerurl = str_replace("www.", "", $customerurl);
        $customerurl = str_replace(".", "D", $customerurl);
        $customerurl = strtoupper($customerurl);
        if (isset($matches['domain']))
        {
           $response = $this->domainKey($customerurl);
        } else {
           $response = "";
        }
        return $response;
    }  

	// to retun target path as per magento
	
	Public function getTargetPlace($targetPath){
	    return Mage::getModel('core/url_rewrite')->load($targetPath, 'target_path');
	
	}
} 
	 