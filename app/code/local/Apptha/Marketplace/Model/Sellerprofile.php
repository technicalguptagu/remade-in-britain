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
class Apptha_Marketplace_Model_Sellerprofile extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('marketplace/sellerprofile');
    }
 
    //function to approve or disapprove seller
    function adminApproval($customer_id) {
        $template_id    = (int) Mage::getStoreConfig('marketplace/admin_approval_seller_registration/email_template_selection');
        $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
        $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
        $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
        if ($template_id) {
            $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
        } else {
            $emailTemplate = Mage::getModel('core/email_template')
                              ->loadDefault('marketplace_admin_approval_seller_registration_email_template_selection');
        }
        $adminurl               = Mage::helper('adminhtml')->getUrl('marketplaceadmin/adminhtml_manageseller/index');
        $customer               = Mage::getModel('customer/customer')->load($customer_id);
        $cemail                 = $customer->getEmail();
        $recipient              = $admin_email_id;
        $cname                  = $customer->getName();
                                $emailTemplate->setSenderName(ucwords($cname));
                                $emailTemplate->setSenderEmail($cemail);
        $emailTemplateVariables = (array('ownername' => ucwords($toName), 'cname' => ucwords($cname), 'cemail' => $cemail, 'adminurl' => $adminurl));
                                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
        $processedTemplate      = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                $emailTemplate->send($toMailId, ucwords($toName), $emailTemplateVariables);
    }
    //function to approve or disapprove seller
    function newSeller($customer_id) {         
        $template_id    = (int) Mage::getStoreConfig('marketplace/admin_approval_seller_registration/new_seller_template');
        $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
        $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
        $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
        if ($template_id) {
            $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
        } else {
            $emailTemplate = Mage::getModel('core/email_template')
                              ->loadDefault('marketplace_admin_approval_seller_registration_new_seller_template');
        }
        $adminurl               = Mage::helper('adminhtml')->getUrl('marketplaceadmin/adminhtml_manageseller/index');
        $customer               = Mage::getModel('customer/customer')->load($customer_id);
        $cemail                 = $customer->getEmail();
        $recipient              = $admin_email_id;
        $cname                  = $customer->getName();
                                $emailTemplate->setSenderName(ucwords($cname));
                                $emailTemplate->setSenderEmail($cemail);
        $emailTemplateVariables = (array('ownername' => ucwords($toName), 'cname' => ucwords($cname), 'cemail' => $cemail, 'adminurl' => $adminurl));
                                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
        $processedTemplate      = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                                $emailTemplate->send($toMailId, ucwords($toName), $emailTemplateVariables);
    }
    //Function to get seller profile info
    function collectprofile($id) {
        $collection = Mage::getModel('marketplace/sellerprofile')->load($id, 'seller_id');
        return $collection;
    }
    //Function to display new products
    function newproduct($sellerid) {       
        $storeId    = Mage::app()->getStore()->getId();
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId)
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter('seller_id', $sellerid)
                    ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
                    ->addAttributeToFilter('news_to_date', array('or' => array(
                            0 => array('date' => true, 'from' => $todayDate),
                            1 => array('is' => new Zend_Db_Expr('null')))
                            ), 'left')
                    ->addAttributeToSort('entity_id', 'DESC')
                    ->setPage(1, 5);
        return $collection;
    }
    //Function to display popular products
    function popularproduct($sellerid) {
        $_productCollection = Mage::getResourceModel('reports/product_collection')
                            ->addOrderedQty()
                            ->addFieldToFilter('seller_id', $sellerid)
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter('status', array('eq' => 1))
                            ->setPage(1, 5);
        return $_productCollection;
    }
    //Function for url management 
    function addRewriteUrl($store_name, $seller_id) {
        $trimStr            = trim(preg_replace('/[^a-z0-9-]+/', '-', strtolower($store_name)), '-');
        $mainUrlRewrite     = Mage::getModel('core/url_rewrite')->load($trimStr, 'request_path');
        $getUrlRewriteId    = $mainUrlRewrite->getUrlRewriteId();
        if ($getUrlRewriteId) {
            $requestPath = $trimStr . '-' . $seller_id;
        } else {
            $requestPath = $trimStr;
        }
        Mage::getModel('core/url_rewrite')
                ->setIsSystem(0)
                ->setIdPath('seller/' . $seller_id)
                ->setTargetPath('marketplace/seller/displayseller/id/' . $seller_id)
                ->setRequestPath($requestPath)
                ->save();
    }
    //Function to get seller product info
   function sellerProduct($id){
        $collection = Mage::getModel('marketplace/commission')->getCollection()               
                        ->addFieldToFilter('order_status','complete')
                        ->addFieldToFilter('seller_id',$id);              
         return $collection; 
    }
    //Function to get seller product order info
    function getdataProduct($order_ids){
       $items = Mage::getModel("sales/order_item")->getCollection()
               ->addFieldToSelect('product_id')
               ->addFieldToSelect('order_id')
               ->addFieldToSelect('name')
               ->addFieldToSelect('qty_invoiced')
               ->addFieldToSelect('base_price')
               ->addAttributeToSort('order_id', 'DESC')
                ->addFieldToFilter("increment_id", array("in" => $order_ids));      
       $items->getSelect()->join( array('t2'=> Mage::getConfig()->getTablePrefix().'sales_flat_order'), 'main_table.order_id = t2.entity_id', array('increment_id' => 't2.increment_id'));
       $items->getSelect()->limit(5);
	   
       return $items;
    }
    //Function to display top seller
    function topSeller($id){
        $collection = Mage::getModel('marketplace/sellerprofile')->getCollection()->addFieldToFilter('seller_id',array($id));                          
        return $collection;
    }
}