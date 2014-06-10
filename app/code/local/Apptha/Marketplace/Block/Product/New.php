<?php
/*
 * ********************************************************* */
/**
 * @name          : Market Place
 * @version	  : 1.2
 * @package       : apptha
 * @since         : Magento 1.5
 * @subpackage    : Market Place
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2014 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Add new product block file
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya 
 * @Modified Date : February 26,2014
 * */
/*
 * ********************************************************* */
class Apptha_Marketplace_Block_Product_New extends Mage_Core_Block_Template 
{
    // Initilize layout
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('marketplace')->__('New Product'));
        return parent::_prepareLayout();
    }
    // New product add action
    public function addProductAction() {
        return Mage::getUrl('marketplace/product/newpost');
    }
    // Getting website id
    public function getWebsiteId() {
        return Mage::app()->getStore(true)->getWebsite()->getId();
    }
    // Getting store id
    public function getStoreId() {
        return Mage::app()->getStore()->getId();
    }
   // Getting attributeset id
    public function getAttributeSetId() {
        return Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
    }
    // Getting store categories list
    public function show_categories_tree($categories) {
        $array      = '<ul class="category_ul">';
        foreach ($categories as $category) {
            $cat_id = $category->getId();
            $cat    = Mage::helper('marketplace/marketplace')->getCategoryData($cat_id); ;
            $count  = $cat->getProductCount();
            if ($category->hasChildren()) {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="end-plus"></span></a><span class="last-collapse"><input id="cat' . $category->getId() . '" type="checkbox" name="category_ids[]" value="' . $category->getId() . '"><label for="cat' . $category->getId() . '">' . $category->getName() . '<span>(' . $count . ')</span>' . '</label></span>';
            } else {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><input id="cat' . $category->getId() . '" type="checkbox" name="category_ids[]" value="' . $category->getId() . '"><label for="cat' . $category->getId() . '">' . $category->getName() . '<span>(' . $count . ')</span>' . '</label>';
            }
            if ($category->hasChildren()) {
                $children = Mage::getModel('catalog/category')->getCategories($category->getId());
                $array .= $this->show_categories_tree($children);
            }
            $array .= '</li>';
        }
        return $array . '</ul>';
    }
} 