<?php
/*
 * @category :  Oscp
 * @package  :  Oscp_ProductEnquiry
 */
class Oscp_ProductEnquiry_Block_Edit extends Mage_Core_Block_Template
{
   	const CACHE_TAG = 'product_enquiry';  

	
	public function __construct()
    {
        parent::__construct();
       
	   $this->addData(array(
            'cache_lifetime' =>86400,
            'cache_tags' => array(Oscp_ProductEnquiry_Block_Edit::CACHE_TAG,
                Mage_Core_Model_Store_Group::CACHE_TAG),
        ));
    }	
	public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    	
	public function getProductEnquiry()
    {
        if (!$this->hasData('productenquiry')) {
            $this->setData('productenquiry', Mage::registry('productenquiry_data'));
        }
        return $this->getData('productenquiry');
        
    }
    
   
   public function getCacheKeyInfo() {
        $cacheKeyInfo = array(
            'PRODUCT_ENQUIRY',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout()
        );
        return $cacheKeyInfo;
    }
    
}