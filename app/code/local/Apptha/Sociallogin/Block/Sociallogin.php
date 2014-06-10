<?php
/**
 * 
 * @package         Apptha Social Login
 * @version         0.1.7
 * @since           Magento 1.5
 * @author          Apptha Team
 * @copyright       Copyright (C) 2014 Powered by Apptha
 * @license         http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date   July 26 2012
 * @Modified By     Murali B
 * @Modified Date   Mar 24 2014
 *
 * */

class Apptha_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template {
    
    /**
     * preparing the social login pop-up layout 
     * 
     * Include the social login js file
     */
    
    public function _prepareLayout() {

        if (Mage::getStoreConfig('sociallogin/general/enable_sociallogin') == 1 && !Mage::helper('customer')->isLoggedIn()) {
            $this->getLayout()->getBlock('head')->addJs('sociallogin/sociallogin.js');
        }

        return parent::_prepareLayout();
    }
}