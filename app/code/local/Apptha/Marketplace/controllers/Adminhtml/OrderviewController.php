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
class Apptha_Marketplace_Adminhtml_OrderviewController extends Mage_Adminhtml_Controller_action {
    protected function _initAction() {
        $this->loadLayout()
        ->_setActiveMenu('marketplace/items')
        ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }
    public function indexAction() {
        $this->_initAction()
        ->renderLayout();
    }
    public function creditAction() {
        $id = $this->getRequest()->getParam('id');
        if ($id > 0) {
            try {
                $model               = Mage::getModel('marketplace/commission')->load($id);
                                       $model->setCredited('1')->save();
                $seller_id           = $model->getSellerId();
                $admin_commission    = $model->getCommissionFee();
                $seller_commission   = $model->getSellerAmount();
                $order_id            = $model->getOrderId();
                //transaction collection
                $transaction         = Mage::getModel('marketplace/transaction')->load($id, 'commission_id');
                $transaction_id      = $transaction->getId();
                if (empty($transaction_id)) {
                    $Data = array('commission_id' => $id, 'seller_id' => $seller_id, 'seller_commission' => $seller_commission, 'admin_commission' => $admin_commission, 'order_id' => $order_id);
                    Mage::getModel('marketplace/transaction')->setData($Data)->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Amount was successfully credited'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }
    public function masscreditAction() {
        $marketplace = $this->getRequest()->getPost('marketplace');
        foreach ($marketplace as $value) {
            $model              = Mage::helper('marketplace/marketplace')->updateCredit($value);
            $seller_id         = $model->getSellerId();
            $admin_commission  = $model->getCommissionFee();
            $seller_commission = $model->getSellerAmount();
            $order_id          = $model->getOrderId();
            //transaction collection
           $transaction        = Mage::helper('marketplace/marketplace')->getTransactionInfo($value);
            $transaction_id    = $transaction->getId();
            if (empty($transaction_id)) {
                $Data = array('commission_id' => $value, 'seller_id' => $seller_id, 'seller_commission' => $seller_commission, 'admin_commission' => $admin_commission, 'order_id' => $order_id);
                 Mage::helper('marketplace/marketplace')->saveTransactionData($Data);
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Amount was successfully credited'));
        $this->_redirect('*/*/');
    }
} 