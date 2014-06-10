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
class Apptha_Marketplace_Block_Adminhtml_Manageseller_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('managesellerGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection() {
        $gid        = Mage::helper('marketplace')->getGroupId();
        $collection = Mage::getResourceModel('customer/customer_collection')
                        ->addNameToSelect()
                        ->addAttributeToSelect('email')
                        ->addAttributeToSelect('created_at')
                        ->addAttributeToSelect('group_id')
                        ->addAttributeToSelect('customerstatus')
                        ->addFieldToFilter('group_id', $gid);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '40px',
            'index'     => 'entity_id',
        ));
        $this->addColumn('store_title', array(
            'header'    => Mage::helper('customer')->__('Store Name'),
            'width'     => '150px',
            'index'     => 'store_title',
            'filter'    => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Storetitle'
        ));
         $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'width'     => '200px',
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '160px',
            'index'     => 'email'
        ));
        $this->addColumn('contact', array(
            'header' => Mage::helper('customer')->__('Contact'),
            'width' => '150px',
            'index' => 'contact',
            'filter' => false,
            'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Contact'
        ));
        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'width'     => '150px',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));
        $this->addColumn('total_products', array(
            'header'    => Mage::helper('customer')->__('#Products'),
            'width'     => '100px',
            'index'     => 'total_products',
            'align'     => 'right',
            'filter'    => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Totalproducts'
        ));
        $this->addColumn('commission', array(
            'header'    => Mage::helper('customer')->__('Commission(%)'),
            'width'     => '70px',
            'index'     => 'commission',
            'align'     => 'right',
            'filter'    => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Commissionrate'
        )); 
        $this->addColumn('total_sales', array(
            'header'    => Mage::helper('customer')->__('Total Sales'),
            'width'     => '80px',
            'index'     => 'total_sales',
            'align'     => 'right',
            'filter'    => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Totalsales'
        ));        
        $this->addColumn('amount_received', array(
            'header'    => Mage::helper('sales')->__('Received'),
            'width'     => '80px',
            'align'     => 'right',
            'index'     => 'entity_id',
            'filter'    => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Amountreceived'
        ));
        $this->addColumn('amount_remaining', array(
            'header'    => Mage::helper('sales')->__('Remaining'),
            'width'     => '80px',
            'align'     => 'right',
            'index'     => 'entity_id',
            'filter'    => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Amountremaining'
        ));
        $this->addColumn('customerstatus', array(
            'header'    => Mage::helper('customer')->__('Status'),
            'width'     => '150px',
            'type'      => 'options',
            'index'     => 'customerstatus',
            'options'   => Mage::getSingleton('marketplace/status_status')->getOptionArray()
        ));
        $this->addColumn('action', array(
            'header'    => Mage::helper('marketplace')->__('Action'),
            'type'      => 'action',
            'width'     => '200px',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('marketplace')->__('Pending'),
                        'url'     => array('base' => '*/*/pending/'),
                        'field'   => 'id',
                        'title'   => Mage::helper('marketplace')->__('Pending')
                ),
                 array(
                    'caption' => Mage::helper('marketplace')->__('Approve'),
                        'url'     => array('base' => '*/*/approve/'),
                        'field'   => 'id',
                        'title'   => Mage::helper('marketplace')->__('Approve')
                ),
                    array(
                        'caption'   => Mage::helper('marketplace')->__('Disapprove'),
                        'url'       => array('base' => "*/*/disapprove"),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('marketplace')->__('Delete'),
                        'url'       => array('base' => "*/*/delete"),
                        'field'     => 'id',
                        'confirm'   => Mage::helper('marketplace')->__('Are you sure?')
                    )
            ),            
            'sortable' => false
        ));
        //set commission
        $this->addColumn('set_commission', array(
            'header'    => Mage::helper('marketplace')->__('Set Commission'),
            'width'     => '80',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('marketplace')->__('Commission'),
                        'url'     => array('base' => '*/*/setcommission/'),
                        'field'   => 'id',
                        'title'   => Mage::helper('marketplace')->__('Commission')
                ),
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        )); 
        //Edit Action
        $this->addColumn('order', array(
            'header'    => Mage::helper('marketplace')->__('Order'),
            'width'     => '80',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('marketplace')->__('Order'),
                    'url'       => array('base' => 'marketplaceadmin/adminhtml_order/index/'),
                    'field'     => 'id'
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
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('marketplace');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'     => Mage::helper('marketplace')->__('Delete'),
            'url'       => $this->getUrl('*/*/massDelete'),
            'confirm'   => Mage::helper('marketplace')->__('Are you sure?')
        )); 
        $this->getMassactionBlock()->addItem('approve', array(
            'label'     => Mage::helper('customer')->__('Approve'),
            'url'       => $this->getUrl('*/*/massApprove')
        ));
        $this->getMassactionBlock()->addItem('disapprove', array(
            'label'     => Mage::helper('customer')->__('Disapprove'),
            'url'       => $this->getUrl('*/*/massDisapprove')
        ));
        return $this;
    } 
    public function getRowUrl($row) {
        return $this->getUrl('adminhtml/customer/edit/', array('id'=>$row->getId()));
    }
} 