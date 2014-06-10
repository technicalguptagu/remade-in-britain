<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_Imagegallery_Model_System_Config_Source_Gallerystyleoptions
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' =>'style1', 'label'=>Mage::helper('adminhtml')->__('Style 1')),
            array('value' =>'style2', 'label'=>Mage::helper('adminhtml')->__('Style 2')),
            array('value' =>'style3', 'label'=>Mage::helper('adminhtml')->__('Style 3')),
           // array('value' =>'style4', 'label'=>Mage::helper('adminhtml')->__('Style 4'))
        );
    }


}