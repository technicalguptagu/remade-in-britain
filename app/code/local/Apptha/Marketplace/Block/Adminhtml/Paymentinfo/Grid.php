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
class Apptha_Marketplace_Block_Adminhtml_Paymentinfo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('paymentinfoGrid');
      $this->setDefaultSort('entity_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }
protected function _prepareCollection()
  {
    $seller_id  = $this->getRequest()->getParam('id');   
    $collection = Mage::getModel('marketplace/transaction')
                ->getCollection()
                ->addFieldToFilter('seller_id',$seller_id)
                ->addFieldToFilter('paid',1);
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }
  protected function _prepareColumns()
  {
         $this->addColumn('id', array(
            'header'    => Mage::helper('marketplace')->__('Transaction ID'),
            'width'     => '100px',
            'index'     => 'id',           
        ));        
         $this->addColumn('order_id', array(
            'header'    => Mage::helper('marketplace')->__('Order ID'),
            'width'     => '100px',
            'index'     => 'order_id',         
        ));
         $this->addColumn('seller_commission', array(
            'header'    => Mage::helper('marketplace')->__('Seller Amount'),
            'width'     => '100px',
            'index'     => 'seller_commission',            
        ));
         $this->addColumn('admin_commission', array(
            'header'    => Mage::helper('marketplace')->__('Admin Commission'),
            'width'     => '100px',
            'index'     => 'admin_commission',           
        ));
         $this->addColumn('paid_date', array(
            'header'    => Mage::helper('customer')->__('Paid On'),            
            'width'     => '100px',
            'align'     => 'center',
            'index'     => 'paid_date',            
            'gmtoffset' => true
        ));
        $this->addColumn('acknowledge_date', array(
            'header'    => Mage::helper('customer')->__('Acknowledge On'),            
            'align'     => 'center',
            'width'     => '100px',
            'index'     => 'acknowledge_date',            
            'gmtoffset' => true
        ));
         $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
         $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
         return parent::_prepareColumns();
  }
  public function getRowUrl($row)
  {
      return false;
  }
} 