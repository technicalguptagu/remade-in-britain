<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_ImageGallery_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_IS_ENABLE           =   'imagegallery/settings/is_enable_gallery';
    const XML_PATH_IS_SHOW_PHOTO_TITLE           =   'imagegallery/settings/is_show_photo_title';
    const XML_PATH_IS_SHOW_PHOTO_DESCRIPTION           =   'imagegallery/settings/is_show_photo_description';
    const XML_PATH_DESCRIPTION_LENGTH           =   'imagegallery/settings/description_length';
    const XML_PATH_THUMB_SIZE           =   'imagegallery/settings/thumb_size';
    const XML_PATH_PHOTO_SIZE_IN_SLIDE_SHOW           =   'imagegallery/settings/photo_size_in_slide_show';
    const XML_PATH_PHOTO_PER_PAGE_DEFAULT           =   'imagegallery/settings/photo_per_page_default';
    const XML_PATH_PHOTO_PER_PAGE           =   'imagegallery/settings/photo_per_page';
    const XML_PATH_GALLERY_STYLE           =   'imagegallery/imagegallery_template/gallery_style';
    const XML_PATH_GALLERY_EFFECT           =   'imagegallery/imagegallery_template/gallery_effect';
    const XML_PATH_GALLERY_INTERVAL           =   'imagegallery/imagegallery_template/interval';
    const XML_PATH_IMAGE_IN_ROWS          =   'imagegallery/settings/image_in_row';
	const XML_PATH_MAX_ITEM_DISPLAY          =   'imagegallery/settings/max_item_display';
      

 public function maxItemDisplay($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_MAX_ITEM_DISPLAY, $storeId);
	   
    } 
 public function timeInterval($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_GALLERY_INTERVAL, $storeId);
	   
    } 
public function canShowGallery($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_IS_ENABLE, $storeId);
    }
    
  public function canShowTitle($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_IS_SHOW_PHOTO_TITLE, $storeId);
    }  
    
  public function canShowDescription($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_IS_SHOW_PHOTO_DESCRIPTION, $storeId);
    }  
    
    public function getDescriptionLength($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_DESCRIPTION_LENGTH, $storeId);
    } 
    
    public function getPhotoSizeInSlideShow($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
      
        $size =  explode('-',Mage::getStoreConfig(self::XML_PATH_PHOTO_SIZE_IN_SLIDE_SHOW, $storeId) );
       return array('width'=>$size[0], 'height'=> ($size[1])? $size[1]:$size[0] );
    } 
    
     public function getThumbSize($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       $size =  explode('-',Mage::getStoreConfig(self::XML_PATH_THUMB_SIZE, $storeId) );
        return array('width'=>$size[0], 'height'=> ($size[1])? $size[1]:$size[0] );
      
    } 
    
    public function getDefaultPhotoPerPage($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_PHOTO_PER_PAGE_DEFAULT, $storeId);
    } 
    
     public function getPhotoPerPage($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_PHOTO_PER_PAGE, $storeId);
    } 
     public function getGalleryStyle($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_GALLERY_STYLE, $storeId);
    } 
    
    
      public function getGalleryEffect($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_GALLERY_EFFECT, $storeId);
    }
       public function getImageInRows($storeId = null)
    {
       if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
       return Mage::getStoreConfig(self::XML_PATH_IMAGE_IN_ROWS, $storeId);
    }
}