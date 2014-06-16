<?php
/*
 * @category Oscp
 * @package Oscp_Leftcategory
 */
/* BOF developer 16 6/14/2014 */

class Oscp_Leftcategory_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getCategoryId($categoryID) {
        $catagory = Mage::getModel('catalog/category')->load($categoryID);
        return $catagory;
    }

}
