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
 * @abstract      : Observer File
 * @Creation Date : January 02,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 25,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_Observer {

    // Creditmemo(Refund process)
    public function adaptiveRefundAction(Varien_Event_Observer $observer) {

        $creditmemo = $observer->getEvent()->getCreditmemo();
        $orderId = $creditmemo->getOrderId();

        $order = Mage::getModel('sales/order')->load($creditmemo->getOrderId());
        $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();

        $incrementId = $order->getIncrementId();
        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('seller_invoice_id', $incrementId);

        // Getting refund process enabled or not
        $offlineRefundStatus = Mage::helper('paypaladaptive')->getRefundStatus();     
        
        // If not using adaptive payment method return ture to refund process
        if ($paymentMethodCode != 'paypaladaptive' && count($collections) < 1 || $offlineRefundStatus != 1) {
            return;
        }

        $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($collections);

        $adminEmail = $firstRow['owner_paypal_id'];
        $payKey = $firstRow['pay_key'];
        $trackingId = $firstRow['tracking_id'];
        $transactionId = $firstRow['seller_transaction_id'];
        $currencyCode = $firstRow['currency_code'];


        $items = $order->getAllItems();
        $newItems = $creditmemo->getAllItems();

        $sellerData = Mage::getModel('paypaladaptive/save')->sellerDataForRefund($items, $incrementId, 1);
        $newSellerData = Mage::getModel('paypaladaptive/save')->sellerDataForRefund($newItems, $incrementId, 0);

        $receiverAmountArray = $receiverEmailArray = array();
        $adminTotalCommission = 0;
        foreach ($sellerData as $data) {

            $sellerId = $data['seller_id'];
            $receiverAmount = $adminCommission = 0;
            if ($data['amount'] == $newSellerData[$sellerId]['amount']) {
                $receiverAmount = $data['amount'];
                $adminCommission = $data['commission_fee'];
            } else {
                if (!empty($newSellerData[$sellerId]['amount'])) {
                    $receiverAmount = $data['amount'] - $newSellerData[$sellerId]['amount'];
                    $adminCommission = $data['commission_fee'] - $newSellerData[$sellerId]['commission_fee'];
                }
            }
            if ($receiverAmount > 0) {
                // Getting receiver paypal id
                $receiverPaypalId = Mage::getModel('paypaladaptive/save')->sellerPaypalIdForRefund($incrementId, $data['seller_id']);
                $receiverAmountArray[] = round($receiverAmount, 2);
                $receiverEmailArray[] = $receiverPaypalId;
                $adminTotalCommission = round($adminTotalCommission + $adminCommission, 2);
            }
        }

        // Gather owner paypal id and amount
        $subTotal = array_sum($receiverAmountArray) + $adminTotalCommission;
        $receiverEmailArray[] = $adminEmail;
        $receiverAmountArray[] = round($adminTotalCommission + $creditmemo->getGrandTotal() - $subTotal, 2);

        $resArray = Mage::getModel('paypaladaptive/apicall')->CallRefund($payKey, $transactionId, $trackingId, $receiverEmailArray, $receiverAmountArray, $currencyCode);

        $ack = strtoupper($resArray["responseEnvelope.ack"]);

        if ($ack == "SUCCESS") {

            // Saving refund details
            for ($inc = 0; $inc <= 5; $inc++) {

                if (!empty($resArray["refundInfoList.refundInfo($inc).encryptedRefundTransactionId"])) {

                    $encryptedRefundTransactionId = $resArray["refundInfoList.refundInfo($inc).encryptedRefundTransactionId"];
                    $refundStatus = $resArray["refundInfoList.refundInfo($inc).refundStatus"];
                    $refundNetAmount = $resArray["refundInfoList.refundInfo($inc).refundNetAmount"];
                    $refundFeeAmount = $resArray["refundInfoList.refundInfo($inc).refundFeeAmount"];
                    $refundGrossAmount = $resArray["refundInfoList.refundInfo($inc).refundGrossAmount"];
                    $refundTransactionStatus = $resArray["refundInfoList.refundInfo($inc).refundTransactionStatus"];
                    $receiverEmail = $resArray["refundInfoList.refundInfo($inc).receiver.email"];
                    $currencyCode = $resArray["currencyCode"];

                    Mage::getModel('paypaladaptive/save')->refund($orderId, $incrementId, $payKey, $trackingId, $transactionId, $encryptedRefundTransactionId, $refundStatus, $refundNetAmount, $refundFeeAmount, $refundGrossAmount, $refundTransactionStatus, $receiverEmail, $currencyCode);
                    Mage::getModel('paypaladaptive/save')->changePaymentStatus($incrementId, $payKey, $trackingId, $receiverEmail);
      
                } else {
                    if ($refundStatus != 'REFUNDED') {                        
                        $url = Mage::helper('adminhtml')
                                ->getUrl('adminhtml/sales_order_creditmemo/new', array('order_id' => $creditmemo->getOrderId()));
                        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                        Mage::app()->getResponse()->sendResponse();
                        Mage::throwException(Mage::helper('paypaladaptive')->__('API connection failed : '). $resArray["refundInfoList.refundInfo($inc).refundStatus"]);
                    }
                }
            }
        } else {
            // Error occurred while refunding process    
            $url = Mage::helper('adminhtml')
                    ->getUrl('adminhtml/sales_order_creditmemo/new', array('order_id' => $creditmemo->getOrderId()));
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
            Mage::throwException(Mage::helper('paypaladaptive')->__('API connection failed : '). $resArray["error(0).message"]);
        }
    }

    // Saving product tab data
    public function saveProductTabData(Varien_Event_Observer $observer) {

        $enabledMarplace = Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Marketplace');

        // Checking where marketplace enable or not  
        if ($enabledMarplace != 1) {

            $product = $observer->getEvent()->getProduct();

            try {
                $productId = $product->getId();
                $productPaypalId = Mage::app()->getRequest()->getPost('product_paypal_id');
                $shareMode = Mage::app()->getRequest()->getPost('share_mode');
                $shareValue = Mage::app()->getRequest()->getPost('share_value');
                $isEnable = Mage::app()->getRequest()->getPost('paypal_adaptive_activate');

                $productData = Mage::getModel('paypaladaptive/productdetails')->getCollection()
                        ->addFieldToFilter('product_id', $productId);
                $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($productData);

                if (!empty($firstRow['product_id']) && $firstRow['product_id'] == $productId) {

                    $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptiveproductdetails');
                    $connection = Mage::getSingleton('core/resource')
                            ->getConnection('core_write');
                    $connection->beginTransaction();
                    $fields = array();
                    if (!empty($productPaypalId)) {
                        $fields['product_paypal_id'] = $productPaypalId;
                    }
                    if (!empty($shareMode)) {
                        $fields['share_mode'] = $shareMode;
                    }
                    if (!empty($shareValue)) {
                        $fields['share_value'] = $shareValue;
                    }
                    $fields['is_enable'] = $isEnable;
                    $where[] = $connection->quoteInto('product_id = ?', $productId);
                    $connection->update($table_name, $fields, $where);
                    $connection->commit();
                } else {

                    // Assigning seller payment data
                    $collections = Mage::getModel('paypaladaptive/productdetails');
                    $collections->setProductId($productId);
                    $collections->setProductPaypalId($productPaypalId);
                    $collections->setShareMode($shareMode);
                    $collections->setShareValue($shareValue);
                    $collections->setIsEnable($isEnable);
                    $collections->save();
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }

    // Adding custom product tabs 
    public function customProductTabs(Varien_Event_Observer $observer) {

        $enabledMarplace = Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Marketplace');
        // Checking where marketplace enable or not 
        if ($enabledMarplace != 1) {
            $block = $observer->getEvent()->getBlock();
            if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
                if (Mage::app()->getRequest()->getActionName() == 'edit' || Mage::app()->getRequest()->getParam('type')) {
                    $block->addTab('adaptivepaypal', array(
                        'label' => Mage::helper('paypaladaptive')->__('Apptha Paypal Adaptive Options'),
                        'content' => $block->getLayout()->createBlock('adminhtml/template', 'adaptivepaypal-custom-tabs', array('template' => 'paypaladaptive/tabs.phtml'))->toHtml(),
                    ));
                }
            }
        }
    }

}
