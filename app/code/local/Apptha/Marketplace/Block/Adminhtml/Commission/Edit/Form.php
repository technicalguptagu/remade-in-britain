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
class Apptha_Marketplace_Block_Adminhtml_Commission_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
 protected function _prepareForm(){
    $seller_id  = $this->getRequest()->getParam('id');   
    $collection = Mage::getModel('marketplace/transaction')            
                  ->load($seller_id,'seller_id');               
    $this->setCollection($collection);            
    $form       = new Varien_Data_Form(array(
                  'id'      => 'edit_form',
                  'action'  => $this->getUrl('*/*/pay', array('id' => $this->getRequest()->getParam('id'))),
                  'method'  => 'post',
                  'enctype' => 'multipart/form-data'
        )
     );
     $fieldset = $form->addFieldset('add_comment', array('legend' => Mage::helper('marketplace')->__('Comments')));
     $fieldset->addField('detail', 'textarea', array(
            'name'      => 'detail',
            'title'     => Mage::helper('marketplace')->__('Comments'),
            'label'     => Mage::helper('marketplace')->__('Comments'),
            'style'     => 'height: 200px;',
            'required'  => true,
            'value'     => $collection['comment']
        ));     
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
