<?php
class Oscp_AddCustomerAttribute_Helper_Data extends Mage_Core_Helper_Abstract
{

	//Function to get retailer store name.
    public function getRetailerStoreName($retailer)
    {
		$retailerId = Mage::getModel('catalog/product')->load($retailer->getProduct()->getId())->getSellerId();
		$retailerStoreName = $retailer->getLayout()->getBlockSingleton('Apptha_Marketplace_Block_Linkseller')->sellerdisplay($retailerId);
		return $retailerStoreName;
    }
}
	 