<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Enterprise search collection resource model
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class EcommerceTeam_Sln_Model_Resource_Search_Collection
extends Enterprise_Search_Model_Resource_Collection
{

   /**
     * Set product visibility filter for enabled products
     *
     * @param   array $visibility
     * @return  Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setVisibility($visibility)
    {
        
        if (is_array($visibility)) {
            $this->addFqFilter(array('visibility' => $visibility));
        }

        $catId = Mage::app()->getRequest()->getParam('cat');
        if($catId){
           $catId_a = explode(',', trim($catId,',') ); 
            $this->addFqFilter(array('category_ids' => $catId_a ));
        }
        // ==========================================================================================================
        $request = Mage::getSingleton('ecommerceteam_sln/request');
        $filterableAttributes = $request->getFilterableAttributes();
        
         foreach ($filterableAttributes as $attribute){
            $attribute_code = $attribute->getAttributeCode();
            $value = Mage::app()->getRequest()->getParam($attribute_code);
            if (EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_INPUT == $attribute->getFrontendType()
                || EcommerceTeam_Sln_Model_Attribute::FRONTEND_TYPE_SLIDER == $attribute->getFrontendType()) {

                $start = (int) Mage::app()->getRequest()->getParam($attribute_code . '_from');
                $end   = (int) Mage::app()->getRequest()->getParam($attribute_code . '_to');
                if (!$start && !$end) {
                    $values = explode(',', Mage::app()->getRequest()->getParam($attribute_code));
                    $start = isset($values[0]) ? $values[0] : 0;
                    $end   = isset($values[1]) ? $values[1] : 0;
                }

                if ($start > 0 || $end > 0) {
                    $value = array(
                        'from' => $start,
                        'to'   => $end,
                    );
                    $this->addFqFilter(array($this->getattributeField($attribute_code) => $value ));                   
                }


            } elseif (null !== $value && '' !== $value) {
                if ($attribute->getFrontendInput() == 'price') {
                    $_value         = array();
                    $_values        = explode(',', $value);
                    $originalValues = $_values;
                    $length         = count($_values);

                    $i = 0;

                    while ($i < $length){
                        $_value[] = sprintf('%d,%d', $_values[$i], $_values[$i+1]);
                        $i += 2;
                    }
                     $this->addFqFilter(array($this->getattributeField($attribute_code) => $_value )); 
                    

                } else {
                    $values         = explode(',', $value);
                    $originalValues = $values;
                    foreach ($values as $key=>$value){
                        if($_value = $request->getFilterIdByKey($attribute_code, $value)){
                            $values[$key] = $_value;
                        }
                    }
                    
                    $this->addFqFilter(array($this->getattributeField($attribute_code) => $values));
        
                 //   $this->addFqFilter(array($attribute_code => $values ));   
                    //    addFqFilter(array($fieldName => $value));
                }
            }
         }
        
        // ==========================================================================================================
        
     //   echo "<pre>"; print_r($this->_searchQueryFilters); exit;
        
        return $this;
    }
    public function getattributeField($attribute_code)
    {
                    $_attribute =  Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute_code);
                    $filterModelName = 'enterprise_search/catalog_layer_filter_attribute';
                    $filter =  Mage::getModel($filterModelName);
                    $filter->setAttributeModel($_attribute);
                    $attribute =  $filter->getAttributeModel(); 
                    $fieldName = Mage::getResourceSingleton('enterprise_search/engine')
                                 ->getSearchEngineFieldName($attribute, 'nav');
                    
                    return $fieldName;
    }
 
}
