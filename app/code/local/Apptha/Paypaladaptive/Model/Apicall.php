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
 * @abstract      : Apicall File
 * @Creation Date : January 13,2014
 * @Modified By   : Ramkumar M
 * @Modified Date : January 23,2014
 * */
/*
 * ********************************************************* */

class Apptha_Paypaladaptive_Model_Apicall {

    // Pay call to PayPal 
    function hashCall($methodName, $nvpStr) {

        // Setting the curl parameters     
        $ApiUserName = Mage::helper('paypaladaptive')->getApiUserName();
        $ApiPassword = Mage::helper('paypaladaptive')->getApiPassword();
        $ApiSignature = Mage::helper('paypaladaptive')->getApiSignature();
        $ApiAppID = Mage::helper('paypaladaptive')->getAppID();        
        $mode = Mage::helper('paypaladaptive')->getPaymentMode();
       
        if ($mode == 1) {
        $ApiEndpoint = "https://svcs.sandbox.paypal.com/AdaptivePayments";
        $ApiEndpoint .= "/" . $methodName;
        } else {
        $ApiEndpoint = "https://svcs.paypal.com/AdaptivePayments";
        $ApiEndpoint .= "/" . $methodName;
        }       
        
        try{        
         
        $curl = new Varien_Http_Adapter_Curl();    
          
        // See DetailLevelCode in the WSDL 
        $detailLevel = urlencode("ReturnAll");

        // For valid enumerations
        // This should be the standard RFC 
        $errorLanguage = urlencode("en_US");

        // NVPRequest for submitting to server
        $nvpreq = "requestEnvelope.errorLanguage=$errorLanguage&requestEnvelope";
        $nvpreq .= "detailLevel=$detailLevel&$nvpStr";
        
        // The below line for SSL
        //$config = array('timeout' => 60,'verifypeer' => true,'verifyhost' => 2);
                
        $config = array('timeout' => 60,'verifypeer' => FALSE,'verifyhost' => FALSE);
        $curl->setConfig($config);

        // Setting the curl parameters.          
         $curl->addOption('CURLOPT_VERBOSE', 1);    
         
         $header =  array(
            'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
            'X-PAYPAL-RESPONSE-DATA-FORMAT: NV',
            'X-PAYPAL-SECURITY-USERID: ' . $ApiUserName,
            'X-PAYPAL-SECURITY-PASSWORD: ' . $ApiPassword,
            'X-PAYPAL-SECURITY-SIGNATURE: ' . $ApiSignature,
            'X-PAYPAL-SERVICE-VERSION: 1.3.0',
            'X-PAYPAL-APPLICATION-ID: ' . $ApiAppID
        );     
         
        $curl->write(Zend_Http_Client::POST,$ApiEndpoint, $http_ver = '1.1',$header, $nvpreq);        
             
        $data = $curl->read(); 
        
        $errNo = $curl->getErrno();
 
        if($errNo == 60){
        $cacert = Mage::getBaseDir('lib').'/paypaladaptive/cacert.pem';    
        $curl->addOption('CURLOPT_CAINFO',$cacert);
        $data = $curl->read();  
        }
        
        if ($curl->getErrno()) {
        //Execute the Error handling module to display errors
        Mage::getSingleton('checkout/session')->addError($curl->getError());
        return;    
        } else {        
        // Converting NVPResponse to an Associative Array  
        $nvpResArray = $this->deformatNVP($data);     
            
        // Closing the curl
        $curl->close();
        }        
        
        // Return Response data
        return $nvpResArray;        
        }catch (Exception $e) {
        Mage::getSingleton('checkout/session')->addError($e->getMessage());
        return;              
        } 
    }

    // Prepares the parameters for the PaymentDetails API Call    
    public function CallPaymentDetails($payKey, $transactionId, $trackingId) {

        // Gather the information to make the PaymentDetails call        
        $nvpstr = "";

        // Conditionally required fields
        if ("" != $payKey) {
            $nvpstr = "payKey=" . urlencode($payKey);
        } elseif ("" != $transactionId) {
            $nvpstr = "transactionId=" . urlencode($transactionId);
        } elseif ("" != $trackingId) {
            $nvpstr = "trackingId=" . urlencode($trackingId);
        }

        // Make the PaymentDetails call to PayPal
        $resArray = $this->hashCall("PaymentDetails", $nvpstr);

        // Return the response array 
        return $resArray;
    }

