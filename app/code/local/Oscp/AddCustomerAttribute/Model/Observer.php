<?php

/**

 * @category   Oscp
 * @package    Oscp_AddCustomerAttribute
 */

 /* developer 06 'Observer to store custom field's value in the database.  2014/06/13' */

class Oscp_AddCustomerAttribute_Model_Observer {

    public function customerRegistered($observer)
	{		
		$uploadsData = new Zend_File_Transfer_Adapter_Http();
		$filesDataArray = $uploadsData->getFileInfo();						
		$store_logo  = $filesDataArray['example_image']['name'];
		$model = Mage::getModel('marketplace/sellerprofile');
		$customer = $observer->getEvent()->getCustomer();
		$request = Mage::app()->getRequest();
		$model->setSellerId($customer->getEntityId());
		$model->setStoreTitle($request->getParam('store_trading_name'));
		$model->setContact($request->getParam('telephone'));
		$model->setExistWebsite($request->getParam('seller_existing_website'));
		$model->setAdditionalInfo($request->getParam('additional'));
		$model->setStoreLogo($store_logo);
		$model->save();
		return $this;
    }
}

