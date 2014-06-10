<?php

class Oscp_ProductStickers_Helper_Data extends Mage_Core_Helper_Abstract 
{
    
    public function getProductShortDescription($productId)
    {	
		return Mage::getModel('catalog/product')->load($productId);
    }


	public function getProductAttributeListing($_product)
    {	
		$product = Mage::getModel('catalog/product')->load($_product->getEntityId());
		return $product->getResource()->getAttribute('product_tag_grid')->getFrontend()->getValue($product);		
    }

	public function getProductAttributeGrid($_product)
    {	
		$product = Mage::getModel('catalog/product')->load($_product->getEntityId());
		return $product->getResource()->getAttribute('product_tag_listing')->getFrontend()->getValue($product);
		
    }
 
    public function getProductAttribute($_product)
    {	
		$product = Mage::getModel('catalog/product')->load($_product->getEntityId());
		return $product->getResource()->getAttribute('product_tag')->getFrontend()->getValue($product);		
    }
	/*
	@var $_imageUrl	
	return $this
	*/

	public function getProductTagImage()
    {		
		$_imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/tag/';	
		return $_imageUrl ;
	}


   public function getNotifyStock($stockQty){
      return Mage::getModel('cataloginventory/stock_item')->loadByProduct($stockQty)->getNotifyStockQty();
	
	}

	public function getNotifyStockQty($notifyStock){

    return (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($notifyStock)->getQty();
	
	}
   
}