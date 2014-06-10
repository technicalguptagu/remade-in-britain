<?php

/*
 * ********************************************************* */

/**
 * @name          : Apptha Paypal Adaptive
 * @version       : 1.0
 * @package       : Apptha
 * @since         : Magento 1.5
 * @subpackage    : Paypal Adaptive
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2013 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Helper File
 * @Creation Date : January 02,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 08,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Helper_Data extends Mage_Core_Helper_Abstract {

    // Getting Marketplace extenstion installed or not    
    public function getModuleInstalledStatus($moduleName) {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array) $modules;         
        if(isset($modulesArray[$moduleName])){
        if($moduleName == 'Apptha_Marketplace'){     
            return Mage::getStoreConfig('marketplace/marketplace/activate');    
        }          
        } else {
            return 0;    
        }
    }
    
     // Getting commission percent value
    public function getCommissionPercent() {
        return Mage::getStoreConfig('marketplace/marketplace/percentperproduct');
    }    

    // Getting payment description    
    public function getPaymentDescription() {
        return Mage::getStoreConfig('payment/paypaladaptive/description');
    }   
    
    // Getting refund enable or not
    public function getRefundStatus() {
        return Mage::getStoreConfig('payment/paypaladaptive/order_refund');
    }
    
    // Getting refund enable or not
    public function getPaymentMethod() {
        return Mage::getStoreConfig('payment/paypaladaptive/payment');
    }
    
    // Getting refund enable or not
    public function getFeePayer() {
        return Mage::getStoreConfig('payment/paypaladaptive/feepayer');
    }

    // Getting order status
    public function getOrderStatus() {
        return Mage::getStoreConfig('payment/paypaladaptive/order_status');
    }

    // Getting successful order status
    public function getOrderSuccessStatus() {
        return Mage::getStoreConfig('payment/paypaladaptive/order_success');
    }

    // Getting payment mode
    public function getPaymentMode() {
        return Mage::getStoreConfig('payment/paypaladaptive/sandbox');
    }

    // Getting API username
    public function getApiUserName() {
        return Mage::getStoreConfig('payment/paypaladaptive/paypal_api_username');
    }

    // Getting API password
    public function getApiPassword() {
        return Mage::getStoreConfig('payment/paypaladaptive/paypal_api_password');
    }

    // Getting API signature
    public function getApiSignature() {
        return Mage::getStoreConfig('payment/paypaladaptive/paypal_api_signature');
    }

    // Getting API Id
    public function getAppID() {
        return Mage::getStoreConfig('payment/paypaladaptive/paypal_app_id');
    }

    // Getting Grand Total
    public function getGrandTotal() {
        $session = Mage::getSingleton('checkout/session');
        $order = Mage::getModel('sales/order');
        return $order->loadByIncrementId($session->getLastRealOrderId())->getGrandTotal();
    }

    // Getting admin paypal id
    public function getAdminPaypalId() {
        return Mage::getStoreConfig('payment/paypaladaptive/merchant_paypal_mail');
    }

    // Calculating defualt seller share
    public function getSellerData() {

        // Getting last order data
        $session = Mage::getSingleton('checkout/session');
        $incrementId = $session->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($incrementId);
        $orderId = $order->getId();
        if (!empty($orderId)) {
            $items = $order->getAllItems();
            $sellerData = array();
            // Preparing seller share 
            foreach ($items as $item) {
                $sellerAmount = 0;
                $productId = $item->getProductId();
                $productData = Mage::getModel('paypaladaptive/productdetails')->getCollection()
                        ->addFieldToFilter('product_id', $productId);                    
                $firstRow = $this->getFirstRowData($productData);             
                if (!empty($firstRow['product_paypal_id']) && $firstRow['is_enable'] == 1) {
                    $sellerPaypalId = $firstRow['product_paypal_id'];
                    $commisionValue = $firstRow['share_value'];
                    $commissionMode = $firstRow['share_mode'];
                    Mage::getModel('paypaladaptive/save')->saveCommissionData($incrementId, $productId, $commisionValue, $commissionMode, $sellerPaypalId);
                    $productAmount = $item->getPrice() * $item->getQtyToInvoice();
                    if ($commissionMode == 'percent') {
                        $productCommission = $productAmount * ($commisionValue / 100);
                        $sellerAmount = $productAmount - $productCommission;
                    } else {
                        $productCommission = $commisionValue;
                        $sellerAmount = $productAmount - $commisionValue;
                    }                  
                    // Calculating seller share individually
                    if(array_key_exists($sellerPaypalId, $sellerData)) {
                        $sellerData[$sellerPaypalId]['amount'] = $sellerData[$sellerPaypalId]['amount'] + $sellerAmount;
                        $sellerData[$sellerPaypalId]['commission_fee'] = $sellerData[$sellerPaypalId]['commission_fee'] + $productCommission;
                    } else {
                        $sellerData[$sellerPaypalId]['amount'] = $sellerAmount;
                        $sellerData[$sellerPaypalId]['commission_fee'] = $productCommission;
                        $sellerData[$sellerPaypalId]['seller_id'] = $sellerPaypalId;
                    }
                }
            }
            return $sellerData;
        } else {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
            $this->_redirect('checkout/cart');
            return;
        }
    }
     // Calculating Marketplace seller share
    public function getMarketplaceSellerData(){   
       //Getting last order data
        $session = Mage::getSingleton('checkout/session');
        $incrementId = $session->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $orderId = $order->getId();
        if(!empty($orderId)){
            $items = $order->getAllItems();
            $sellerData = array();
            // Preparing seller share 
            foreach ($items as $item){
                $sellerAmount = 0;
                $productId = $item->getProductId();           
                $sellerProductData = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id',$productId)->addAttributeToSelect('*')->setPageSize(1);
                $product = $this->getFirstRowData($sellerProductData);  
                $marketplaceGroupId = Mage::helper('marketplace')->getGroupId();
                $productGroupId = $product->getGroupId();
                if($marketplaceGroupId == $productGroupId){                    
                    $sellerInfo = $this->getMarketplaceSellerPaypalId($product->getSellerId()); 
                    $sellerPaypalId = $sellerInfo['paypal_id'];
                    $sellerId = $product->getSellerId();
                    if(empty($sellerInfo)){
                        Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Please contact admin partner paypal id is required"));   
                       $url = Mage::getUrl('checkout/cart');
                       Mage::app()->getResponse()->setRedirect($url);
                        return;                    
                    }    
                    $productAmount = $item->getPrice() * $item->getQtyToInvoice();
                    $percentPerProduct = $sellerInfo['commission'];
                    $productCommission = $productAmount * ($percentPerProduct / 100);
                    $sellerAmount = $productAmount - $productCommission;                    
                    Mage::getModel('paypaladaptive/save')->saveCommissionData($incrementId, $productId, $percentPerProduct, 'percent', $sellerPaypalId, $sellerId);   
                    // Calculating seller share individually
                    if (array_key_exists($sellerPaypalId, $sellerData)){
                        $sellerData[$sellerId]['amount'] = $sellerData[$sellerPaypalId]['amount'] + $sellerAmount;
                        $sellerData[$sellerId]['commission_fee'] = $sellerData[$sellerPaypalId]['commission_fee'] + $productCommission;
                    } else {
                        $sellerData[$sellerId]['amount'] = $sellerAmount;
                        $sellerData[$sellerId]['commission_fee'] = $productCommission;
                        $sellerData[$sellerId]['seller_id'] = $sellerPaypalId;
                    }
                    
                }
            }
          return $sellerData;
            
        } else {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
            $url = Mage::getUrl('checkout/cart');
            Mage::app()->getResponse()->setRedirect($url);
            return;
        }     
    }  
    // Getting marketplace seller paypal id
    public function getMarketplaceSellerPaypalId($seller_id) {
       $collection   = Mage::getModel('marketplace/sellerprofile')->load($seller_id,'seller_id');                    
        return $collection;  
    }  
     // Getting first row data from collection 
     public function getFirstRowData($collections) {
        foreach($collections as $collection){
        return $collection;            
        }    
    }        
}