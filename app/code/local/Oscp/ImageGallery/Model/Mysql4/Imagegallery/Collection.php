<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_ImageGallery_Model_Mysql4_Imagegallery_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imagegallery/imagegallery');
    }
    
    public function setRandStyle1()
    {
        $this->getSelect()
           ->order('RAND() ')
           ->limit(6); 
    }


	public function setRandStyle2()
    {
        $this->getSelect()
           ->order('RAND() ')
           ->limit(3); 
    }

	public function setRandStyle3()
    {
        $this->getSelect()
           ->order('sort_order');
    }
 
}