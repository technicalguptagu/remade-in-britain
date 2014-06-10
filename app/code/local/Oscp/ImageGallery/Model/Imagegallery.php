<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_ImageGallery_Model_Imagegallery extends Mage_Core_Model_Abstract {

    public function _construct() {

        parent::_construct();
        $this->_init('imagegallery/imagegallery');
    }

    public function setImage($field) {

        if ($image = $this->getUploaderInstance()->saveImages($field)->getImageName()) {
            return $image;
        }
        return false;
    }

    public function getUploaderInstance() {
        if (!$this->_uploaderInstance) {
            $this->_uploaderInstance = Mage::getModel('imagegallery/uploader');
        }
        return $this->_uploaderInstance;
    }

}