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
class Apptha_Marketplace_Adminhtml_PaymentinfoController extends Mage_Adminhtml_Controller_action
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
     public function exportCsvAction()
    {
        $fileName   = 'transaction.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/customer_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'transaction.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/customer_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
} 