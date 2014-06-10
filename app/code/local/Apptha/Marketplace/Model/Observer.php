<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.2.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
*/
class Apptha_Marketplace_Model_Observer {
    /*
     * After success payment
     */
    public function successAfter($observer) {
        $orderIds = $observer->getEvent()->getOrderIds();
        $order = Mage::getModel('sales/order')->load($orderIds[0]);
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $getCustomerId = $customer->getId();
        $grandTotal = $order->getGrandTotal();
        $status = $order->getStatus();
        # get Product
        $items = $order->getAllItems();
        $orderEmailData = array();
        foreach($items as $item){
            $getProductId   = $item->getProductId();
            $created_at    = $item->getCreatedAt();
            $payment_method_code = $order->getPayment()->getMethodInstance()->getCode();           
            $products       = Mage::helper('marketplace/marketplace')->getProductInfo($getProductId);            
            $seller_id      = $products->getSellerId();
            if($seller_id){
                if($payment_method_code == 'paypaladaptive'){
                    $credited = 1;
                    $orderPrice     = $item->getPrice() * $item->getQtyInvoiced();
                    $product_amt    = $item->getPrice();
                    $product_qty    = $item->getQtyInvoiced();
                } else {
                    $credited = 0;
                    $orderPrice     = $item->getPrice() * $item->getQtyToInvoice();
                    $product_amt    = $item->getPrice();
                    $product_qty    = $item->getQtyToInvoice();
                }
                //Get seller commission percent                
                $seller_collection  = Mage::helper('marketplace/marketplace')->getSellerCollection($seller_id);
                $percentperproduct  = $seller_collection['commission'];
                $commission_fee     = $orderPrice * ($percentperproduct / 100);
                $seller_amount      = $orderPrice - $commission_fee;
                
                // Storing commissionData 
                $commissionData = array('seller_id' => $seller_id, 'product_id' => $getProductId, 'product_qty' => $product_qty, 'product_amt' => $product_amt, 'commission_fee' => $commission_fee, 'seller_amount' => $seller_amount, 'order_id' => $order->getId(), 'increment_id' => $order->getIncrementId(), 'order_total' => $grandTotal, 'order_status' => $status, 'credited'=>$credited, 'customer_id' => $getCustomerId, 'status' => 1,'created_at'=>$created_at,'payment_method'=>$payment_method_code);
                $commission_id = $this->storeCommissionData($commissionData); 
                $total_product_amt = $total_commission_fee = $total_seller_amt = 0;
                 
                $orderEmailData[$item_count]['seller_id'] = $seller_id;
                $orderEmailData[$item_count]['product_id'] = $getProductId;
                $orderEmailData[$item_count]['product_qty'] = $product_qty;
                $orderEmailData[$item_count]['product_amt'] = $product_amt;
                $orderEmailData[$item_count]['commission_fee'] = $commission_fee;
                $orderEmailData[$item_count]['seller_amount'] = $seller_amount;
                $orderEmailData[$item_count]['increment_id'] = $order->getIncrementId();
                $orderEmailData[$item_count]['customer_email'] = $order->getCustomerEmail();
                $orderEmailData[$item_count]['customer_firstname'] = $order->getCustomerFirstname();
                $item_count = $item_count + 1;                      
            }
//            if(Mage::getStoreConfig('marketplace/admin_approval_seller_registration/sales_notification') == 1){
//                    $this->sendOrderEmail($orderEmailData);    
//            }    
            if($payment_method_code=='paypaladaptive'){
            //Credit action
                    $model              = Mage::helper('marketplace/marketplace')->getCommissionInfo($commission_id);
                    $seller_id          = $model->getSellerId();
                    $admin_commission   = $model->getCommissionFee();
                    $seller_commission  = $model->getSellerAmount();
                    $order_id           = $model->getOrderId();
                    $commission_id      = $model->getId(); 
                    //transaction collection
                    $transaction        = Mage::helper('marketplace/marketplace')->getTransactionInfo($commission_id);
                    $transaction_id     = $transaction->getId();        
                    if(empty($transaction_id))
                    {
                        $Data = array('commission_id' => $commission_id, 'seller_id' => $seller_id,'seller_commission' => $seller_commission, 'admin_commission' => $admin_commission, 'order_id' =>$order_id,'received_status'=>0);
                        Mage::helper('marketplace/marketplace')->saveTransactionData($Data);
                    }
                    //Payment action
                    $transactions = Mage::getModel('marketplace/transaction')->getCollection()
                                    ->addFieldToFilter('seller_id', $seller_id)
                                    ->addFieldToSelect('id')
                                    ->addFieldToFilter('paid',0);
                            foreach($transactions as $transaction){
                                $transaction_id = $transaction->getId();
                                //current date
                                if (!empty($transaction_id)) {
                                    Mage::helper('marketplace/marketplace')->updateTransactionData($transaction_id);
                                } 
                            }                         
        }
            }
            
            if(Mage::getStoreConfig('marketplace/admin_approval_seller_registration/sales_notification') == 1){
                    $this->sendOrderEmail($orderEmailData);    
            }
            
        }

