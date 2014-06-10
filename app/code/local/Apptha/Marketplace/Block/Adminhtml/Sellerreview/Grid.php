<?php
/*
 * ********************************************************* */
/**
 * @name          : Market Place
 * @version	  : 1.1
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
class Apptha_Marketplace_Block_Adminhtml_Sellerreview_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() {
        parent::__construct();        
        $this->setId('sellerreviewGrid');
        $this->setDefaultSort('seller_review_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection() {        
        $collection = Mage::getModel('marketplace/sellerreview')->getCollection();          
        $this->setCollection($collection);       
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        $this->addColumn('seller_review_id', array(
            'header'     => Mage::helper('customer')->__('Review ID'),
            'width'      => '40px',
            'index'      => 'seller_review_id',
        ));
        $this->addColumn('customer_at', array(
            'header'    => Mage::helper('customer')->__('Reviewed On'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));    
        $this->addColumn('review', array(
            'header'    => Mage::helper('customer')->__('Review'),
            'type'      => 'text',
            'align'     => 'left',
            'index'     => 'review',
            'width'     => '250px',            
        ));
        $this->addColumn('rating', array(
            'header'    => Mage::helper('customer')->__('Rating'),
            'type'      => 'text',
            'align'     => 'center',
            'index'     => 'rating', 
        ));
        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('customer')->__('Customer ID'),
            'type'      => 'text',
            'align'     => 'center',
            'index'     => 'customer_id',            
        ));
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('customer')->__('Product ID'),
            'type'      => 'text',
            'align'     => 'center',
            'index'     => 'product_id',            
        ));
        $this->addColumn('seller_id', array(
            'header'    => Mage::helper('customer')->__('Seller ID'),
            'type'      => 'text',
            'align'     => 'center',
            'index'     => 'seller_id',            
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('marketplace')->__('Status'),
            'width'     => '150px',
            'type'      => 'options',
            'index'     => 'status',
            'options'   => Mage::getSingleton('marketplace/status_reviewstatus')->getOptionArray()
        ));
        //Action to change the review status
        $this->addColumn('action', array(
            'header'    => Mage::helper('marketplace')->__('Action'),
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('marketplace')->__('Pending'),
                    'url'       => array('base' => '*/*/pending'),
                    'field'     => 'id'
                ),
                array(
                    'caption'   => Mage::helper('marketplace')->__('Approve'),
                    'url'       => array('base' => '*/*/approve'),
                    'field'     => 'id'
                ),
                array(
                    'caption'   => Mage::helper('marketplace')->__('Delete'),
                    'url'       => array('base' => '*/*/delete'),
                    'field'     => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false
        ));      
        return parent::_prepareColumns();
    }
    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('marketplace');        
        $this->getMassactionBlock()->addItem('disapprove', array(
            'label'     => Mage::helper('customer')->__('Pending'),
            'url'       => $this->getUrl('*/*/massPending')
        ));           
       $this->getMassactionBlock()->addItem('approve', array(
            'label'     => Mage::helper('marketplace')->__('Approve'),
            'url'       => $this->getUrl('*/*/massApprove')
        ));       
        $this->getMassactionBlock()->addItem('delete', array(
            'label'     => Mage::helper('marketplace')->__('Delete'),
            'url'       => $this->getUrl('*/*/massDelete'),
            'confirm'   => Mage::helper('marketplace')->__('Are you sure?')
        ));
        return $this;
    } 
    public function getRowUrl($row) {
        return false;
    }
  } 