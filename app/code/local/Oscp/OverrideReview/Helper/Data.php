<?php
/**
 * @category   Osc
 * @package    Oscp_OverrideReview
 */

class Oscp_OverrideReview_Helper_Data extends Mage_Core_Helper_Abstract
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