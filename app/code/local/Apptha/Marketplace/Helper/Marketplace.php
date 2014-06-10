<?php
/*
 ***********************************************************/
/**
 * @name          : Market Place
 * @version	  : 1.2
 * @package       : apptha
 * @since         : Magento 1.5
 * @subpackage    : Market Place
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2014 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Helper File
 * @Creation Date : October 23,2013
 * @Modified By   : Jenifer Ratchanya
 * @Modified Date : February 27,2014
 * */
/*
 ***********************************************************/
class Apptha_Marketplace_Helper_Marketplace extends Mage_Core_Helper_Abstract 
{
    //Function to get new product url
    public function getNewProductUrl() {
        return Mage::getUrl('marketplace/product/new');
    }
    //Functionto get the manage product url
    public function getManageProductUrl() {
        return Mage::getUrl('marketplace/product/manage');
    }
    //Function to get the manage order url
    public function getManageOrderUrl() {
        return Mage::getUrl('marketplace/order/manage');
    }
    //Function to get the add profile url
    public function addprofileUrl() {
        return Mage::getUrl('marketplace/seller/addprofile');
    }
    //Function to get the become a merchant url
    public function becomemerchantUrl() {
        return Mage::getUrl('marketplace/seller/changebuyer');
    }
    //Function to get link profile url 
    public function linkprofileUrl($seller_id) {
        return Mage::getUrl('marketplace/seller/displayseller', array('id' => $seller_id));
    }
    //Function to get link product url
    public function linkproductUrl($seller_id) {
        return Mage::getUrl('marketplace/seller/sellerproduct', array('id' => $seller_id));
    }
    //Function to get seller registration url
    public function getregisterUrl(){
        return Mage::getUrl('marketplace/seller/create');
    }
    //Function to get seller registration url and login url
    public function getregister(){
        return Mage::getUrl('marketplace/seller/login');
    }
    //Function to get the dashboard url
    public function dashboardUrl(){
        return Mage::getUrl('marketplace/seller/dashboard');
    }
    //Function to get all seller information
    public function getviewallsellerUrl(){
        return Mage::getUrl('marketplace/seller/allseller');
    }
    //Function to get view order url
    public function getVieworder($getOrderId){
        return Mage::getUrl('marketplace/order/vieworder',array('orderid'=>$getOrderId));
    }
    //Function to get view transaction url
    public function getViewtransaction(){
        return Mage::getUrl('marketplace/order/viewtransaction');
    }
    //Function to get the received amount of seller
    public function getAmountReceived() {
        $return         = '';
        $sellerId       = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $_collection    = Mage::getModel('marketplace/transaction')->getCollection()
                            ->addFieldToSelect('seller_commission')
                            ->addFieldToFilter('seller_id', $sellerId)
                            ->addFieldToFilter('paid', 1);
        $_collection->getSelect()
                ->columns('SUM(	seller_commission) AS seller_commission')
                ->group('seller_id');
        foreach ($_collection as $amount) {
            $return = $amount->getSellerCommission();
        }
        return Mage::helper('core')->currency($return, true, false);
    }
    //Function to get the remaining amount of seller
    public function getAmountRemaining() {
        $return         = '';
        $sellerId       = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $_collection    = Mage::getModel('marketplace/transaction')->getCollection()
                            ->addFieldToSelect('seller_commission')
                            ->addFieldToFilter('seller_id', $sellerId)
                            ->addFieldToFilter('paid', 0);
        $_collection->getSelect()
                ->columns('SUM(	seller_commission) AS seller_commission')
                ->group('seller_id');
        foreach ($_collection as $amount) {
            $return = $amount->getSellerCommission();
        }
        return Mage::helper('core')->currency($return, true, false);
    }
    //Function to get customer review url
    public function customerreviewUrl() {
        return Mage::getUrl('marketplace/seller/customerreview');
    }
    //Function to get all review data
    function getallreviewdata($id){
       $store_id   = Mage::app()->getStore()->getId();
       $collection = Mage::getModel('marketplace/sellerreview')
                    ->getCollection()               
                    ->addFieldToFilter('status',1)
                     ->addFieldToFilter('store_id',$store_id)
                    ->addFieldToFilter('seller_id',$id);
         return $collection; 
   } 
   //Function to get order collection
   function allowReview($customer_id){
       $orders = Mage::getResourceModel('sales/order_collection')
                      ->addFieldToSelect('*')
                      ->addFieldToFilter('customer_id',$customer_id)
                      ->addAttributeToSort('created_at', 'DESC')
                      ->setPageSize(5);
          return $orders;
     }
    //Function to get contact form url
    public function getContactFormUrl() {
        return Mage::getUrl('marketplace/contact/form');
    }
    public function getSellerRewriteUrl($seller_id){
        $target_path        = 'marketplace/seller/displayseller/id/'.$seller_id;
        $mainUrlRewrite     = Mage::getModel('core/url_rewrite')->load($target_path, 'target_path');
        $getRequestPath     = $mainUrlRewrite->getRequestPath();
        $get_requestPath    = Mage::getUrl($getRequestPath);
        return $get_requestPath;
    }
    //Function to load particular seller information
    public function getSellerCollection($_id){
       $collection = Mage::getModel('marketplace/sellerprofile')->load($_id,'seller_id');
       return $collection;
    }
    //Function to load particular category information
    public function getCategoryData($cat_id){
        $collection = Mage::getModel('catalog/category')->load($cat_id);
        return $collection;
    }
    //Function to delete product
    public function deleteProduct($_entity_ids){
       Mage::getModel('catalog/product')->setId($_entity_ids)->delete();
       return true;
    }
    //Function to get product collection
    public function getProductInfo($getProductId){
        $collection = Mage::getModel('catalog/product')->load($getProductId);
        return $collection;
    }
    //Function to get Commission data
    public function getCommissionInfo($commission_id){
       $collection = Mage::getModel('marketplace/commission')->load($commission_id,'id');
       return $collection;
    }
    //Function to get Transaction data
    public function getTransactionInfo($commission_id){
        $collection = Mage::getModel('marketplace/transaction')->load($commission_id,'commission_id');
        return $collection;
    }
    //Function to save transaction data
    public function saveTransactionData($Data){
        Mage::getModel('marketplace/transaction')->setData($Data)->save();
        return true;
    }
    //Function to save transaction data
    public function updateTransactionData($transaction_id){
        $now  = Mage::getModel('core/date')->date('Y-m-d H:i:s', time());
        if (!empty($transaction_id)) {
            Mage::getModel('marketplace/transaction')
                    ->setPaid(1)                                            
                    ->setPaidDate($now)
                     ->setComment('Paypal Adaptive Payment')
                    ->setId($transaction_id)->save();
        }      
    }
    //Function to update commission data
    public function updateCommissionData($status_order,$commission_id){
        Mage::getModel('marketplace/commission')->setOrderStatus($status_order)->setId($commission_id)->save();
        return true;        
    }
    //Function to save commission data
    public function saveCommissionData($Data,$commission_id){
         Mage::getModel('marketplace/commission')->setData($Data)->setId($commission_id)->save();
         return true; 
    }
    //Function to load email template 
    public function loadEmailTemplate($template_id){
        $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
        return $emailTemplate;
    }
    //Function to load customer data
    public function loadCustomerData($seller_id){
        $customer = Mage::getModel('customer/customer')->load($seller_id);
        return $customer;
    }
    //Function to update comment from admin
    public function updateComment($comment,$transaction_id){
         $now            = Mage::getModel('core/date')->date('Y-m-d H:i:s', time());
         if (!empty($transaction_id)) {
            Mage::getModel('marketplace/transaction')
               ->setPaid(1)                                            
               ->setPaidDate($now)
               ->setComment($comment)
               ->setId($transaction_id)->save();
         return true;
        }
    }
    //Function to credit amount to seller
    public function updateCredit($commission_id){
        $collection = Mage::getModel('marketplace/commission')->load($commission_id,'id');
                      $collection->setCredited('1')->save();
        return $collection;
    }
    //Function to delete a seller review
    public function deleteReview($marketplaceId){
        $model = Mage::getModel('marketplace/sellerreview');
                 $model->setId($marketplaceId)->delete();
        return true;
    }
    //Function to approve review
    public function approveReview($marketplaceId){
        $model =  Mage::getModel('marketplace/sellerreview')->load($marketplaceId);
                  $model->setStatus('1')->save();
        return $model;
    }
    //Function to delete a seller account
    public function deleteSeller($marketplaceId){
        $marketplace = Mage::getModel('customer/customer')->load($marketplaceId);
                        $marketplace->delete();
        return true;
    }
    //Function to update approve seller status
    public function approveSellerStatus($marketplaceId){
         $model = Mage::getModel('customer/customer')->load($marketplaceId);
                        $model->setCustomerstatus('1')
                        ->save();
         return true;
    }
    //Function to update disapprove seller status
    public function disapproveSellerStatus($marketplaceId){
         $model = Mage::getModel('customer/customer')->load($marketplaceId);
                 $model->setCustomerstatus('2')
                        ->save();
         return true;
    }
    //Function to upload product image
        public function uploadImage($filename,$filesDataArray){
            $images_path = array();
            $uploader = new Varien_File_Uploader($filename);
                            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                            $uploader->addValidateCallback('catalog_product_image',
                            Mage::helper('catalog/image'), 'validateUploadFile');
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(false);
                            $path = Mage::getBaseDir('media') . DS . 'tmp' . DS . 'catalog' . DS . 'product' . DS;
                            $uploader->save($path, $filesDataArray[$filename]['name']);
                            $images_path = $path . $uploader->getUploadedFileName();
                           
             return $images_path;
        }
        //Function to save image in media gallery
        public function mediaGallery($product){            
            $product->save();
            return true;
        }
        
        //Function to disallow php files
        public function disAllowUpload($uploader,$tmpPath){       
            $result = $uploader->save($tmpPath);
            return $result;
        }
        //Function to delete existing product custom option
        public function deleteCustomOption($opt){
            $opt->delete();
            return true;
        }
        //Function to storing downloadable product link data
        public function saveDownLoadLink($linkModel){
           $linkModel->save();
           return true;
        } 
        //Function to initialize product instock
        public function productInStock($is_in_stock){
            if (isset($is_in_stock)) {
               return $stock_data['is_in_stock'] = $is_in_stock;
            } else {
                return $stock_data['is_in_stock'] = 1;
            }
        }
       
}