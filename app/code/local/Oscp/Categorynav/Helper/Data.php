<?php
/**
 * Catalog navigation Categorynav
 *
 * @category   Osc
 * @package    Osc_Categorynav * 
 */
?>
<?php
class Oscp_Categorynav_Helper_Data extends Mage_Core_Helper_Abstract {	
	/*
	@var $_imageUrl
	return array
	CurrentChildCategories images
	*/
	public function getCateImageUrl($_currentChild)
        {	
                    $_categories = $_currentChild; //$this->getCurrentChildCategories();
                    $images = array();				
                    foreach($_categories as $_category){
                        $_imageUrl = null;
                        $_thumbUrl = null;
                        $_category = Mage::getModel('catalog/category')->load($_category->getEntityId());

                        if($_category->getImage()){
                            $_imageUrl = Mage::getBaseUrl('media').'catalog/category/'.$_category->getImage();
                        }
                        if($_category->getThumbnail()){
                                    $_thumbUrl = Mage::getBaseUrl('media').'catalog/category/'.$_category->getThumbnail();
                            }
                            $images[$_category->getId()] = array('image' => $_imageUrl,
                                                                'thumb' => $_thumbUrl
                                ); 
                    }

                    return $images;		
        }


	/********* deve(3) *****************/
   public function getCatedata()
    {	
	$_category  = $this->getCurrentCategory();
	$cateid = $_category->getId();
	// echo 'prak'.$cateid;
	$category  =  Mage::getModel('catalog/category')->load($this->getData($cateid));

	return $category;
	}

  public function getImageUrl($_category) 
     { 
         return Mage::getModel('catalog/category')->load($_category->getId())->getImageUrl(); 
     }     
 
  public function getThumbnailUrl($_category) 
     { 
         return Mage::getModel('catalog/category')->load($_category->getId())->getThumbnailUrl(); 
     }

	  public function getCurrentCategory() 
     { 
        $_layer = Mage::getSingleton('catalog/layer');
        $_category = $_layer->getCurrentCategory();
   
	    return $_category;
     }
	  
	 /********* deve(3) *****************/
     // BOF function by developer7
      public function showCategoryBlock($_category) 
     { 
         
         $category =  Mage::getModel('catalog/category')->load($_category->getId()); 			 
		 $prodCollection = $category->getProductCollection();
		 $product_count= $prodCollection->count();          
         
         if ($product_count>0){             
             foreach($prodCollection as $product){  
               
               if('simple'==$product->getTypeId()){                  
   				    $manageStock = $product->getStockItem()->getIsInStock();
                    $qtyStock = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                    //if( $qtyStock>0 ){ return true; }
                     if( $manageStock==1 ){ return true; }

                }
               else{  
                    $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
                    $col = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
                    foreach($col as $simple_product){  
					    $manageStock = $product->getStockItem()->getIsInStock();
                        $qtyStock = (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($simple_product)->getQty();
                       if( $manageStock==1 ){ return true; }
						//if( $qtyStock>0 ){ return true; }
                    }                
               }               
              
             }          
         }         
       
       return false;
     }
     // EOF function by developer7
}