<?php
    /**
    * EcommerceTeam.com
    *
    * Seo Layered Navigation
    *
    * @category     Magento Extension
    * @copyright    Copyright (c) 2011 Ecommerce Team (http://www.ecommerce-team.com)
    * @author       Ecommerce Team
    * @version      3.0
    */

class EcommerceTeam_Sln_Model_Resource_Eav_Mysql4_Product_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->reset(Zend_Db_Select::GROUP);
        return $select;
    }
    
     public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
       $parentCollection = parent::addAttributeToSort($attribute, $dir);
       
      $selectOrders = $parentCollection->getSelect()->getPart(Zend_Db_Select::ORDER);
      $parentCollection->getSelect()->reset(Zend_Db_Select::ORDER);
        foreach ($selectOrders as $term) {
            if($term[0]!='cat_index_position'){
            $parentCollection->getSelect()->order(sprintf('%s %s',$term[0], $term[1]) );
            }
            else{
                $parentCollection->getSelect()->order('cat_index.position ' . $term[1]);
            }
        }      
         
        return $parentCollection; 
    } 
    
}
