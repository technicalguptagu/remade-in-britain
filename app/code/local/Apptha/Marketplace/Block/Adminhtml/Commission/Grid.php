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
class Apptha_Marketplace_Block_Adminhtml_Commission_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('commissionGrid');
      $this->setDefaultSort('entity_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }
 protected function _prepareCollection()
  {      
  $gid        = Mage::helper('marketplace')->getGroupId();
  $collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToSelect('email')
                ->addAttributeToSelect('created_at')
                ->addAttributeToSelect('group_id')
                ->addFieldToFilter('group_id',$gid);
  $this->setCollection($collection);
  return parent::_prepareCollection();
  }
  protected function _prepareColumns()
  {
      $this->addColumn('entity_id', array(
            'header'    => Mage::helper('marketplace')->__('Seller ID'),
            'width'     => '40px',
            'index'     => 'entity_id',
            'type'      => 'number',
      ));      
      $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'width'     => '200px',
            'index'     => 'name'
      ));
      $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '200px',
            'index'     => 'entity_id',
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordersellerdetails'
       ));        
       $this->addColumn('amount_received', array(
            'header'    => Mage::helper('sales')->__('Amount Received'),
            'align'     => 'right',
            'index'     => 'entity_id',
            'width'     => '150px',
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Amountreceived'
        ));
        $this->addColumn('amount_remaining', array(
            'header'    => Mage::helper('sales')->__('Amount Remaining'),
            'align'     => 'right',
            'index'     => 'entity_id',
            'width'     => '150px',
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Amountremaining'
        ));
        $this->addColumn('payment_mode', array(
            'header'    => Mage::helper('sales')->__('Payment Mode'),
            'align'     => 'left',
            'index'     => 'entity_id',            
            'filter'    => false,
            'width'     => '200px',
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Paymentmode'
        ));
        //Pay action
        $this->addColumn('action', array(
            'header'    => Mage::helper('marketplace')->__('Actions'),
            'align'     => 'center',
            'width'     => '100',
            'index'     => 'id',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Payment'
        ));
         $this->addColumn('payment_comment', array(
            'header'    => Mage::helper('sales')->__('Comments'),
            'align'     => 'right',
            'index'     => 'entity_id',            
            'filter'    => false,
             'width'    => '200px',
            'renderer'  => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Paymentcomment'
        ));
        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'filter'    => false,
            'sortable'  => false,
            'gmtoffset' => true
        ));
        return parent::_prepareColumns();
  }
  public function getRowUrl($row)
  {
     return  $this->getUrl('marketplaceadmin/adminhtml_paymentinfo/index/', array('id'=>$row->getId()));
  }
}
