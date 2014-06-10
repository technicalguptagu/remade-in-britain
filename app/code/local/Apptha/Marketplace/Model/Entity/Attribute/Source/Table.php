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
class Apptha_Marketplace_Model_Entity_Attribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    //Add approve and disapprove action in grid
   public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $withEmpty = true;
        $defaultValues = false;
        $options[] = array(
                'value' => 0,
                'label' => 'Pending'
        );
        $options[] = array(
                'value' => 1,
                'label' => 'Approve'
        );
        $options[] = array(
                'value' => 2,
                'label' => 'Disapprove'
        );
        return $options;
    }
} 
