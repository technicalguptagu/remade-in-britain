<?php
class Apptha_CustomMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_menuData = null;

    public function getMenuData()
    {
        if (!is_null($this->_menuData)) return $this->_menuData;
        $blockClassName = Mage::getConfig()->getBlockClassName('custommenu/navigation');
        $block = new $blockClassName();
        $categories = $block->getStoreCategories();
        if (is_object($categories)) $categories = $block->getStoreCategories()->getNodes();
        if (Mage::getStoreConfig('custom_menu/general/ajax_load_content')) {
            $_moblieMenuAjaxUrl = str_replace('http:', '', Mage::getUrl('custommenu/ajaxmobilemenucontent'));
            $_menuAjaxUrl = str_replace('http:', '', Mage::getUrl('custommenu/ajaxmenucontent'));
        } else {
            $_moblieMenuAjaxUrl = '';
            $_menuAjaxUrl = '';
        }
        $this->_menuData = array(
            '_block'                        => $block,
            '_categories'                   => $categories,
            '_moblieMenuAjaxUrl'            => $_moblieMenuAjaxUrl,
            '_menuAjaxUrl'                  => $_menuAjaxUrl,
            '_showHomeLink'                 => Mage::getStoreConfig('custom_menu/general/show_home_link'),
            '_popupWidth'                   => Mage::getStoreConfig('custom_menu/popup/width') + 0,
            '_popupTopOffset'               => Mage::getStoreConfig('custom_menu/popup/top_offset') + 0,
            '_popupDelayBeforeDisplaying'   => Mage::getStoreConfig('custom_menu/popup/delay_displaying') + 0,
            '_popupDelayBeforeHiding'       => Mage::getStoreConfig('custom_menu/popup/delay_hiding') + 0,
            '_rtl'                          => Mage::getStoreConfig('custom_menu/general/rtl') + 0,
            '_mobileMenuEnabled'            => Mage::getStoreConfig('custom_menu/general/mobile_menu') + 0,
        );
        return $this->_menuData;
    }

    public function getMobileMenuContent()
    {
        $menuData = Mage::helper('custommenu')->getMenuData();
        extract($menuData);
        if (!$_mobileMenuEnabled) return '';
        // --- Home Link ---
        $homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $homeLinkText       = $this->__('Home');
        $homeLink           = '';
        if ($_showHomeLink) {
            $homeLink = <<<HTML
<div id="menu-mobile-0" class="menu-mobile level0">
    <div class="parentMenu">
        <a href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
    </div>
</div>
HTML;
        }
        // --- Menu Content ---
        $mobileMenuContent = '';
        $mobileMenuContentArray = array();
        foreach ($_categories as $_category) {
            $mobileMenuContentArray[] = $_block->drawCustomMenuMobileItem($_category);
        }
        if (count($mobileMenuContentArray)) {
            $mobileMenuContent = implode("\n", $mobileMenuContentArray);
        }
        // --- Result ---
        $menu = <<<HTML
$homeLink
$mobileMenuContent
<div class="clearBoth"></div>
HTML;
        return $menu;
    }

    public function getMenuContent()
    {
        $menuData = Mage::helper('custommenu')->getMenuData();
        extract($menuData);
        // --- Home Link ---
        $homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $homeLinkText       = $this->__('Home');
        $homeLink           = '';
        if ($_showHomeLink) {
            $homeLink = <<<HTML
<div class="menu">
    <div class="parentMenu menu0">
        <a href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
    </div>
</div>
HTML;
    }
        // --- Menu Content ---
        $menuContent = '';
        $menuContentArray = array();
        foreach ($_categories as $_category) {
            $_block->drawCustomMenuItem($_category);
        }
        $topMenuArray = $_block->getTopMenuArray();
        if (count($topMenuArray)) {
            $topMenuContent = implode("\n", $topMenuArray);
        }
        $popupMenuArray = $_block->getPopupMenuArray();
        if (count($popupMenuArray)) {
            $popupMenuContent = implode("\n", $popupMenuArray);
        }
        // --- Result ---
        $topMenu = <<<HTML
$homeLink
$topMenuContent
<div class="clearBoth"></div>
HTML;
        return array('topMenu' => $topMenu, 'popupMenu' => $popupMenuContent);
    }
}