    public function storeCommissionData($commissionData) {        
        $model = Mage::getModel('marketplace/commission');
        $model->setData($commissionData);        
        $model->save();
        $commission_id = $model->getId();
        return $commission_id;
    }
    //salesOrderAfter
    public function salesOrderAfter() {
        $orderId = (int) Mage::app()->getRequest()->getParam('order_id');
        if($orderId){
            $orders = Mage::getModel('sales/order')->load($orderId);
            $status_order = $orders->getStatus();
            $commissions = Mage::getModel('marketplace/commission')->getCollection()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToSelect('id');
            $count = count($commissions);
            if($count>0){
            foreach($commissions as $commission){
                $commission_id = $commission->getId();
                if (!empty($commission_id)) {
                    Mage::helper('marketplace/marketplace')->updateCommissionData($status_order,$commission_id);
                }
            }
        }
        else
        {
        $order = Mage::getModel('sales/order')->load($orderId);
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $getCustomerId = $customer->getId();
        $grandTotal = $order->getGrandTotal();
        $status = $order->getStatus();
        # get Product
        $items = $order->getAllItems();        
        foreach($items as $item){
            $getProductId   = $item->getProductId();
            $created_at     = $item->getCreatedAt();
            $payment_method_code = $order->getPayment()->getMethodInstance()->getCode();           
            $products       = Mage::helper('marketplace/marketplace')->getProductInfo($getProductId);            
            $seller_id      = $products->getSellerId();
            if($seller_id){
                    $credited = 1;
                    $orderPrice     = $item->getPrice() * $item->getQtyToInvoice();
                    $product_amt    = $item->getPrice();
                    $product_qty    = $item->getQtyToInvoice();
                }
                //Get seller commission percent
                $seller_collection  = Mage::helper('marketplace/marketplace')->getSellerCollection($seller_id);
                $percentperproduct  = $seller_collection['commission'];
                $commission_fee     = $orderPrice * ($percentperproduct / 100);
                $seller_amount      = $orderPrice - $commission_fee;
                
                // Storing commissionData 
                $commissionData = array('seller_id' => $seller_id, 'product_id' => $getProductId, 'product_qty' => $product_qty, 'product_amt' => $product_amt, 'commission_fee' => $commission_fee, 'seller_amount' => $seller_amount, 'order_id' => $order->getId(), 'increment_id' => $order->getIncrementId(), 'order_total' => $grandTotal, 'order_status' => $status, 'credited'=>$credited, 'customer_id' => $getCustomerId, 'status' => 1,'created_at'=>$created_at,'payment_method'=>$payment_method_code);
                $commission_id = $this->storeCommissionData($commissionData);                
            }
            if($payment_method_code=='paypaladaptive'){
            //Credit action
                    $model              = Mage::helper('marketplace/marketplace')->getCommissionInfo($commission_id);
                    $seller_id          = $model->getSellerId();
                    $admin_commission   = $model->getCommissionFee();
                    $seller_commission  = $model->getSellerAmount();
                    $order_id           = $model->getOrderId();
                    $commission_id      = $model->getId(); 
                    //transaction collection
                    $transaction        = Mage::helper('marketplace/marketplace')->getTransactionInfo($commission_id);
                    $transaction_id     = $transaction->getId();        
                    if(empty($transaction_id))
                    {
                        $Data = array('commission_id' => $commission_id, 'seller_id' => $seller_id,'seller_commission' => $seller_commission, 'admin_commission' => $admin_commission, 'order_id' =>$order_id,'received_status'=>0);
                        Mage::getModel('marketplace/transaction')->setData($Data)->save();
                    }
                    //Payment action
                    $transactions = Mage::getModel('marketplace/transaction')->getCollection()
                                    ->addFieldToFilter('seller_id', $seller_id)
                                    ->addFieldToSelect('id')
                                    ->addFieldToFilter('paid',0);
                            foreach($transactions as $transaction){
                                $transaction_id = $transaction->getId();
                                //current date
                                if (!empty($transaction_id)) {
                                    Mage::helper('marketplace/marketplace')->updateTransactionData($transaction_id);
                                }             
                            }
                  
           
        }
        }
    }
    }
    //creditmemo(Refund process)
    public function creditMemoEvent(Varien_Event_Observer $observer) {

        $orderId = (int) Mage::app()->getRequest()->getParam('order_id');
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $items = $creditmemo->getAllItems();
        foreach ($items as $item) {
            $getProductId = $item->getProductId();
            // Storing commissionData 
            $commissions = Mage::getModel('marketplace/commission')->getCollection()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('product_id', $getProductId)
                    ->addFieldToSelect('id')
                    ->addFieldToSelect('product_qty');
            foreach ($commissions as $commission) {
                $commission_id = $commission->getId();
                $commission_qty = $commission->getProductQty();
                $qty = $commission_qty - $item->getQty();
                $seller_id = $commission->getSellerId();
                $orderPrice = $item->getPrice() * $qty;
                $seller_collection  = Mage::helper('marketplace/marketplace')->getSellerCollection($seller_id);
                $percentperproduct  = $seller_collection['commission'];               
                $commission_fee = $orderPrice * ($percentperproduct / 100);
                $seller_amount = $orderPrice - $commission_fee;
                if (empty($seller_amount)) {
                    $status = 0;
                } else {
                    $status = 1;
                }
                if (!empty($commission_id)) {
                    $Data = array('product_qty' => $qty, 'commission_fee' => $commission_fee, 'seller_amount' => $seller_amount, 'status' => $status);
                    Mage::helper('marketplace/marketplace')->saveCommissionData($Data,$commission_id);
                }
            }
        }
    }
    public function productEditAction($observer) {
        // Checking whether email notification enabled or not
        if (Mage::getStoreConfig('marketplace/product/productmodificationnotification')) {

            $product = array();
            $prdouct_group_id = $seller_id = $product_url = $marketplace_group_id = $saved_product_status = $saved_product_createdat = $saved_product_updatedat = $saved_product_updatedat = '' ;
            $store = 0;
            $store_name = 'All Store Views';  
            $product = $observer->getProduct();
            $prdouct_group_id = $product->getGroupId();
            $seller_id = $product->getSellerId();
            $product_status = $product->getStatus();            
            $marketplace_group_id = Mage::helper('marketplace')->getGroupId();
            $observer->getStoreId();             
            if($store != 0)
            {
                $store_name = Mage::getModel('core/store')->load($store);    
            }   else {
                $store_name = 'All Store Views';    
            }
            $saved_product_id = $product->getId();
            $saved_product = Mage::getModel('catalog/product')->load($saved_product_id);
            $saved_product_status = $saved_product->getStatus();                              
            if($saved_product_status != $product_status && count($saved_product) >= 1) {            
                    if ($prdouct_group_id == $marketplace_group_id) {
                        if ($product_status == 1) {
                            $template_id = (int) Mage::getStoreConfig('marketplace/product/addproductenabledemailnotificationtemplate');
                        } else {
                            $template_id = (int) Mage::getStoreConfig('marketplace/product/addproductdisabledemailnotificationtemplate');
                        }
                        $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
                        $toMailId = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                        $toName = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                        // Selecting template id
                        if ($template_id) {
                            $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
                        } else {
                            if ($product_status == 1) {

                                $emailTemplate = Mage::getModel('core/email_template')
                                        ->loadDefault('marketplace_product_addproductenabledemailnotificationtemplate');
                            } else {

                                $emailTemplate = Mage::getModel('core/email_template')
                                        ->loadDefault('marketplace_product_addproductdisabledemailnotificationtemplate');
                            }
                        }
                        $customer = Mage::getModel('customer/customer')->load($seller_id);
                        $selleremail = $customer->getEmail();
                        $recipient = $selleremail;
                        $sellername = $customer->getName();
                        $productname = $product->getName();
                        $producturl = $product->getProductUrl();
                        $emailTemplate->setSenderName($toName);
                        $emailTemplate->setSenderEmail($toMailId);
                        $emailTemplateVariables = (array('ownername' => $toName, 'sellername' => $sellername, 'adminemailid' => $toMailId, 'productname' => $productname, 'producturl' => $producturl, 'storename' => $store_name));
                        $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        $emailTemplate->send($recipient, $toName, $emailTemplateVariables);
                    }
            }
        }
    }
  
