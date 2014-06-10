<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_Imagegallery_Model_System_Config_Source_Galleryeffectoptions
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'random', 'label'=>Mage::helper('adminhtml')->__('Random Effect')),
            array('value' => 'autoslide', 'label'=>Mage::helper('adminhtml')->__('Auto Slide')),
            array('value' => 'fadding-fadout', 'label'=>Mage::helper('adminhtml')->__('Fadding/Faddout')),
        );
    }


}