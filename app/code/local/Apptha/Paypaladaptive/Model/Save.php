<?php

/*
 * ********************************************************* */

/**
 * @name          : Apptha Paypal Adaptive
 * @version	  : 1.0
 * @package       : Apptha
 * @since         : Magento 1.5
 * @subpackage    : Paypal Adaptive
 * @author        : Apptha - http://www.apptha.com
 * @copyright     : Copyright (C) 2013 Powered by Apptha
 * @license       : GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @abstract      : Model File
 * @Creation Date : January 16,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 16,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_Save {

    // Saving payment details to db
    public function saveOrderData($orderId, $invoiceId, $dataSellerId, $dataAmount, $dataCommissionFee, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $grandTotal) {

        // If checking whether seller or owner for store data
        try {
            $paymentCollection = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                    ->addFieldToFilter('seller_invoice_id', $invoiceId)
                    ->addFieldToFilter('seller_id', $dataSellerId);
                    
            if (count($paymentCollection) >= 1) {

                // Assign table prefix if it's exist
                try {
                    $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedetails');
                    $connection = Mage::getSingleton('core/resource')
                            ->getConnection('core_write');
                    $connection->beginTransaction();
                    $where[] = $connection->quoteInto('seller_invoice_id = ?', $invoiceId);
                    $where[] = $connection->quoteInto('seller_id = ?', $dataSellerId);
                    $connection->delete($table_name, $where);
                    $connection->commit();
                } catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                    return;
                }
            }

            // Assigning seller payment data
            $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails');
            $collections->setSellerInvoiceId($invoiceId);
            $collections->setOrderId($orderId);
            $collections->setSellerId($dataSellerId);
            $collections->setSellerAmount($dataAmount);
            $collections->setCommissionAmount($dataCommissionFee);
            $collections->setGrandTotal($grandTotal);
            $collections->setCurrencyCode($dataCurrencyCode);
            $collections->setOwnerPaypalId(Mage::helper('paypaladaptive')->getAdminPaypalId());
            $collections->setPayKey($dataPayKey);
            $collections->setGroupType($dataGroupType);
            $collections->setTrackingId($dataTrackingId);
            $collections->setTransactionStatus('Pending');
            $collections->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            return;
        }
    }

    // Updating transaction id and status

    public function update($payKey, $trackingId, $receiverTransactionId, $receiverTransactionStatus, $senderEmail, $receiverEmail, $receiverInvoiceId) {

        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('pay_key', $payKey)
                ->addFieldToFilter('tracking_id', $trackingId)
                ->addFieldToFilter('seller_id', $receiverEmail)
                ->addFieldToFilter('seller_invoice_id', $receiverInvoiceId);

        if (count($collections) >= 1) {

            // Assign table prefix if it's exist
            try {
                
                // Changing transaction status first letter capital 
                $receiverTransactionStatus = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($receiverTransactionStatus))));
                
                $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedetails');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['seller_transaction_id'] = $receiverTransactionId;
                $fields['buyer_paypal_mail'] = $senderEmail;
                $fields['transaction_status'] = $receiverTransactionStatus;
                $where[] = $connection->quoteInto('pay_key = ?', $payKey);
                $where[] = $connection->quoteInto('tracking_id = ?', $trackingId);
                $where[] = $connection->quoteInto('seller_invoice_id = ?', $receiverInvoiceId);
                $where[] = $connection->quoteInto('seller_id = ?', $receiverEmail);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
                return;
            }
        }
    }

    // Saving refund details to db
    public function refund($orderId, $incrementId, $payKey, $trackingId, $transactionId, $encryptedRefundTransactionId, $refundStatus, $refundNetAmount, $refundFeeAmount, $refundGrossAmount, $refundTransactionStatus, $receiverEmail, $currencyCode) {

        try {
            $payDetails = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                    ->addFieldToFilter('seller_invoice_id', $incrementId)
                    ->addFieldToFilter('pay_key', $payKey)
                    ->addFieldToFilter('tracking_id', $trackingId)
                    ->addFieldToFilter('seller_id', $receiverEmail);

            $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($payDetails);

            if (!empty($firstRow['buyer_paypal_mail'])) {
                $buyerPaypalMail = $firstRow['buyer_paypal_mail'];
            } else {
                $buyerPaypalMail = '';
            }

            // Changing transaction status first letter capital 
            $refundStatus = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($refundStatus))));
            
            // Assigning seller payment data
            $collections = Mage::getModel('paypaladaptive/refunddetails');
            $collections->setIncrementId($incrementId);
            $collections->setOrderId($orderId);
            $collections->setSellerPaypalId($receiverEmail);
            $collections->setPayKey($payKey);
            $collections->setTrackingId($trackingId);
            $collections->setTransactionId($transactionId);
            $collections->setEncryptedRefundTransactionId($encryptedRefundTransactionId);
            $collections->setRefundNetAmount($refundNetAmount);
            $collections->setRefundFeeAmount($refundFeeAmount);
            $collections->setRefundGrossAmount($refundGrossAmount);
            $collections->setbuyerPaypalMail($buyerPaypalMail);
            $collections->setRefundTransactionStatus($refundTransactionStatus);
            $collections->setRefundStatus($refundStatus);
            $collections->setCurrencyCode($currencyCode);

            $collections->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return;
        }
    }

    public function sellerPaypalIdForRefund($incrementId, $sellerId) {

        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('seller_invoice_id', $incrementId)
                ->addFieldToFilter('seller_id', $sellerId);

        $sellerPaypalId = '';
        $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($collections);
        if (!empty($firstRow)) {
            $sellerPaypalId = $firstRow['seller_id'];
        }

        return $sellerPaypalId;
    }

    public function sellerDataForRefund($items, $incrementId, $flag) {

        $sellerData = array();

        // Preparing seller share 
        foreach ($items as $item) {

            $sellerAmount = 0;
            $productId = $item->getProductId();

            $commissionData = Mage::getModel('paypaladaptive/commissiondetails')->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('increment_id', $incrementId);
            $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($commissionData);

            if (!empty($firstRow['seller_id'])) {
                $commisionValue = $firstRow['commission_value'];
                $commissionMode = $firstRow['commission_mode'];
                $sellerId = $firstRow['seller_id'];

                if ($flag == 1) {
                    $productAmount = $item->getPrice() * $item->getQtyInvoiced();
                } else {
                    $productAmount = $item->getPrice() * $item->getQty();
                }

                if ($commissionMode == 'percent') {
                    $productCommission = $productAmount * ($commisionValue / 100);
                    $sellerAmount = $productAmount - $productCommission;
                } else {
                    $productCommission = $commisionValue;
                    $sellerAmount = $productAmount - $commisionValue;
                }

                // Calculating seller share individually
                if (array_key_exists($sellerId, $sellerData)) {
                    $sellerData[$sellerId]['amount'] = $sellerData[$sellerId]['amount'] + $sellerAmount;
                    $sellerData[$sellerId]['commission_fee'] = $sellerData[$sellerId]['commission_fee'] + $productCommission;
                } else {
                    $sellerData[$sellerId]['amount'] = $sellerAmount;
                    $sellerData[$sellerId]['commission_fee'] = $productCommission;
                    $sellerData[$sellerId]['seller_id'] = $sellerId;
                }
            }
        }
        return $sellerData;
    }

    // Storing commission details to database table
    public function saveCommissionData($incrementId, $productId, $commisionValue, $commissionMode,$sellerPaypalId, $sellerId) {

        try {
            $commissionData = Mage::getModel('paypaladaptive/commissiondetails')->getCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('increment_id', $incrementId);
            $firstRow = Mage::helper('paypaladaptive')->getFirstRowData($commissionData);
            if (!empty($firstRow['product_id']) && $firstRow['product_id'] == $productId) {
                $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivecommissiondetails');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['commission_mode'] = $commissionMode;
                $fields['commission_value'] = $commisionValue;
                $fields['seller_id'] = $sellerPaypalId;
                $fields['seller_customer_id'] = $sellerId;
                $where[] = $connection->quoteInto('product_id = ?', $productId);
                $where[] = $connection->quoteInto('increment_id = ?', $incrementId);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } else {

                // Assigning seller payment data
                $collections = Mage::getModel('paypaladaptive/commissiondetails');
                $collections->setProductId($productId);
                $collections->setIncrementId($incrementId);
                $collections->setCommissionMode($commissionMode);
                $collections->setCommissionValue($commisionValue);
                $collections->setSellerId($sellerPaypalId);
                $collections->setSellerCustomerId($sellerId);
                $collections->save();
            }
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            return;
        }
    }

    // Change payment status to refunded
    public function changePaymentStatus($incrementId, $payKey, $trackingId, $receiverEmail) {

        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('pay_key', $payKey)
                ->addFieldToFilter('tracking_id', $trackingId)
                ->addFieldToFilter('seller_id', $receiverEmail)
                ->addFieldToFilter('seller_invoice_id', $incrementId);

        if (count($collections) >= 1) {

            // Assign table prefix if it's exist
            try {
                $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedetails');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['transaction_status'] = 'Refunded';
                $where[] = $connection->quoteInto('pay_key = ?', $payKey);
                $where[] = $connection->quoteInto('tracking_id = ?', $trackingId);
                $where[] = $connection->quoteInto('seller_invoice_id = ?', $incrementId);
                $where[] = $connection->quoteInto('seller_id = ?', $receiverEmail);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return;
            }
        }
    }

    // Change payment status to canceled
    public function cancelPayment($paypalAdaptive, $payKey, $trackingId) {

        $collections = Mage::getModel('paypaladaptive/paypaladaptivedetails')->getCollection()
                ->addFieldToFilter('pay_key', $payKey)
                ->addFieldToFilter('tracking_id', $trackingId)
                ->addFieldToFilter('seller_invoice_id', $paypalAdaptive);

        if (count($collections) >= 1) {

            // Assign table prefix if it's exist
            try {
                $table_name = Mage::getSingleton('core/resource')->getTableName('paypaladaptivedetails');
                $connection = Mage::getSingleton('core/resource')
                        ->getConnection('core_write');
                $connection->beginTransaction();
                $fields = array();
                $fields['transaction_status'] = 'Canceled';
                $where[] = $connection->quoteInto('pay_key = ?', $payKey);
                $where[] = $connection->quoteInto('tracking_id = ?', $trackingId);
                $where[] = $connection->quoteInto('seller_invoice_id = ?', $paypalAdaptive);
                $connection->update($table_name, $fields, $where);
                $connection->commit();
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
                return;
            }
        }
    }

}