    // This function will take NVPString and convert it to an Associative Array    
    // It is useful to search for a particular key and display arrays
    public function deformatNVP($nvpstr) {
        $intial = 0;
        $nvpArray = array();

        while (strlen($nvpstr)) {
            // Postion of Key
            $keypos = strpos($nvpstr, '=');
            // Position of value
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
            // Getting the Key and Value values and storing in a Associative Array 
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            // Decoding the respose
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        return $nvpArray;
    }

    // Collecting the information to make the Pay call
    public function CallPay($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId) {

         $memo = $pin = $preapprovalKey = $senderEmail = '';
        
        // The variable nvpstr holds the name-value pairs
        // Required fields for pay call
        $nvpstr = "actionType=" . urlencode($actionType) . "&currencyCode=";
        $nvpstr .= urlencode($currencyCode) . "&returnUrl=";
        $nvpstr .= urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);
        
               
        if (0 != count($receiverAmountArray)) {          
            $nvpstr .= $this->receiverAmountData($receiverAmountArray,$nvpstr);                
        }
        
        if (0 != count($receiverEmailArray)) {            
        $nvpstr .= $this->receiverEmailData($receiverEmailArray,$nvpstr);       
        }
        
        if (0 != count($receiverPrimaryArray)) {            
        $nvpstr .= $this->receiverPrimaryData($receiverPrimaryArray,$nvpstr);     
        }
        
        if (0 != count($receiverInvoiceIdArray)) {            
        $nvpstr .= $this->receiverInvoiceIdData($receiverInvoiceIdArray,$nvpstr);     
        }

        // Optional fields for pay call
        if ("" != $feesPayer) {
            $nvpstr .= "&feesPayer=" . urlencode($feesPayer);
        }
        if ("" != $ipnNotificationUrl) {
            $nvpstr .= "&ipnNotificationUrl=" . urlencode($ipnNotificationUrl);
        }        
       
        if ("" != $reverseAllParallelPaymentsOnError) {
            $nvpstr .= "&reverseAllParallelPaymentsOnError=";
            $nvpstr .= urlencode($reverseAllParallelPaymentsOnError);
        }
    
        if ("" != $trackingId) {
            $nvpstr .= "&trackingId=" . urlencode($trackingId);
        }

        // Make the Pay call to PayPal 
        $resArray = $this->hashCall("Pay", $nvpstr);

        // Return the response array 
        return $resArray;
    }

    // Prepares the parameters for the Refund API Call   
    function CallRefund($payKey, $transactionId, $trackingId, $receiverEmailArray, $receiverAmountArray,$currencyCode) {

        // Gather the information to make the Refund call       
        $nvpstr = "currencyCode=";
        $nvpstr .= urlencode($currencyCode);

        // conditionally required fields
        if ("" != $payKey) {
            $nvpstr .= "&payKey=" . urlencode($payKey);
            if (0 != count($receiverEmailArray)) {
               $nvpstr .= $this->receiverEmailData($receiverEmailArray,$nvpstr);  
            }
            if (0 != count($receiverAmountArray)) {
                $nvpstr .= $this->receiverAmountData($receiverAmountArray,$nvpstr);
            }
        } elseif ("" != $trackingId) {
            $nvpstr .= "&trackingId=" . urlencode($trackingId);
            if (0 != count($receiverEmailArray)) {
            $nvpstr .= $this->receiverEmailData($receiverEmailArray,$nvpstr);  
            }
            if (0 != count($receiverAmountArray)) {
            $nvpstr .= $this->receiverAmountData($receiverAmountArray,$nvpstr);
            }
        } elseif ("" != $transactionId) {
            $nvpstr .= "&transactionId=" . urlencode($transactionId);
            // the caller should only have 1 entry in the email and amount arrays
            if (0 != count($receiverEmailArray)) {
               $nvpstr .= $this->receiverEmailData($receiverEmailArray,$nvpstr);  
            }
            if (0 != count($receiverAmountArray)) {
              $nvpstr .= $this->receiverAmountData($receiverAmountArray,$nvpstr);
            }
        }
             
        // Make the Refund call to PayPal 
        $resArray = $this->hashCall("Refund", $nvpstr);

        // Return the response array 
        return $resArray;
    }
    
       
    public function receiverAmountData($receiverAmountArray,$nvpstr){    
    reset($receiverAmountArray);
            while (list($key, $value) = each($receiverAmountArray)) {
                if ("" != $value) {
                    $nvpstr .= "&receiverList.receiver(" . $key . ").amount=" . urlencode($value);
                }
            }
    return $nvpstr;       
    }
    
     public function receiverEmailData($receiverEmailArray,$nvpstr){
     reset($receiverEmailArray);
            while (list($key, $value) = each($receiverEmailArray)) {
                if ("" != $value) {
                    $nvpstr .= "&receiverList.receiver(" . $key . ").email=" . urlencode($value);
                }
            }
     return $nvpstr;
     }
     
      public function receiverPrimaryData($receiverPrimaryArray,$nvpstr){
         reset($receiverPrimaryArray);
            while (list($key, $value) = each($receiverPrimaryArray)) {
                if ("" != $value) {
                    $nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").primary=" .
                            urlencode($value);
                }
            }
      return $nvpstr;      
      }
      
         public function receiverInvoiceIdData($receiverInvoiceIdArray,$nvpstr){    
           reset($receiverInvoiceIdArray);
            while (list($key, $value) = each($receiverInvoiceIdArray)) {
                if ("" != $value) {
                    $nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").invoiceId=" .
                            urlencode($value);
                }
            }
         return $nvpstr;
         }
    
}