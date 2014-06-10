<?php
/**
* @category Oscp
* @package Oscp_ImageGallery
*/ 

class Oscp_ImageGallery_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}

