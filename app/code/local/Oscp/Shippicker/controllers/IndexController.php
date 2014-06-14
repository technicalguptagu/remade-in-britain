<?php
/**
 * Oscp.com
 *
 * Oscp Shippicker Module 
 *
 * @category     Magento Extension
 * @copyright    Copyright (c) 2011 Ecommerce Team (http://www.ecommerce-team.com)
 * @author       Ecommerce Team
 * @version      1.0
 */
class Oscp_Shippicker_IndexController extends Mage_Core_Controller_Front_Action
{   
    /**
     * Get phtml file to browser
     *
     */
    public function indexAction()
    {	
		$this->loadLayout();     
		$this->renderLayout();
    }
}
