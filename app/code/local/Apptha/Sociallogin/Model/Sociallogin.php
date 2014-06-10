<?php

/**
 * 
 * @package         Apptha Social Login
 * @version         0.1.7
 * @since           Magento 1.5
 * @author          Apptha Team
 * @copyright       Copyright (C) 2011 Powered by Apptha
 * @license         http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date   July 26 2012
 * @Modified By     Murali B
 * @Modified Date   Mar 24 2014
 *
 * */
class Apptha_Sociallogin_Model_Sociallogin extends Mage_Core_Model_Abstract 
{
    /**
     * Initialise the social login extension
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('sociallogin/sociallogin');
    }
    

}