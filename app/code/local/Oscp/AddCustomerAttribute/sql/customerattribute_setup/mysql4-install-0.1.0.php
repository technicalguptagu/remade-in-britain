<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('customer', 'store_trading_name', array(
    'group'         => 'General',
    'input'         => 'text',
    'type'          => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'label'         => 'Store/Trading Name',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,    
));

$setup->addAttribute('customer', 'seller_existing_website', array(
    'group'         => 'General',
    'input'         => 'text',
    'type'          => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'label'         => 'Existing website',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,    
));

$setup->addAttribute('customer', 'seller_example_image', array(
    'group'         => 'General',
    'input'         => 'image',
    'type'          => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'label'         => 'Upload example product images',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,    
));

$setup->addAttribute('customer', 'seller_additional_info', array(
    'group'         => 'General',
    'input'         => 'textarea',
    'type'          => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'label'         => 'Additional information...',
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 1,    
));


$installer->endSetup();