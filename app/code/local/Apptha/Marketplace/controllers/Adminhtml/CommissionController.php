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
class Apptha_Marketplace_Adminhtml_CommissionController extends Mage_Adminhtml_Controller_action
{
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
    public function editAction() {     
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_commission_edit'));
        $this->renderLayout();        
    }
    public function payAction() {
        $id         = $this->getRequest()->getParam('id');
        $comment    = $this->getRequest()->getPost('detail'); 
        if ( $id > 0) {
            try {
                $transactions = Mage::getModel('marketplace/transaction')->getCollection()
                                    ->addFieldToFilter('seller_id', $id)
                                    ->addFieldToSelect('id')
                                    ->addFieldToFilter('paid',0);
                            foreach ($transactions as $transaction) {
                                $transaction_id = $transaction->getId();                               
                                if (!empty($transaction_id)) {
                                    Mage::helper('marketplace/marketplace')->updateComment($comment,$transaction_id);
                                }             
                            }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Payment successful'));
                $this->_redirect('*/*/');                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
	}
        public function addcommentAction() {
           $this->_initAction()
           ->renderLayout(); 
        }
} 