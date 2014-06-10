<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_ImageGallery_Block_Imagegallery extends Mage_Core_Block_Template {

    protected $_collection = null;

    public function __construct() {
        parent::__construct();
        $this->_init();

        if (!$this->helper('imagegallery')->canShowGallery()) {
            return false;
        }

        if ($this->hasData('template')) {
            $this->setTemplate('imagegallery/imagegallery1.phtml');
        }

        $this->setCollection($this->_collection);
    }

    protected function _init() {
        if ($this->_collection == null) {
            $this->_collection = Mage::getModel('imagegallery/imagegallery')->getCollection()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('store_id', Mage::app()->getStore()->getId());

            $this->filterCollection();
        }
    }

    protected function filterCollection() {
        if ($this->helper('imagegallery')->getGalleryStyle() == 'style1') {
            $this->_collection->setRandStyle1();
            $this->setTemplate('imagegallery/imagegallery1.phtml');
        }

        if ($this->helper('imagegallery')->getGalleryStyle() == 'style2') {
            $this->_collection->setRandStyle2();
            $this->setTemplate('imagegallery/imagegallery2.phtml');
        }

        if ($this->helper('imagegallery')->getGalleryStyle() == 'style3') {
            $this->_collection->setRandStyle3();
            $this->setTemplate('imagegallery/imagegallery3.phtml');
        }
    }

}

