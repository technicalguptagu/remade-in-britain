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
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 26,2014
 * */
/*
 * ********************************************************* */
class Apptha_Marketplace_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() {
        parent::__construct();
        $this->setId('orderGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection() {
        $sellerId   = Mage::app()->getRequest()->getParam('id');
        $orders     = Mage::getModel('marketplace/commission')->getCollection()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('order_status', array('neq' => 'closed'))
                        ->addFieldToFilter('status', array('eq' => 1))
                        ->addFieldToFilter('seller_id', $sellerId)
                        ->setOrder('order_id', 'desc');
        $this->setCollection($orders);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {   
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'width'     => '100px',
            'index'     => 'increment_id'
        ));
        $this->addColumn('productdetail', array(
            'header'    => Mage::helper('marketplace')->__('Product details'),
            'width'     => '300px',
            'index'     => 'id',
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_OrderProductdetails'
        ));
        $this->addColumn('product_amt', array(
            'header'    => Mage::helper('sales')->__('Product Price'),
            'align'     => 'right',
            'index'     => 'product_amt',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));
        $this->addColumn('seller_amount', array(
            'header'    => Mage::helper('sales')->__('Seller\'s Earned Amount'),
            'align'     => 'right',
            'index'     => 'seller_amount',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));
        $this->addColumn('commission_fee', array(
            'header'    => Mage::helper('sales')->__('Commission Fee'),
            'align'     => 'right',
            'index'     => 'commission_fee',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));
        $this->addColumn('order_status', array(
            'header'    => Mage::helper('marketplace')->__('Status'),
            'align'     => 'center',
            'width'     => '80px',
            'index'     => 'order_status',
        ));
        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('marketplace')->__('Order At'),
            'align'     => 'center',
            'index'     => 'order_id',
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Orderdate'
        ));
        //Credit Action
        $this->addColumn('action', array(
            'header'    => Mage::helper('marketplace')->__('Actions'),
            'align'     => 'center',
            'width'     => '100',
            'index'     => 'id',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordercredit'
        ));
       //View order
        $this->addColumn('view', array(
            'header'    => Mage::helper('marketplace')->__('View'),
            'width'     => '80',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('marketplace')->__('View'),
                    'url'       => array('base' => 'adminhtml/sales_order/view/'),
                    'field'     => 'order_id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        )); 
        //Export data
        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }
    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('marketplace');
        $this->getMassactionBlock()->addItem('credit', array(
                'label' => Mage::helper('marketplace')->__('Credit'),
                'url'   => $this->getUrl('*/*/masscredit'),
        ));
        return $this;
    }
    public function getRowUrl($row) {
        return false;
    }
} 