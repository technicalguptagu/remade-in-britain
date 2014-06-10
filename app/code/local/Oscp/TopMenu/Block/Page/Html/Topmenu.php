<?php
/**
 * @category    Oscp
 * @package     Oscp_TopMenu
 */
class Oscp_TopMenu_Block_Page_Html_Topmenu extends Mage_Page_Block_Html_Topmenu
{
    public function getMobiHtml($outermostClass = '', $childrenWrapClass = '')
    {
        Mage::dispatchEvent('page_block_html_topmenu_gethtml_before', array(
            'menu' => $this->_menu,
            'block' => $this
        ));

        $this->_menu->setOutermostClass($outermostClass);
        $this->_menu->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getMobiHtml($this->_menu, $childrenWrapClass);

        Mage::dispatchEvent('page_block_html_topmenu_gethtml_after', array(
            'menu' => $this->_menu,
            'html' => $html
        ));

        return $html;
    }
     protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

		/*if($childLevel==1){
				$_num=-1;
				}	*/	

      
        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }
			$units ="my-units";
			

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName()) . '</span></a>';

		
            if ($child->hasChildren()) {
                if (!empty($childrenWrapClass)) {
                    $html .= '<div class="' . $childrenWrapClass . '">';
                }

                $_childId = (int) str_replace('category-node-', '', $child->getId());
                $_categoryCollection = $this->getCategoryAttribute($_childId);
				if(($_categoryCollection->getMenuImage())&&($_categoryCollection->getMenuLink())):
					$_subCategoryBox = " menu-left";
				else:
					$_subCategoryBox = "";
				endif;

                $html .= '<div class="menu-bootm">';
				if ($childLevel == 0 ){
				//$html .= '<div class="category-descr">';
				//$html .= '<div class="catagory"><h3>'. $this->escapeHtml($child->getName()) . '</h3></div>';	
				//$html .= '<div class="description">'.$_categoryCollection->getDescription() . '</div>';	
				//$html .= '</div>';
                $html .= '<div class="sub-categories'.$_subCategoryBox.'">';
				}
                $html .= '<ul class="' . $units.'">';
				
				$html .= $this->_getHtml($child, $childrenWrapClass);
				$html .= '</ul>';

                if ($childLevel == 0 ){
                $html .= '</div>';
				}

                if($child->getLevel()==0):
					if(($_categoryCollection->getMenuImage())&&($_categoryCollection->getMenuLink())):
                        $html .= '<div class="menu-right">';
						$html .= '<div class="catagory-image"><img src="'.Mage::getBaseUrl('media').'catalog/category/'.$_categoryCollection->getMenuImage() . '" alt="" /></div>';
						$html .= '<div class="static-connect">';                       
						$html .= '<div class="textes">'.$_categoryCollection->getMenuText() .'</div>';
				        $html .= '<div class="text-link"><a href="'.$_categoryCollection->getMenuLink().'">Click here to view  &#187;</a></div>';
                        $html .= '</div></div>';
                    endif; 
				endif;
           
				 $html .= '</div>';

            }
            $html .= '</li>';
			 $counter++;
        }

        return $html;
    }
	 protected function _getMobiHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName()) . '</span></a>';

            if ($child->hasChildren()) {
                if (!empty($childrenWrapClass)) {
                    $html .= '<div class="' . $childrenWrapClass . '">';
                }
                if($childLevel == 0){
				$html .='<span class="opener">&nbsp;</span>';
				}
                $html .= '<ul class="level' . $childLevel . ' clearer">';
                $html .= $this->_getHtml($child, $childrenWrapClass);
                $html .= '</ul>';

                if (!empty($childrenWrapClass)) {
                    $html .= '</div>';
                }
            }
            $html .= '</li>';

            $counter++;
        }

        return $html;
    }

    public function getCategoryAttribute($id)
    {		
		$_data = Mage::getModel('catalog/category')->load($id);
        return $_data;	
	}


}