      public function productMassEditAction($observer) {                        
        // Checking whether email notification enabled or not
        if (Mage::getStoreConfig('marketplace/product/productmodificationnotification')) {
            $product = $product_ids = $attributes_data = array(); 
            $store_name = 'All Store Views';  
            $store_name = 0;
            $attributes_data= $observer->getAttributesData();
            $status = $attributes_data['status'];               
            $product_ids = $observer->getProductIds(); 
            $store = $observer->getStoreId();                                       
            if($store != 0)
            {
                $store_name = Mage::getModel('core/store')->load($store);    
            }  else {
                $store_name = 'All Store Views';    
            }                               
            foreach($product_ids as $product_id)
            {
            $marketplace_group_id = $prdouct_group_id = $seller_id = $product_status = '';   
            $marketplace_group_id = Mage::helper('marketplace')->getGroupId();                  
            $product = Mage::helper('marketplace/marketplace')->getProductInfo($product_id);
            $prdouct_group_id = $product->getGroupId(); 
            $seller_id = $product->getSellerId();  
            $product_status = $product->getStatus();                                 
            if($product_status != $status && $prdouct_group_id == $marketplace_group_id) {             
                if($status == 1) {
                    $template_id = (int) Mage::getStoreConfig('marketplace/product/addproductenabledemailnotificationtemplate');
                } else {
                    $template_id = (int) Mage::getStoreConfig('marketplace/product/addproductdisabledemailnotificationtemplate');
                }
                $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
                $toMailId = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                $toName = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
                // Selecting template id
                if($template_id) {
                    $emailTemplate = Mage::helper('marketplace/marketplace')->loadEmailTemplate($template_id);
                } else {
                    if ($status == 1) {
                        $emailTemplate = Mage::getModel('core/email_template')
                                ->loadDefault('marketplace_product_addproductenabledemailnotificationtemplate');
                    } else {
                        $emailTemplate = Mage::getModel('core/email_template')
                                ->loadDefault('marketplace_product_addproductdisabledemailnotificationtemplate');
                    }
                }
                $customer = Mage::helper('marketplace/marketplace')->loadCustomerData($seller_id);
                $selleremail = $customer->getEmail();
                $recipient = $selleremail;
                $sellername = $customer->getName();
                $productname = $product->getName();
                $producturl = $product->getProductUrl();
                $emailTemplate->setSenderName($toName);
                $emailTemplate->setSenderEmail($toMailId);
                $emailTemplateVariables = (array('ownername' => $toName, 'sellername' => $sellername, 'adminemailid' => $toMailId, 'productname' => $productname, 'producturl' => $producturl, 'storename' => $store_name));
                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailTemplate->send($recipient, $toName, $emailTemplateVariables);
            }
        }
      } // End foreach products iteration
    }
    public function sendOrderEmail($orderEmailData) {

        $seller_ids = array();
        foreach ($orderEmailData as $data) {
            if (!in_array($data['seller_id'], $seller_ids)) {
                $seller_ids[] = $data['seller_id'];
            }
        }
        foreach ($seller_ids as $key => $id) {
            
            $total_product_amt = $total_commission_fee = $total_seller_amt = 0;
            $product_details = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea">';
            $product_details .='<thead><tr>
                                <th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">Product Name</th>
                                <th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">Product QTY</th>
                                <th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">Product Amount</th>
                                <th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">Commission Fee</th>
                                <th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">Seller Amount</th>
                                </tr></thead>
                                <tbody bgcolor="#F6F6F6">';
            $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();    
            foreach ($orderEmailData as $data) {
                if ($id == $data['seller_id']) {
                    
                    $seller_id = $data['seller_id'];
                    $product_id = $data['product_id'];
                    
            $product = Mage::helper('marketplace/marketplace')->getProductInfo($product_id);
            // Getting group id
            $group_id = Mage::helper('marketplace')->getGroupId();
            $product_group_id = $product->getGroupId();
            $productname = $product->getName();                    

                    $product_details .= '<tr>';                   
                    $product_details .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productname . '</td>';
                    $product_details .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $data['product_qty'] . '</td>';
                    $product_details .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol.$data['product_amt'] . '</td>';
                    $product_details .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol.$data['commission_fee'] . '</td>';
                    $product_details .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol.$data['seller_amount'] . '</td>';

                    $total_product_amt = $total_product_amt + $data['product_amt'];
                    $total_commission_fee = $total_commission_fee + $data['commission_fee'];
                    $total_seller_amt = $total_seller_amt + $data['seller_amount'] ;        
                    
                    $increment_id = $data['increment_id'];
                    $customer_email = $data['customer_email'];
                    $customer_firstname = $data['customer_firstname'];
                    
                    $product_details .= '</tr>';
                }
            }                              
             $product_details .= '</tbody>';
             $product_details .= '<tfoot>
                                 <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px">Commision Fee</td>
                                    <td align="center" style="padding:3px 9px"><span>'.$currencySymbol.$total_commission_fee.'</span></td>
                                </tr>
                                 <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px">Seller Amount</td>
                                    <td align="center" style="padding:3px 9px"><span>'.$currencySymbol.$total_seller_amt.'</span></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right" style="padding:3px 9px"><strong>Total Product Amount</strong></td>
                                    <td align="center" style="padding:3px 9px"><strong><span>'.$currencySymbol.$total_product_amt.'</span></strong></td>
                                </tr>
                            </tfoot>';
                $product_details .= '</table>';  
                if ($group_id == $product_group_id) {
                // Sending order email 
                $template_id = (int) Mage::getStoreConfig('marketplace/admin_approval_seller_registration/sales_notification_template_selection');
                $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
                $toMailId = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                $toName = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");

                if ($template_id) {
                    $emailTemplate = Mage::helper('marketplace/marketplace')->loadEmailTemplate($template_id);
                } else {
                    $emailTemplate = Mage::getModel('core/email_template')
                            ->loadDefault('marketplace_admin_approval_seller_registration_sales_notification_template_selection');
                }
                $customer = Mage::helper('marketplace/marketplace')->loadCustomerData($seller_id);
                $selleremail = $customer->getEmail();
                $sellername = $customer->getName();               
                $recipient = $toMailId;
                $seller_store = Mage::app()->getStore()->getName();
                $recipient_seller = $selleremail;
                $emailTemplate->setSenderName($customer_firstname);
                $emailTemplate->setSenderEmail($customer_email);
                $emailTemplateVariables = (array('ownername' => $toName,'productdetails' => $product_details,'order_id' => $increment_id,'seller_store' => $seller_store,'customer_email'=>$customer_email,'customer_firstname'=>$customer_firstname));
                $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                // Sending mail to admin              
                $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailTemplate->send($recipient, $sellername, $emailTemplateVariables);
                // Sending mail to seller
                $emailTemplateVariables = (array('ownername' => $sellername, 'productdetails' => $product_details,'order_id' => $increment_id,'seller_store' => $seller_store,'customer_email'=>$customer_email,'customer_firstname'=>$customer_firstname));
                $emailTemplate->send($recipient_seller, $sellername, $emailTemplateVariables);
            }         
        }
    }
}
