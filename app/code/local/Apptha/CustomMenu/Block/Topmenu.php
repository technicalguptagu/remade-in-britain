<?php

if (!Mage::getStoreConfig('custom_menu/general/enabled')) {
    class Apptha_CustomMenu_Block_Topmenu extends Mage_Page_Block_Html_Topmenu {}
    return;
}

class Apptha_CustomMenu_Block_Topmenu extends Apptha_CustomMenu_Block_Navigation {}
