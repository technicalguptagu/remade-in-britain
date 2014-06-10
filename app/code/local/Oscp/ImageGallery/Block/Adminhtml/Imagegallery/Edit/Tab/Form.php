<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_ImageGallery_Block_Adminhtml_Imagegallery_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('imagegallery_form', array(
            'legend' => Mage::helper('imagegallery')->__('Gallery information')
        ));


        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'select', array(
                'label' => Mage::helper('core')->__('Store'),
                'title' => Mage::helper('core')->__('Store'),
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                'name' => 'store_id',
                'required' => true
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'store_id',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }


        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('imagegallery')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title'
        ));

        $fieldset->addField('smallimage', 'file', array(
            'label' => Mage::helper('imagegallery')->__('Thumbnail Image'),
            'required' => false,
            'name' => 'smallimage'
        ));


        $fieldset->addField('largeimage', 'file', array(
            'label' => Mage::helper('imagegallery')->__('Large Image'),
            'required' => false,
            'name' => 'largeimage'
        ));

        $fieldset->addField('weblink', 'text', array(
            'label' => Mage::helper('imagegallery')->__('Web Url'),
            'required' => false,
            'name' => 'weblink'
        ));

        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('imagegallery')->__('Sort Order'),
            'required' => false,
            'name' => 'sort_order'
        ));

        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('imagegallery')->__('Description'),
            'title' => Mage::helper('imagegallery')->__('Description'),
            'style' => 'width:400px; height:100px;',
            'wysiwyg' => false,
            'required' => false
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('imagegallery')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('imagegallery')->__('Enabled')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('imagegallery')->__('Disabled')
                )
            )
        ));

        if (Mage::getSingleton('adminhtml/session')->getBannerSliderData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerSliderData());
            Mage::getSingleton('adminhtml/session')->setBannerSliderData(null);
        } elseif (Mage::registry('imagegallery_data')) {
            $form->setValues(Mage::registry('imagegallery_data')->getData());
        }

        if ($smallimage = $form->getElement('smallimage')) {
            $smallimage->setRenderer(Mage::app()->getLayout()->createBlock('imagegallery/adminhtml_imagegallery_edit_tab_smallimage'));
        }
        if ($largeimage = $form->getElement('largeimage')) {
            $largeimage->setRenderer(Mage::app()->getLayout()->createBlock('imagegallery/adminhtml_imagegallery_edit_tab_largeimage'));
        }

        return parent::_prepareForm();
    }

}

