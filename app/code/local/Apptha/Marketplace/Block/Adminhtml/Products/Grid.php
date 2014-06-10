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
 * @copyright     : Copyright (C) 2013 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 26,2014
 * */
/*
 * ********************************************************* */
class Apptha_Marketplace_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
     {
         parent::__construct();
         $this->setId('productsGrid');
         $this->setDefaultSort('entity_id');
         $this->setDefaultDir('DESC');
         $this->setSaveParametersInSession(true);
     }
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    protected function _prepareCollection()
    {
        $store      = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
                        ->addAttributeToSelect('sku')
                        ->addAttributeToSelect('name')
                         ->addAttributeToSelect('seller_id')    
                        ->addAttributeToSelect('attribute_set_id')
                        ->addAttributeToSelect('type_id');
        $getGroupId = Mage::helper('marketplace')->getGroupId();        
        $collection->addAttributeToFilter('group_id',array('eq' => $getGroupId));
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {            
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
        ));
        $store = $this->_getStore();
        if($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }
        $this->addColumn('type',
            array(
                'header'    => Mage::helper('catalog')->__('Type'),
                'width'     => '60px',
                'index'     => 'type_id',
                'type'      => 'options',
                'options'   => Mage::helper('marketplace')->getProductTypes(),
        ));
        $this->addColumn('seller_id', array(
            'header'    => Mage::helper('marketplace')->__('Seller Id'),
            'width'     => '80px',
            'index'     => 'seller_id',            
        ));
         $this->addColumn('sellerid', array(
            'header'    => Mage::helper('marketplace')->__('Seller Email'),
            'width'     => '150px',
            'index'     => 'seller_id',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordersellerdetails'           
        ));         
        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));        
        $this->addColumn('price',
            array(
                'header'        => Mage::helper('catalog')->__('Price'),
                'type'          => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index'         => 'price',
        ));
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'    => Mage::helper('catalog')->__('Qty'),
                    'width'     => '100px',
                    'type'      => 'number',
                    'index'     => 'qty',
            ));
        }   
        $this->addColumn('status',
            array(
                'header'    => Mage::helper('catalog')->__('Status'),
                'width'     => '70px',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'    => Mage::helper('catalog')->__('Websites'),
                    'width'     => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('View'),
                        'url'     => array(
                        'base'    =>'adminhtml/catalog_product/edit',
                        'params'  =>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));
        return parent::_prepareColumns();
    }
    protected function _prepareMassaction()
    {
      return $this;
    }
    public function getRowUrl($row)
    {
       return $this->getUrl('adminhtml/catalog_product/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );        
    }
} 