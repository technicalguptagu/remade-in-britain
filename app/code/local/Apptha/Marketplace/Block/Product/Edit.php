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
 * @abstract      : Edit existing product block file
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 26,2014
 * */
/*
 * ********************************************************* */
class Apptha_Marketplace_Block_Product_Edit extends Mage_Core_Block_Template 
{
    // Initilize layout
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('marketplace')->__('Edit Product'));
        return parent::_prepareLayout();
    }
    // Product edit action
    public function editProductAction() {
        return Mage::getUrl('marketplace/product/editpost');
    }
    // Getting product data
    public function getProductData($product_id) {
        return Mage::getModel('catalog/product')->load($product_id);
    }
    // Getting store categories list
    public function getCategoriesTree($categories, $category_ids) {
        $array           = '<ul class="category_ul">';
        
        foreach ($categories as $category) {
            $cat_id      = $category->getId();
            $cat         = Mage::helper('marketplace/marketplace')->getCategoryData($cat_id);
            $count       = $cat->getProductCount();
            $cat_checked = '';
            if (in_array($category->getId(), $category_ids)) {
                $cat_checked = 'checked';
            }
            if ($category->hasChildren()){
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="end-plus"></span></a><span class="last-collapse"><input id="cat' . $category->getId() . '" type="checkbox" name="category_ids[]" ' . $cat_checked . ' value="' . $category->getId() . '"><label for="cat' . $category->getId() . '">' . $category->getName() . '<span>(' . $count . ')</span>' . '</label></span>';
            } else {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><input id="cat' . $category->getId() . '" type="checkbox" name="category_ids[]" ' . $cat_checked . ' value="' . $category->getId() . '"><label for="cat' . $category->getId() . '">' . $category->getName() . '<span>(' . $count . ')</span>' . '</label>';
            }
            if ($category->hasChildren()) {
                $children = Mage::getModel('catalog/category')->getCategories($category->getId());
                $array .= $this->getCategoriesTree($children, $category_ids);
            }
            $array .= '</li>';
        }
        return $array . '</ul>';
    }
    // Getting selected product categories id
    public function getCategoryIds($category_array) {
        foreach ($category_array as $key) {
            foreach ($key as $value) {
                $category_ids[] = $value;
            }
        }
        return $category_ids;
    }
} 