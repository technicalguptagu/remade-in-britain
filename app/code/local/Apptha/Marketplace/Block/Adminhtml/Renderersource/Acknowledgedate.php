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
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Acknowledgedate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    //Function to render the data of Acknowledge date of seller
    public function render(Varien_Object $row){       
       $id = $row->getData(); 
       $received_status = '';
        foreach($id as $_id)
        { 
            $paymentStatus = Mage::getModel('marketplace/transaction')->getPaymentstatus($_id);       
            foreach($paymentStatus as $_paymentStatus)
            {   
                if(isset($_paymentStatus['acknowledge_date'])){
                    $received_status = $_paymentStatus['acknowledge_date'];              
                }
                return $received_status;
            }
        }
  }
} 