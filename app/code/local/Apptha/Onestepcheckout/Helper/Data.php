<?php
/**
 * @name         :  Apptha One Step Checkout
 * @version      :  1.7
 * @since        :  Magento 1.4
 * @author       :  Apptha - http://www.apptha.com
 * @copyright    :  Copyright (C) 2013 Powered by Apptha 
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  June 20 2011
 * @Modified By  :  Murali B
 * @Modified Date:  Feb 24 2014
 *
 * */
class Apptha_Onestepcheckout_Helper_Data extends Mage_Core_Helper_Abstract {
//get the Onestepcheckout activated settings and return the boolean

    public function isOnepageCheckoutLinksEnabled()
    {
        return Mage::getStoreConfig('onestepcheckout/general/Activate_apptha_onestepcheckout');
    }
    
	public function onestepApiKey() 
	{

		$code = $this->genenrateOscdomain(); 
		$domainKey = substr($code, 0, 25) . "CONTUS";
		return $domainKey;
	}

	public function domainKey($tkey) {

		$message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
		$lenkey = strlen($tkey);
		for ($i = 0; $i < $lenkey; $i++) {
			$key_array[] = $tkey[$i];
		}
		$enc_message = "";
		$kPos = 0;
		$chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
		$lenCharStr = strlen($chars_str);
		for ($i = 0; $i < $lenCharStr; $i++) {
			$chars_array[] = $chars_str[$i];
		}
		$messagelen = strlen($message);
		$countKeyArray = count($key_array);
		for ($i = 0; $i < $messagelen; $i++) {
			$char = substr($message, $i, 1);

			$offset = $this->getOffset($key_array[$kPos], $char);
			$enc_message .= $chars_array[$offset];
			$kPos++;
			if ($kPos >= $countKeyArray) {
				$kPos = 0;
			}
		}

		return $enc_message;
	}
	public function license()
	{
	 return 'license';
	}
	public function getOffset($start, $end) {

		$chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
		$charlenstr = strlen($chars_str);
		for ($i = 0; $i < $charlenstr; $i++) {
			$chars_array[] = $chars_str[$i];
		}
		$countCharsArr = count($chars_array);
		for ($i = $countCharsArr - 1; $i >= 0; $i--) {
			$lookupObj[ord($chars_array[$i])] = $i;
		}

		$sNum = $lookupObj[ord($start)];
		$eNum = $lookupObj[ord($end)];

		$offset = $eNum - $sNum;

		if ($offset < 0) {
			$offset = count($chars_array) + ($offset);
		}

		return $offset;
	}

	public function genenrateOscdomain() {
		 
		$strDomainName = Mage::app()->getFrontController()->getRequest()->getHttpHost();
		preg_match("/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
                preg_match("/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
		preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder[2], $matches);
		if (isset($matches['domain'])) {
			$customerurl = $matches['domain'];
		} else {
			$customerurl = "";
		}
		$customerurl = str_replace("www.", "", $customerurl);
		$customerurl = str_replace(".", "D", $customerurl);
		$customerurl = strtoupper($customerurl);
		if (isset($matches['domain'])) {
			$response = $this->domainKey($customerurl);
		} else {
			$response = "";
		}
		return $response;
	}

}