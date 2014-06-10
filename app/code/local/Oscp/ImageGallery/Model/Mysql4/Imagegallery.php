<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_ImageGallery_Model_Mysql4_Imagegallery extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
       
        // Note that the imagegallery_id refers to the key field in your database table.
        $this->_init('imagegallery/imagegallery', 'imagegallery_id');
    }
}