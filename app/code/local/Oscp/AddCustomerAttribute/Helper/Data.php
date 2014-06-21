<?php
/**
 *
 * @package    Oscp_AddCustomerAttribute
 * @author     2jDesign Team
 * @Date       21/06/2014
 *
 * */
class Oscp_AddCustomerAttribute_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getRetailerStoreName($product_id)
    {
		$retailerId = Mage::getSingleton('catalog/product')->load($product_id)->getSellerId();
		$retailerStoreName = Mage::getBlockSingleton('Apptha_Marketplace_Block_Linkseller')->sellerdisplay($retailerId);
		return $retailerStoreName;
    }
	public function getRetailerStoreLink($product_id)
    {
		$retailerId = Mage::getSingleton('catalog/product')->load($product_id)->getSellerId();
		$show_profile = Mage::getBlockSingleton('Apptha_Marketplace_Block_Linkseller')->sellerprofiledisplay($retailerId);
        if ($show_profile == 1) {
			$target_path = 'marketplace/seller/displayseller/id/' . $retailerId;
			$mainUrlRewrite     = Mage::getModel('core/url_rewrite')->load($target_path, 'target_path');
			$getRequestPath     = $mainUrlRewrite->getRequestPath();
			$get_requestPath    = Mage::getUrl($getRequestPath);
			return $get_requestPath;
		}
    }
}