<?php
/*
 * @category :  Oscp
 * @package  :  Oscp_ProductEnquiry
 */

class Oscp_ProductEnquiry_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $productId = null;

	/*@ var return current product Id */
	public function getCurentProductId()
	{
		$this->productId = (int)Mage::app()->getRequest()->getParam('id');
		return $this->productId ;
	}

	/*@ var return product object Mage_Core_Catalog_Model_Product */
	public function getCurrentProduct()
	{
	    $product_id = $this->getCurentProductId();
		if(isset($product_id) && $product_id !=null && !empty($product_id)){
		    try {
		        $product = Mage::getModel('catalog/product')->load($product_id);
		    }catch (Exception $e) {
			    Mage::getSingleton('core/session')->addError(Mage::helper('productenquiry')->__('Undefined Index Product Id.'));
		    }
		}
		return $product ;
	}

	/*@ var return current product Url 
    public function getCurrentProductUrl()
	{
	    $product = $this->getCurrentProduct();
	    $product_url = $product->getProductUrl();
	    return $product_url ;
    }*/

	/*@ var return current product Name */
	public function getCurrentProductName()
	{
			$product = $this->getCurrentProduct();
			$product_name = $product->getName();
			return $product_name ;
	}
	public function getCurrentProductSku()
	{
			$product = $this->getCurrentProduct();
			$product_sku = $product->getSku();
			return $product_sku ;
	}
	/*@ var return  retailer's email-id */
    public function getRetailerEmailId()
    {
			$retailerId = $this->getCurrentProduct()->getSellerId();
			$retailerEmail = Mage::getModel('customer/customer')->load($retailerId)->getEmail();
			return $retailerEmail;
    }

}