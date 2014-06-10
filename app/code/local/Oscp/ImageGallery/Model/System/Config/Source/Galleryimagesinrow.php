<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_Imagegallery_Model_System_Config_Source_Galleryimagesinrow
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' =>'4', 'label'=>Mage::helper('adminhtml')->__('4')),
            array('value' =>'6', 'label'=>Mage::helper('adminhtml')->__('6')),
            array('value' =>'8', 'label'=>Mage::helper('adminhtml')->__('8')),
            array('value' =>'10', 'label'=>Mage::helper('adminhtml')->__('10'))
        );
    }


}