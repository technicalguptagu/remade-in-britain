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
 * @abstract      : Controller File
 * @Creation Date : January 02,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 16,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_AdaptiveController extends Mage_Core_Controller_Front_Action {

    public function redirectAction() {

        // Checking whether order id available or not
        $session = Mage::getSingleton('checkout/session');
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $orderId = $order->getId();
        $orderStatus = $order->getStatus();
        if (empty($orderId) || $orderStatus != 'pending') {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
            $this->_redirect('checkout/cart');
            return FALSE;
        }

        // Initilize adaptive payment data    
        $actionType = "PAY";
        $cancelUrl = Mage::getUrl('paypaladaptive/adaptive/cancel');
        $returnUrl = Mage::getUrl('paypaladaptive/adaptive/return');
        $ipnNotificationUrl = Mage::getUrl('paypaladaptive/adaptive/ipnnotification');
        $currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $senderEmail = "";
        $feesPayer = Mage::helper('paypaladaptive')->getFeePayer();        
        $memo = "";
        $pin = "";
        $preapprovalKey = "";
        $reverseAllParallelPaymentsOnError = "";
        $trackingId = $this->generateTrackingID();

        $enabledMarplace = Mage::helper('paypaladaptive')->getModuleInstalledStatus('Apptha_Marketplace');

        // Checking where marketplace enable or not  
        if ($enabledMarplace == 1) {
            // Calculating receiver data
            $receiverData = Mage::helper('paypaladaptive')->getMarketplaceSellerData();
        } else {
            // Calculating receiver data
            $receiverData = Mage::helper('paypaladaptive')->getSellerData();
        }

        // If Checking whether receiver count greater than 5 or not
        $receiverCount = count($receiverData);
        if ($receiverCount > 5) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("You have ordered more than 5 partner products"));
            $this->_redirect('checkout/cart');
            return;
        }

        // Geting checkout grand total amount 
        $grandTotal = round(Mage::helper('paypaladaptive')->getGrandTotal(), 2);

        // Getting receiver amount total       
        $amountTotal = $this->getAmountTotal($receiverData);

        $sellerTotal = round($amountTotal, 2);
        
        if ($grandTotal >= $sellerTotal) {

            // Initilize receiver data
            $receiverAmountArray = $receiverEmailArray = $receiverPrimaryArray = $receiverInvoiceIdArray = array();

            // Getting invoice id
            $invoiceId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $paypalInvoiceId = $invoiceId . $trackingId;

            // Preparing receiver data
            foreach ($receiverData as $data) {
                // Getting receiver paypal id
                $receiverPaypalId = $data['seller_id'];
                $receiverAmountArray[] = round($data['amount'], 2);
                $receiverEmailArray[] = $receiverPaypalId;
                $receiverPrimaryArray[] = 'false';
                $receiverInvoiceIdArray[] = $paypalInvoiceId;
            }

           
            $receiverEmailArray[] = Mage::helper('paypaladaptive')->getAdminPaypalId();
            $receiverInvoiceIdArray[] = $paypalInvoiceId;             
            
            $paymentMethod =Mage::helper('paypaladaptive')->getPaymentMethod();
            
             // If no seller product available for checkout. Setting receiverPrimaryArray empty     
            if ($receiverCount < 1) {      
            $receiverPrimaryArray = array();
            // Assigning store owner paypal id & amount
            $receiverAmountArray[] = round($grandTotal, 2);
            }elseif($paymentMethod == 'parallel'){       
            $receiverPrimaryArray[] = 'false';
            // Assigning store owner paypal id & amount
            $receiverAmountArray[] = round($grandTotal - $sellerTotal, 2);
            }else{           
            $receiverPrimaryArray[] = 'true';              
            // Assigning store owner paypal id & amount
            $receiverAmountArray[] = round($grandTotal, 2);
            }
           
            } else {

            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Please contact admin partner amount is greater than total amount"));
            $this->_redirect('checkout/cart');
            return;
           }
           
        $resArray = Mage::getModel('paypaladaptive/apicall')->CallPay($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId
        );

        $ack = strtoupper($resArray["responseEnvelope.ack"]);

        if ($ack == "SUCCESS") {
            $cmd = "cmd=_ap-payment&paykey=" . urldecode($resArray["payKey"]);

            // Assigning session valur for paykey , tracking id and order id
            $session = Mage::getSingleton('checkout/session');
            $session->setPaypalAdaptiveTrackingId($trackingId);
            $session->setPaypalAdaptivePayKey(urldecode($resArray["payKey"]));
            $session->setPaypalAdaptiveRealOrderId($invoiceId);

            // Storing seller payment details to paypaladaptivedetails table 
            foreach ($receiverData as $data) {

                // Initilizing payment data for save
                $dataSellerId = $data['seller_id'];
                $dataAmount = round($data['amount'], 2);
                $dataCommissionFee = round($data['commission_fee'], 2);
                $dataCurrencyCode = $currencyCode;
                $dataPayKey = $resArray["payKey"];
                $dataGroupType = 'seller';
                $dataTrackingId = $trackingId;

                // Calling save function for storing seller payment data
                Mage::getModel('paypaladaptive/save')->saveOrderData($orderId, $invoiceId, $dataSellerId, $dataAmount, $dataCommissionFee, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $grandTotal);
            }

            
             // Initilizing payment data for save
             $dataSellerId = Mage::helper('paypaladaptive')->getAdminPaypalId();  
             $dataCommissionFee = 0;
             $dataCurrencyCode = $currencyCode;
             $dataPayKey = $resArray["payKey"];
             $dataGroupType = 'admin';
             $dataTrackingId = $trackingId;
             $dataAmount = $grandTotal - $sellerTotal; 

            // Calling save function for storing owner payment data      
            Mage::getModel('paypaladaptive/save')->saveOrderData($orderId, $invoiceId, $dataSellerId, $dataAmount, $dataCommissionFee, $dataCurrencyCode, $dataPayKey, $dataGroupType, $dataTrackingId, $grandTotal);

            // Redirectr to Paypal site
            $this->RedirectToPayPal($cmd);
            return;
        } else {
            $errorMsg = urldecode($resArray["error(0).message"]);
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Pay API call failed."));
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Error Message:") . ' ' . $errorMsg);
            $this->_redirect('checkout/cart');
            return;
        }
    }

    // Payment return functionality 
    public function returnAction() {

        $session = Mage::getSingleton('checkout/session');

        // Getting pay key and tracking id 
        $payKey = $session->getPaypalAdaptivePayKey();
        $trackingId = $session->getPaypalAdaptiveTrackingId();
        $transactionId = '';

        // Make the Pay Details call to PayPal 
        $resArray = Mage::getModel('paypaladaptive/apicall')->CallPaymentDetails($payKey, $transactionId, $trackingId);

        $ack = strtoupper($resArray["responseEnvelope.ack"]);
        if ($ack == "SUCCESS" && isset($resArray["paymentInfoList.paymentInfo(0).transactionId"]) && $resArray["paymentInfoList.paymentInfo(0).transactionId"] != '') {

            $paypalAdaptive = $session->getPaypalAdaptiveRealOrderId();

            try {

                $order = Mage::getModel('sales/order');
                $order->loadByIncrementId($paypalAdaptive);
                $order->setLastTransId($resArray["paymentInfoList.paymentInfo(0).transactionId"])->save();

                if ($order->canInvoice()) {
                    $invoice = $order->prepareInvoice();
                    $invoice->register()->pay();
                    $invoice->getOrder()->setIsInProcess(true);
                    $status = Mage::helper('paypaladaptive')->getOrderSuccessStatus();
                    $invoice->getOrder()->setData('state', $status);
                    $invoice->getOrder()->setStatus($status);
                    $history = $invoice->getOrder()->addStatusHistoryComment('Partial amount of captured automatically.', true);
                    $history->setIsCustomerNotified(true);
                    $invoice->sendEmail(true, '');

                    Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder())
                            ->save();
                    $invoice->save();

                    // Saving payment success details
                    for ($inc = 0; $inc <= 5; $inc++) {

                        if (!empty($resArray["paymentInfoList.paymentInfo($inc).transactionId"])) {

                            $receiverTransactionId = $resArray["paymentInfoList.paymentInfo($inc).transactionId"];
                            $receiverTransactionStatus = $resArray["paymentInfoList.paymentInfo($inc).transactionStatus"];
                            $senderEmail = $resArray["senderEmail"];
                            $receiverEmail = $resArray["paymentInfoList.paymentInfo($inc).receiver.email"];
                            $receiverInvoiceId = $resArray["paymentInfoList.paymentInfo($inc).receiver.invoiceId"];

                            // Updating transaction id and status
                            Mage::getModel('paypaladaptive/save')->update($payKey, $trackingId, $receiverTransactionId, $receiverTransactionStatus, $senderEmail, $receiverEmail, $receiverInvoiceId);
                        }
                    }

                    $session->unsPaypalAdaptivePayKey();
                    $session->unsPaypalAdaptiveTrackingId();
                    $session->unsPaypalAdaptiveRealOrderId();
                    $this->_redirect('checkout/onepage/success', array('_secure' => true));
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError($e->getMessage());
                return;
            }
        } else {
            $this->_redirect('checkout/onepage/failure');
            return;
        }
    }

    // Payment return functionality 
    public function ipnnotificationAction() {
        
    }

    // Order cancel functionality 
    public function cancelAction() {

        try {
            $session = Mage::getSingleton('checkout/session');
            $paypalAdaptive = $session->getPaypalAdaptiveRealOrderId();
            $payKey = $session->getPaypalAdaptivePayKey();
            $trackingId = $session->getPaypalAdaptiveTrackingId();

            if (empty($paypalAdaptive)) {
                Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("No order for processing found"));
                $this->_redirect('checkout/cart');
                return;
            }
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($paypalAdaptive);
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Payment Canceled."));

            // Changing payment status
            Mage::getModel('paypaladaptive/save')->cancelPayment($paypalAdaptive, $payKey, $trackingId);

            $session->unsPaypalAdaptivePayKey();
            $session->unsPaypalAdaptiveTrackingId();
            $session->unsPaypalAdaptiveRealOrderId();

            $this->_redirect('checkout/cart');
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Unable to cancel Paypal Adaptive Checkout."));
            $this->_redirect('checkout/cart');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError(Mage::helper('paypaladaptive')->__("Unable to cancel Paypal Adaptive Checkout."));
            $this->_redirect('checkout/cart');
            return;
        }
    }

    // Calculating  sum of receiver amount
    public function getAmountTotal($receiverData) {

        $amountTotal = 0;
        foreach ($receiverData as $data) {
            $amountTotal = $amountTotal + $data['amount'];
        }
        return $amountTotal;
    }
    
    public function generateCharacter() {
        $possible = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
        return $char;
    }

    // Generating tracking id
    public function generateTrackingID() {
        $GUID = $this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
        $GUID .=$this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
        $GUID .=$this->generateCharacter() . $this->generateCharacter() . $this->generateCharacter();
        return $GUID;
    }

    // Redirect to paypal.com here   
    public function RedirectToPayPal($cmd) {
        $mode = Mage::helper('paypaladaptive')->getPaymentMode();
        $payPalURL = "";
        if ($mode == 1) {
            $payPalURL = "https://www.sandbox.paypal.com/webscr?" . $cmd;
        } else {
            $payPalURL = "https://www.paypal.com/webscr?" . $cmd;
        }
        Mage::app()->getResponse()->setRedirect($payPalURL);
        return FALSE;
    }

}