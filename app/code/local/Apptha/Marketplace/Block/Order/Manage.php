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
class Apptha_Marketplace_Block_Order_Manage extends Mage_Core_Block_Template 
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
            $manage_collection = $this->getsellerOrders();            
            $this->setCollection($manage_collection);
            $pager = $this->getLayout()
                    ->createBlock('page/html_pager', 'my.pager')                   
                    ->setCollection($manage_collection);
            $pager->setAvailableLimit(array(10 => 10,20 => 20,30=>30,50=>50));
            $pager->setLimit(10);
            $this->setChild('pager', $pager);
        return $this;
    }
    //Function to get pagination
     public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }
    //Function to get seller order details
    public function getsellerOrders() {
        $data = $status = $select_filter = $from = $to = '';
        $data           = $this->getRequest()->getPost(); 
        if(isset($data['status'])){
            $status         = $data['status'];  
        }
        if(isset($data['select_filter'])){
            $select_filter  = $data['select_filter'];  
        }
        if (!empty($select_filter)) {
        switch ($select_filter) {
            case "today":
                // today interval
                $start_day      = strtotime("-1 today midnight");
                $end_day        = strtotime("-1 tomorrow midnight");
                $from           = date("Y-m-d", $start_day);
                $to             = date("Y-m-d", $end_day);
                $from_display   = date("Y-m-d", $start_day);
                $to_display     = date("Y-m-d", $start_day);
                break;
            case "yesterday":
                // yesterday interval
                $start_day      = strtotime("-1 yesterday midnight");
                $end_day        = strtotime("-1 today midnight");
                $from           = date("Y-m-d", $start_day);
                $to             = date("Y-m-d", $end_day);
                $from_display   = date("Y-m-d", $start_day);
                $to_display     = date("Y-m-d", $start_day);
                break;
            case "lastweek":
                // last week interval
                $to             = date('d-m-Y');
                $to_day         = date('l', strtotime($to));
                // if today is monday, take last monday
                if ($to_day == 'Monday'){
                    $start_day  = strtotime("-1 monday midnight");
                    $end_day    = strtotime("yesterday");
                } else {
                    $start_day  = strtotime("-2 monday midnight");
                    $end_day    = strtotime("-1 sunday midnight");
                }
                $from           = date("Y-m-d", $start_day);
                $to             = date("Y-m-d", $end_day);
                $to             = date('Y-m-d', strtotime($to . ' + 1 day'));
                $from_display   = $from;
                $to_display     = date("Y-m-d", $end_day);
                break;
            case "lastmonth":
                // last month interval
                $from           = date('Y-m-01', strtotime('last month'));
                $to             = date('Y-m-t', strtotime('last month'));
                $to             = date('Y-m-d', strtotime($to . ' + 1 day'));
                $from_display   = $from;
                $to_display     = date('Y-m-t', strtotime('last month'));
                break;
            case "custom":
                // last custom interval
                $from           = date('Y-m-d', strtotime($data['date_from']));
                $to             = date('Y-m-d', strtotime($data['date_to'] . ' + 1 day'));
                $from_display   = $from;
                $to_display     = date('Y-m-d', strtotime($data['date_to']));
                break;
        }
    }
      // Convert local date to magento db date.
      $db_from  = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
      $db_to    = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
         
        $orders = Mage::getModel('marketplace/commission')->getCollection();
                   $orders->addFieldToSelect('*');
                   $orders->addFieldToFilter('seller_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
                   if($status!=''){
                     $orders->addFieldToFilter('order_status', array('in' => array($status)));}                   
                     if($select_filter!=''){
                        $orders->addFieldToFilter('created_at', array('from' =>$db_from, 'to'=>$db_to));
                     }                  
                     $orders->setOrder('order_id', 'desc');                 
        return $orders;
    }  
} 