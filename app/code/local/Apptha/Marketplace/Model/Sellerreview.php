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
class Apptha_Marketplace_Model_Sellerreview extends Mage_Core_Model_Abstract 
{
    public function _construct() {
        parent::_construct();
        $this->_init('marketplace/sellerreview');
    }
    //Function to save a seller review
    function saveReview($data) {
       $need_admin   =  Mage::getStoreConfig('marketplace/seller_review/need_approval');
        if($data){
         $store_id   = Mage::app()->getStore()->getId();
         $collection = Mage::getModel('marketplace/sellerreview');
                        $collection->setSellerId($data['seller_id']);
                        $collection->setProductId($data['product_id']);
                        $collection->setCustomerId($data['customer_id']);
                        $collection->setRating($data['rating']);
                        $collection->setReview($data['feedback']);
                        $collection->setStoreId($store_id);
                        if($need_admin == 1){
                          $collection->setStatus(0);  
                        } else {
                          $collection->setStatus(1); 
                        }
                        $collection->save();
        $template_id    = (int) Mage::getStoreConfig('marketplace/seller_review/admin_notify_review');
        $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
        $toMailId       = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
        $toName         = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
        if ($template_id) {
            $emailTemplate = Mage::getModel('core/email_template')->load($template_id); 
        } else {
            $emailTemplate = Mage::getModel('core/email_template')
                            ->loadDefault('marketplace_seller_review_admin_notify_review');
        }
        $adminurl       = Mage::helper('adminhtml')->getUrl('marketplaceadmin/adminhtml_sellerreview/index');
        $customer       = Mage::getModel('customer/customer')->load($data['customer_id']);
        $cemail         = $customer->getEmail();
        $recipient      = $admin_email_id;
        $cname          = $customer->getName();
                            $emailTemplate->setSenderName(ucwords($cname));
                            $emailTemplate->setSenderEmail($cemail);
        $emailTemplateVariables = (array('ownername' => ucwords($toName), 'cname' => ucwords($cname), 'cemail' => $cemail, 'adminurl' => $adminurl));
                                    $emailTemplate->setDesignConfig(array('area' => 'frontend'));
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                             $emailTemplate->send($toMailId, ucwords($toName), $emailTemplateVariables);
        return true;
        } else {
            return false;
        }
    }
    //Function to check customer already review for this product or not 
    function checkReview($customer_id,$id,$product_id){
        $store_id   = Mage::app()->getStore()->getId();
        $coreResource = Mage::getSingleton('core/resource');
        $conn         = $coreResource->getConnection('core_read');
        $table        = $coreResource->getTableName('marketplace/sellerreview');
	$select       = $conn->select()
                        ->from(array('p' => $table ), new Zend_Db_Expr('seller_review_id'))
                        ->where('seller_id = ?',$id)
                        ->where('customer_id = ?', $customer_id)
                        ->where('product_id = ?', $product_id)
                        ->where('status = ?', 1)
                        ->where('store_id = ?', $store_id);
        $count = $conn->fetchOne($select);
        return $count;
    }
    //Function to display seller recent review
    function displayReview($id){
        $store_id   = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('marketplace/sellerreview')
                    ->getCollection()               
                    ->addFieldToFilter('status',1)
                     ->addFieldToFilter('store_id',$store_id)
                    ->addFieldToFilter('seller_id',$id)
                    ->setOrder('created_at','DESC')
                    ->setPageSize(5);
                    
         return $collection; 
    }
    //Function to get seller store name
    function getSellerInfo($id){
        $collection   = Mage::getModel('marketplace/sellerprofile')->getCollection()
                        ->addFieldToFilter('seller_id',$id);
        foreach($collection as $data){          
           return $data['store_title'];
        }  
    }
    //Function to get seller profile url of particular seller
    function backUrl($id){
        $seller_data = Mage::getModel('marketplace/sellerreview')->getSellerInfo($id);
        if ($seller_data) {
            $target_path        = 'marketplace/seller/displayseller/id/' . $id;
            $mainUrlRewrite     = Mage::getModel('core/url_rewrite')->load($target_path, 'target_path');
            $getRequestPath     = $mainUrlRewrite->getRequestPath();
            $get_requestPath    = Mage::getUrl($getRequestPath);
            return $get_requestPath;
        }         
    }    
} 
