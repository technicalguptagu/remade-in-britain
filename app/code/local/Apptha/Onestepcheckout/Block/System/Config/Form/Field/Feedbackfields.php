<?php
/**
 * @name         :  Apptha One Step Checkout
 * @version      :  1.7
 * @since        :  Magento 1.4
 * @author       :  Apptha - http://www.apptha.com
 * @copyright    :  Copyright (C) 2013 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  June 20 2011
 * @Modified By  :  Murali B
 * @Modified Date:  Feb 24 2014
 * 
 * */
class Apptha_Onestepcheckout_Block_System_Config_Form_Field_Feedbackfields extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('value', array(
            'label' => $this->__('Label'),
            'style' => 'width:250px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = $this->__('Add label');
        parent::__construct();
    }
}