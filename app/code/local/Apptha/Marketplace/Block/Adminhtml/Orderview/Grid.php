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
class Apptha_Marketplace_Block_Adminhtml_Orderview_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() {
        parent::__construct();
        $this->setId('orderviewGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection() {
        $orders = Mage::getModel('marketplace/commission')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('order_status', array('eq' => 'complete'))
                ->addFieldToFilter('status', array('eq' => 1))
                ->setOrder('order_id', 'desc');
        $this->setCollection($orders);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('selleremail', array(
            'header'    => Mage::helper('sales')->__('Seller detail'),
            'width'     => '150px',
            'index'     => 'seller_id',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordersellerdetails'
        ));
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'width'     => '100px',
            'index'     => 'increment_id'
        ));
        $this->addColumn('productdetail', array(
            'header'    => Mage::helper('marketplace')->__('Product details'),
            'width'     => '150px',
            'index'     => 'id',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_OrderProductdetails'
        ));
        $this->addColumn('product_amt', array(
            'header'    => Mage::helper('sales')->__('Product Price'),
            'align'     => 'right',
            'index'     => 'product_amt',
            'width'     => '80px',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));
        $this->addColumn('seller_amount', array(
            'header'    => Mage::helper('sales')->__('Seller\'s Earned Amount'),
            'align'     => 'right',
            'width'     => '80px',
            'index'     => 'seller_amount',
            'type'      => 'currency',
            'currency'  => 'order_currency_code',
        ));

        $this->addColumn('commission_fee', array(
            'header'    => Mage::helper('sales')->__('Commission Fee'),
            'align'     => 'right',
            'index'     => 'commission_fee',
            'type'      => 'currency',
            'width'     => '80px',
            'currency'  => 'order_currency_code',
        ));
        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('marketplace')->__('Order At'),
            'align'     => 'center',
            'width'     => '200px',
            'index'     => 'order_id',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Orderdate'
        ));
        //Credit Action
        $this->addColumn('action', array(
            'header'    => Mage::helper('marketplace')->__('Actions'),
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'id',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordercredit'
        ));
        //Payment status
        $this->addColumn('payment_status', array(
            'header'    => Mage::helper('marketplace')->__('Ack Status'),
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'payment_status',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Receivedstatus'
        ));
        //Acknowledge Date
        $this->addColumn('acknowledge_date', array(
            'header'    => Mage::helper('marketplace')->__('Ack On'),
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'acknowledge_date',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Acknowledgedate'
        ));
        //View Action
        $this->addColumn('view', array(
            'header'    => Mage::helper('marketplace')->__('View'),
            'type'      => 'action',
            'getter'    => 'getOrderId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('marketplace')->__('View'),
                    'url'     => array('base' => 'adminhtml/sales_order/view/'),
                    'field'   => 'order_id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));
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