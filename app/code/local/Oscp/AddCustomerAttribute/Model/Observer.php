<?php

/**

 * @category   Oscp
 * @package    Oscp_AddCustomerAttribute
 */

 /* OSCDEV #42 Observer to store custom fields value in the databasa.  2014/06/13 */

class Oscp_AddCustomerAttribute_Model_Observer {

    public function customerRegistered($observer)
	{
        $customer = $observer->getEvent()->getCustomer();
		$request = Mage::app()->getRequest();
		$_store_trading_name =  $request->getParam('store_trading_name');
		$_seller_existing_website =  $request->getParam('seller_existing_website');	
		$_seller_example_image =  $request->getParam('seller_example_image');	
		$_seller_additional_info =  $request->getParam('seller_additional_info');	

		$customer->setStoreTradingName($_store_trading_name)
		         ->setSellerExistingWebsite($_seller_existing_website)
				 ->setSellerExampleImage($_seller_example_image)
				 ->setSellerAdditionalInfo($_seller_additional_info);

		$customer->save();
        return $this;
    }
}

/* EOF OSCDEV #42 */