<?php
/*
 * @category :  Oscp
 * @package  :  Oscp_ProductEnquiry
 */
class Oscp_ProductEnquiry_Model_Productenquiry extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productenquiry/productenquiry'); 	
    }
	
}