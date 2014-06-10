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
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Commissionrate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    //Function to render Total sales of particular seller
    public function render(Varien_Object $row){       
      $id = $row->getData();
       foreach($id as $_id){  
            $sellerProduct = Mage::helper('marketplace/marketplace')->getSellerCollection($_id);                 
            return $sellerProduct['commission'];         
       }
    }
} 