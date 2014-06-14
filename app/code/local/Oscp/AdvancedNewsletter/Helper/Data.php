<?php
/**
 * Oscp.com
 *
 * Oscp   Advanced Newsletter
 *
 * @category     Magento Extension
 * @copyright    Copyright (c) 2011 Ecommerce Team (http://www.ecommerce-team.com)
 * @author       Ecommerce Team
 * @version      1.0
 */
class Oscp_AdvancedNewsletter_Helper_Data extends Mage_Core_Helper_Abstract
{
	 /**
     * Retrieve Session Data Set In Controller
     *
     * @return Object of Core Session
     */
    public function getSessionData()
    {
        return Mage::getSingleton('core/session',array('name' => 'frontend'));
    }
}
	 