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
class Apptha_Marketplace_ProductController extends Mage_Core_Controller_Front_Action 
{
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }
    public function indexAction() {
        if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function newAction() {
        // Check license key
        Mage::helper('marketplace')->checkMarketplaceKey();
        // Initilize customer and seller group id
        $customer_group_id = $seller_group_id = $customer_status = '';
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $seller_group_id = Mage::helper('marketplace')->getGroupId();
        $customer_status = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customer_group_id != $seller_group_id) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Checking whether customer approved or not  
        if ($customer_status != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function newpostAction() {
        // Check license key
        Mage::helper('marketplace')->checkMarketplaceKey();
        // Initilize customer and seller group id
        $customer_group_id = $seller_group_id = $customer_status = '';
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $seller_group_id = Mage::helper('marketplace')->getGroupId();
        $customer_status = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customer_group_id != $seller_group_id) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Checking whether customer approved or not  
        if ($customer_status != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Initializing variables
        $sku = $sku_first = $sku_second = $sku_third = $product_name_trim = $set = $type = $store = $seller_id = $setbanner = '';
        // Getting  product values       
        $type = $this->getRequest()->getPost('type');
        // Attribute set 
        $set = $this->getRequest()->getPost('set');
        $store = $this->getRequest()->getPost('store');
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $seller_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        // Getting group id
        $group_id = Mage::helper('marketplace')->getGroupId();
        // Getting product data from product array
        $product_data = $this->getRequest()->getPost('product');
        
        // Getting product categories from category_ids array    
        $category_ids = $this->getRequest()->getPost('category_ids');
        if (!empty($product_data['name']) && !empty($product_data['description']) && isset($product_data['price']) && isset($product_data['stock_data']['qty']) && !empty($type)) {
            // Initilize product weight
            if ($type == 'simple') {
                if (!isset($product_data['weight'])) {
                    $product_data['weight'] = 0;
                }
            }
            // Assing product short description     
            if (!empty($product_data['short_description'])) {
                $product_data['short_description'] = $product_data['short_description'];
            }
            // Assign create at time
            $created_at = Mage::getModel('core/date')->gmtDate();
            // Generating sku for product
            // Getting seller id
            if (!empty($seller_id)) {
                $sku_first = $seller_id;
            }
            // Getting product first 5 letters
            if (!empty($product_data['name'])) {
                $product_name_trim = $product_data['name'];
                $sku_second = substr($product_name_trim, 0, 5);
            }
            // Generating random value
            $sku_third = rand(1, 100000000000);
            // Assigning sku value
            if (!empty($sku_first) && !empty($sku_second) && !empty($sku_second)) {
                $sku = $sku_first . $sku_second . $sku_third;
            } else {
                $sku = $sku_third;
            }
            // Getting instance for catalog product collection       
            $product = Mage::getModel('catalog/product');
            // Initialize product sku 
            if (!empty($sku)) {
                $product_data['sku'] = $sku;
            }
            // Initialize product attribute set id
            if (isset($set)) {
                $product->setAttributeSetId($set);
            }
            // Initialize product type
            if (isset($type)) {
                $product->setTypeId($type);
            }
            // Initialize product categories
            if (isset($category_ids)) {
                $product->setCategoryIds($category_ids);
            }
            
            // Storing product data's to all store view 
            $product->setStoreId(0);

            // Initialize product create at time
            if (isset($created_at)) {
                $product->setCreatedAt($created_at);
            }
            // Initialize seller id  
            if (isset($seller_id)) {
                $product->setSellerId($seller_id);
            }
            // Initialize group id  
            if (isset($group_id)) {
                $product->setGroupId($group_id);
            }
           //Set product in banner image
            if(isset($product_data['setbanner'])){             
                $product->setSetbanner($product_data['setbanner']);
            }
            $uploadsData        = new Zend_File_Transfer_Adapter_Http();
            $filesDataArray     = $uploadsData->getFileInfo();

               // Checking whether image exist or not    
            if (!empty($filesDataArray)) {
                foreach ($filesDataArray as $key => $value) {
                    // Initilize file name
                    $filename = $key;
                    
                    if(substr($key, 0, 5) == 'image')
                    {        
                    if (isset($filesDataArray[$filename]['name']) && (file_exists($filesDataArray[$filename]['tmp_name']))) {
                        try {
                            $images_path[] = Mage::helper('marketplace/marketplace')->uploadImage($filename,$filesDataArray); 
                        } catch (Exception $e) {
                            // Display error message for images upload   
                            Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                        }
                    }
                    }
                }
            }

            // Adding Product images       
            if (!empty($images_path)) {
                $product->setMediaGallery(array('images' => array(), 'values' => array()));
                foreach($images_path as $value) {
                    $product->addImageToMediaGallery($value, array('image', 'small_image', 'thumbnail'), false, false);
                }
            }


            //  Initialize dispatch event for product prepare  
            Mage::dispatchEvent(
                    'catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest())
            );

            // Adding data to product instanse
            if (!empty($product_data)) {
                $product->addData($product_data);
            }
            // Saving new product      
            try {
                $product->save();

                $product_id = $product->getId();
                
                //Load the product
                
                $product = Mage::getModel('catalog/product')->load($product_id);
                //Get all images
                $mediaGallery = $product->getMediaGallery();
                //If there are images
                if (isset($mediaGallery['images']) && !empty($store)) {
                    //Loop through the images
                    foreach ($mediaGallery['images'] as $image) {
                        //Set the first image as the base image                       
                        $product->setStoreId($store)
                                ->setImage($image['file'])
                                ->setSmallImage($image['file'])
                                ->setThumbnail($image['file']);
                             if(isset($product_data['setbanner'])) {  
                                $product->setBanner($image['file']);
                             }
                        $product->save();
                        //Stop
                        break;
                    }
                }

                //  Initialize product options                            
                if (!empty($product_data['options'])) {
                    $product->setProductOptions($product_data['options']);
                    $product->setCanSaveCustomOptions(1);
                    $product->save();
                }

                // Checking whether image or not
                if (!empty($images_path)) {
                    foreach ($images_path as $delete_image) {
                        // Checking whether image exist or not    
                        if (file_exists($delete_image)) {
                            // Delete images from temporary folder      
                            unlink($delete_image);
                        }
                    }
                }
                
                // Function for adding downloadable product sample and link data
                $download_product_id = $product->getId();
                if($type == 'downloadable' && isset($download_product_id) && isset($store)){
                $this->addDownloadableProductData($download_product_id,$store);
                }

                // Success message redirect to manage product page
                if (Mage::helper('marketplace')->getProductApproval() == 1) {
                    Mage::getSingleton('core/session')->addSuccess($this->__('Your product is added successfully'));


                    if (Mage::getStoreConfig('marketplace/product/addproductemailnotification') == 1) {
                        // Sending email for added new product
                        $template_id = (int) Mage::getStoreConfig('marketplace/product/addproductemailnotificationtemplate');
                        $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
                        $toMailId = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                        $toName = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");

                        // Selecting template id
                        if ($template_id) {
                            $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
                        } else {
                            $emailTemplate = Mage::getModel('core/email_template')
                                    ->loadDefault('marketplace_product_addproductemailnotificationtemplate');
                        }
                        $customer = Mage::getModel('customer/customer')->load($seller_id);
                        $selleremail = $customer->getEmail();
                        $recipient = $toMailId;
                        $sellername = $customer->getName();
                        $productname = $product->getName();
                        $producturl = $product->getProductUrl();
                        $emailTemplate->setSenderName($sellername);
                        $emailTemplate->setSenderEmail($selleremail);
                        $emailTemplateVariables = (array('ownername' => $toName, 'sellername' => $sellername, 'selleremail' => $selleremail, 'productname' => $productname, 'producturl' => $producturl));
                        $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        $emailTemplate->send($recipient, $sellername, $emailTemplateVariables);
                    }
                } else {
                    Mage::getSingleton('core/session')->addSuccess($this->__('Your product is awaiting moderation'));

                    if (Mage::getStoreConfig('marketplace/product/addproductemailnotification') == 1) {
                        // Sending email for added new product
                        $template_id = (int) Mage::getStoreConfig('marketplace/product/addproductapprovalemailnotificationtemplate');
                        $admin_email_id = Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
                        $toMailId = Mage::getStoreConfig("trans_email/ident_$admin_email_id/email");
                        $toName = Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");

                        if ($template_id) {
                            $emailTemplate = Mage::getModel('core/email_template')->load($template_id);
                        } else {
                            $emailTemplate = Mage::getModel('core/email_template')
                                    ->loadDefault('marketplace_product_addproductapprovalemailnotificationtemplate');
                        }
                        $customer = Mage::getModel('customer/customer')->load($seller_id);
                        $selleremail = $customer->getEmail();
                        $recipient = $toMailId;
                        $sellername = $customer->getName();
                        $productname = $product->getName();
                        $producturl = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/edit', array('id' => $product->getId()));

                        $emailTemplate->setSenderName($sellername);
                        $emailTemplate->setSenderEmail($selleremail);
                        $emailTemplateVariables = (array('ownername' => $toName, 'sellername' => $sellername, 'selleremail' => $selleremail, 'productname' => $productname, 'producturl' => $producturl));
                        $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        $emailTemplate->send($recipient, $sellername, $emailTemplateVariables);
                    }
                }
                $this->_redirect('marketplace/product/manage/');
            } catch (Mage_Core_Exception $e) {
                // Error message redirect to create new product page
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('marketplace/product/new/');
            } catch (Exception $e) {
                // Error message redirect to create new product page
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('marketplace/product/new/');
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Please enter all required fields'));
            $this->_redirect('marketplace/product/new');
        }
    }
    public function manageAction() {
        // Check license key
        Mage::helper('marketplace')->checkMarketplaceKey();
        // Initilize customer and seller group id
        $customer_group_id = $seller_group_id = $customer_status = '';
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $seller_group_id = Mage::helper('marketplace')->getGroupId();
        $customer_status = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customer_group_id != $seller_group_id) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page.'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Checking whether customer approved or not
        if ($customer_status != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account.'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function editAction() {
        // Check license key
        Mage::helper('marketplace')->checkMarketplaceKey();
       
        // Initilize customer and seller group id
        $customer_group_id = $seller_group_id = $customer_status = '';
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $seller_group_id = Mage::helper('marketplace')->getGroupId();
        // Checking whether customer and seller group id
        if (!$this->_getSession()->isLoggedIn() && $customer_group_id != $seller_group_id) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page.'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Initilize product id , customer id and seller id
        $entity_id = (int) $this->getRequest()->getParam('id');
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('catalog/product')->load($entity_id);
        $seller_id = $collection->getSellerId();
        // Checking for edit permission
        if ($customer_id != $seller_id) {
            Mage::getSingleton('core/session')->addError($this->__("You don't have enough permission to edit this product details."));
            $this->_redirect('marketplace/product/manage');
            return;
        }
        $customer_status = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        // Checking whether customer approved or not 
        if ($customer_status != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirm your Seller Account'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function editpostAction() {
        // Check license key
        Mage::helper('marketplace')->checkMarketplaceKey();
         Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        // Initilize customer and seller group id
        $customer_group_id = $seller_group_id = $customer_status = '';
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $seller_group_id = Mage::helper('marketplace')->getGroupId();
        $customer_status = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();
        if (!$this->_getSession()->isLoggedIn() && $customer_group_id != $seller_group_id) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Checking whether customer approved or not  
        if ($customer_status != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            $this->_redirect('marketplace/seller/login');
            return;
        }
        // Initialize product data        
        $product_id = $name = $weight  = $description = $short_description = $price = $meta_title = $meta_keyword = $meta_description = $store = '';
        $category_ids = $stock_data = $deleteimages = array();
        $type = $this->getRequest()->getPost('type');
        $product_data = $this->getRequest()->getPost('product');
      
        $product_id = $this->getRequest()->getPost('product_id');
        $category_ids = $this->getRequest()->getPost('category_ids');
        $store = Mage::app()->getStore()->getId();
        $name = $product_data['name'];
        $description = $product_data['description'];
        $short_description = $product_data['short_description'];
        $price = $product_data['price'];
        
        $meta_title = $product_data['meta_title'];
        $meta_keyword = $product_data['meta_keyword'];
        $meta_description = $product_data['meta_description'];
        $qty = $product_data['stock_data']['qty'];
        $is_in_stock = $product_data['stock_data']['is_in_stock'];
        $special_price = $product_data['special_price'];
        $special_from_date = $product_data['special_from_date'];
        $special_to_date = $product_data['special_to_date'];
        $deleteimages = $this->getRequest()->getPost('deleteimages');
        $baseimage = $this->getRequest()->getPost('baseimage');
        // Checking whether select type custom option having values or not        
        if (!empty($product_data['options'])) {
            foreach ($product_data['options'] as $o) {
                if ($o['is_delete'] != 1) {
                    $optionType = $o['type'];
                    if ($optionType == 'drop_down' || $optionType == 'radio' || $optionType == 'checkbox' || $optionType == 'multiple') {
                        if (!isset($o['values']) || empty($o['values'])) {
                            Mage::getSingleton('core/session')->addError($this->__('Custom type option not selected.'));
                            $this->_redirect('marketplace/product/edit/id/' . $product_id);
                            return;
                        }
                    }
                }
            }
        }
        // Initilize product categories
        if (empty($category_ids)) {
            $category_ids = array();
        }
        if (!empty($product_id) && !empty($name) && !empty($description) && !empty($short_description) && isset($price) && isset($qty) && !empty($type)) {
            $product = Mage::getModel("catalog/product")->load($product_id);
            // Initilize product weight
            if ($type == 'simple') {
                if (empty($product_data['weight'])) {
                    $weight = 0;
                } else {
                    $weight = $product_data['weight'];
                }
                $product->setWeight($weight);
            }
            // Initilize product in stock
            $stock_data['is_in_stock'] = Mage::helper('marketplace/marketplace')->productInStock($is_in_stock);
            
            // Initilize product store
            if (isset($store)) {
                $product->setStoreId($store);
            }            

            // Initilize product special price and date
            if (isset($special_price)) {
                $product->setSpecialPrice($special_price);
            }
            if(isset($product_data['setbanner'])){
                $product->setSetbanner($product_data['setbanner']);
                $product->setBanner($baseimage);                
            } else {
                $product->setSetbanner(0);
            }
            
            if (!empty($special_from_date)) {
                $product->setSpecialFromDate($special_from_date);
            }
            if (!empty($special_to_date)) {
                $product->setSpecialToDate($special_to_date);
            }

            // Updating product data
            $product->setName($name);
            $product->setShortDescription($short_description);
            $product->setDescription($description);
            $product->setPrice($price);
            $product->setMetaTitle($meta_title);
            $product->setMetaKeyword($meta_keyword);
            $product->setMetaDescription($meta_description);
            $product->setCategoryIds($category_ids);
            $stock_data['qty'] = $qty;
            $product->setStockData($stock_data);
            $uploadsData        = new Zend_File_Transfer_Adapter_Http();
            $filesDataArray     = $uploadsData->getFileInfo();
         // Checking whether image exist or not    
            if (!empty($filesDataArray)) {
                foreach ($filesDataArray as $key => $value) {
                    // Initilize file name
                    $filename = $key;
                    
                    if(substr($key, 0, 5) == 'image')
                    {        
                    if (isset($filesDataArray[$filename]['name']) && (file_exists($filesDataArray[$filename]['tmp_name']))) {
                        try {
                            $images_path[] = Mage::helper('marketplace/marketplace')->uploadImage($filename,$filesDataArray);
                        } catch (Exception $e) {
                            // Display error message for images upload   
                            Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                        }
                    }
                    }
                }
            }

            // Adding Product images       
            if (!empty($images_path)) {
                $product->setMediaGallery(array('images' => array(), 'values' => array()));
                foreach ($images_path as $value) {
                    $product->addImageToMediaGallery($value, array('image', 'small_image', 'thumbnail'), false, false);
                }
            }

            try {
                // Updating product data 
                $product->save();
                // Setting product base image
                if (!empty($baseimage) && !empty($product_id) && !empty($store)) {                  
                    $product = Mage::getModel("catalog/product")->load($product_id);
                    $product->setStoreId($store)
                            ->setImage($baseimage)
                            ->setSmallImage($baseimage)
                            ->setThumbnail($baseimage); 
                     if(isset($product_data['setbanner'])){
                          $product->setBanner($baseimage);
                   
            }
                    $product->save();
                } else {
                    //Get all images
                    $mediaGallery = $product->getMediaGallery();
                    //If there are images
                    if (isset($mediaGallery['images']) && !empty($product_id)) {
                        $product = Mage::getModel("catalog/product")->load($product_id);
                        //Loop through the images
                        foreach ($mediaGallery['images'] as $image) {
                            //Set the first image as the base image                       
                            $product->setStoreId(0)
                                    ->setImage($image['file'])
                                    ->setSmallImage($image['file'])
                                    ->setThumbnail($image['file']);
                            if(isset($product_data['setbanner'])){
                          $product->setBanner($image['file']);
                   
            }
                            $product->save();
                            //Stop
                            break;
                        }
                    }
                }
                
                // Removing product images
                if (!empty($deleteimages) && !empty($product_id)) {
                    $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
                    foreach ($deleteimages as $image) {
                        $mediaApi->remove($product_id, $image);
                    }
                }

                // Delete existing product custom option      
                if ($product->getOptions()) {
                    foreach ($product->getOptions() as $opt) {
                         Mage::helper('marketplace/marketplace')->deleteCustomOption($opt);
                    }
                    $product->setCanSaveCustomOptions(1);
                    $product->save();
                }

                //  Initialize product options                            
                if (!empty($product_data['options'])) {
                    $product->setProductOptions($product_data['options']);
                    $product->setCanSaveCustomOptions(1);
                    $product->save();
                }

                // Checking whether image or not
                if (!empty($images_path)) {
                    foreach ($images_path as $delete_image) {
                        // Checking whether image exist or not    
                        if (file_exists($delete_image)) {
                            // Delete images from temporary folder      
                            unlink($delete_image);
                        }
                    }
                }
                
                // Function for edit downloadable product sample and link data
                $download_product_id = $product->getId();
                if($type == 'downloadable' && isset($download_product_id) && isset($store)){
                $this->editDownloadableProductData($download_product_id,$store);
                }

                // Success message redirect to manage product page
                Mage::getSingleton('core/session')->addSuccess($this->__('Your product details are updated successfully.'));
                $this->_redirect('marketplace/product/manage/');
            } catch (Mage_Core_Exception $e) {
                // Error message redirect to create new product page
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('marketplace/product/edit/id/' . $product_id);
            } catch (Exception $e) {
                // Error message redirect to create new product page
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('marketplace/product/edit/id/' . $product_id);
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Please enter all required fields'));
            $this->_redirect('marketplace/product/edit/id/' . $product_id);
        }
    }

    public function deleteAction() {

        //check license key
        Mage::helper('marketplace')->checkMarketplaceKey();

        // Initilize customer and seller group id
        $customer_group_id = $seller_group_id = $customer_status = '';
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $seller_group_id = Mage::helper('marketplace')->getGroupId();
        $customer_status = Mage::getSingleton('customer/session')->getCustomer()->getCustomerstatus();

        if (!$this->_getSession()->isLoggedIn() && $customer_group_id != $seller_group_id) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('marketplace/seller/login');
            return false;
        }
        // Checking whether customer approved or not  
        if ($customer_status != 1) {
            Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            $this->_redirect('marketplace/seller/login');
            return false;
        }
        $this->loadLayout();
        $this->renderLayout();
        $entity_id = (int) $this->getRequest()->getParam('id');

        //delete collection
        Mage::register('isSecureArea', true); /* set secure admin area */
        Mage::getModel('catalog/product')->setId($entity_id)->delete();
        Mage::unregister('isSecureArea'); /* un set secure admin area */
        Mage::getSingleton('core/session')->addSuccess($this->__("Product Deleted Successfully"));
        $this->_redirect('*/product/manage/');
        return true;
    }
    
        public function addDownloadableProductData($download_product_id,$store){
        // Initilize downloadable product sample and link files
        $sample_tpath = $link_tpath = $slink_tpath = array();
        $uploadsData        = new Zend_File_Transfer_Adapter_Http();
        $filesDataArray     = $uploadsData->getFileInfo();
        foreach($filesDataArray as $key => $result)
        {       
        if (isset($filesDataArray[$key]['name']) && (file_exists($filesDataArray[$key]['tmp_name']))) {   
            
        $type = '';    
        if(substr($key, 0, 5) == 'sampl')
        {
        $tmpPath = Mage_Downloadable_Model_Sample::getBaseTmpPath();
        $type = 'samples';                         
        }
        elseif(substr($key, 0, 5) == 'links') {
            $tmpPath = Mage_Downloadable_Model_Link::getBaseTmpPath();
             $type = 'links'; 
        } elseif(substr($key, 0, 5) == 'l_sam') {
            $tmpPath = Mage_Downloadable_Model_Link::getBaseSampleTmpPath();
             $type = 'link_samples'; 
        }       
        
        if($type == 'samples' || $type == 'links' || $type == 'link_samples')
        {
           $result = array(); 
        try {
             // Initilize validate flag
        $validate_flag = 0;
        // Getting uploaded file extension type
        $uploader_extension = pathinfo($filesDataArray[$key]['name'], PATHINFO_EXTENSION);
        $validate_image = array('jpg', 'jpeg', 'gif', 'png');
        if(in_array($uploader_extension,$validate_image)){
        $img_size = getimagesize($filesDataArray[$key]['tmp_name']);
        if(!$img_size){
        $validate_flag = 1;
        }
        }

        // Disallow php file for downloadable product
        if($uploader_extension != 'php' && $validate_flag == 0)
        {
            $uploader = new Varien_File_Uploader($key);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
           $result = $uploader->save($tmpPath);
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] = str_replace(DS, "/", $result['path']);            
            
            if (isset($result['file'])) {
                $fullPath = rtrim($tmpPath, DS) . DS . ltrim($result['file'], DS);
                Mage::helper('core/file_storage_database')->saveFile($fullPath);
            }
            
            $file_name = $file_path = $file_size = $sample_no = '';
            $file_name = $uploader->getUploadedFileName();
            $file_path = ltrim($result['file'], DS);
            $file_size =  $result['size'];           
            
        if($type == 'samples')
        {  
            $sample_no = substr($key, 7); 
            $sample_tpath[$sample_no] = array(
                'file' => $file_path,
                'name' => $file_name,
                'size' => $file_size,
                'status' => 'new'
             );                        
        }
        elseif($type == 'links') {                                
            
            $sample_no = substr($key, 6); 
            $link_tpath[$sample_no] = array(
                'file' => $file_path,
                'name' => $file_name,
                'size' => $file_size,
                'status' => 'new'
             ); 
        } elseif($type == 'link_samples') {                            
            
            $sample_no = substr($key, 9); 
            $slink_tpath[$sample_no] = array(
                'file' => $file_path,
                'name' => $file_name,
                'size' => $file_size,
                'status' => 'new'
             );
        } 
          }else{
         Mage::getSingleton('core/session')->addError($this->__('Disallowed file type.'));
        }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
        }               
        }
        }
        }            
        
        // Initilize Downloadable product data  
        $downloadable_data = $this->getRequest()->getPost('downloadable');        
       
      try{    
         
         // Storing Downloadable product sample data
         if (isset($downloadable_data['sample'])) {                
                foreach ($downloadable_data['sample'] as $sampleItem) {                
             $sample_id = ''; 
             $sample = array();
             $sample_id =  $sampleItem['sample_id'];             
             $sample[] = $sample_tpath[$sample_id];       
         $sampleModel = Mage::getModel('downloadable/sample');                 
                         $sampleModel->setData($sample)
                             ->setSampleType($sampleItem['type'])
                             ->setProductId($download_product_id)
                             ->setStoreId(0)
                             ->setWebsiteIds(array(Mage::app()->getStore($store)->getWebsiteId()))
                             ->setTitle($sampleItem['title'])
                             ->setDefaultTitle(false)                             
                             ->setSortOrder($sampleItem['sort_order']);                         
                         if($sampleItem['type'] == 'url'){ 
                         $sampleModel ->setSampleUrl($sampleItem['sample_url']); 
                         }                      
                         if(!empty($sample_tpath[$sample_id])){                         
                         if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                             $sampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                 Mage_Downloadable_Model_Sample::getBaseTmpPath(),
                                Mage_Downloadable_Model_Sample::getBasePath(),
                                 $sample
                            );      
                          }                           
                            $sampleModel->setSampleFile($sampleFileName);                       
                         }   
                         
                          Mage::helper('marketplace/marketplace')->saveDownLoadLink($sampleModel);
                }
                   }
                
             // Storing Downloadable product sample data      
             if (isset($downloadable_data['link'])) {              
             foreach ($downloadable_data['link'] as $linkItem) {
             $link_id = ''; 
             $linkfile = $samplefile = array();
             $link_id =  $linkItem['link_id'];             
             $linkfile[] = $link_tpath[$link_id];             
             $samplefile[] = $slink_tpath[$link_id];                      
                      $linkModel = Mage::getModel('downloadable/link')
                             ->setData($linkfile)
                             ->setLinkType($linkItem['type'])
                             ->setProductId($download_product_id)
                             ->setWebsiteIds(array(Mage::app()->getStore($store)->getWebsiteId())) 
                             ->setStoreId(0)
                             ->setSortOrder($linkItem['sort_order'])
                             ->setTitle($linkItem['title'])                              
                             ->setIsShareable($linkItem['is_shareable']);                      
                              if($linkItem['type'] == 'url'){                                  
                              $linkModel->setLinkUrl($linkItem['link_url']);
                              }                     
                             $linkModel->setPrice($linkItem['price']);                       
                             $linkModel->setNumberOfDownloads($linkItem['number_of_downloads']);                                                 
                             if(isset($linkItem['sample']['type'])){
                             if($linkItem['sample']['type'] == 'url'){                                  
                               $linkModel->setSampleUrl($linkItem['sample']['url']);
                             }                   
                             $linkModel->setSampleType($linkItem['sample']['type']);
                             } 
                             $sampleFile = '';
                             $sampleFile = Zend_Json::decode(json_encode($samplefile));                           
                             if(!empty($link_tpath[$link_id]) && $linkItem['type'] == 'file')
                             {    
                             $linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseTmpPath(),
                                 Mage_Downloadable_Model_Link::getBasePath(),
                                $linkfile
                             );                             
                             $linkModel->setLinkFile($linkFileName);
                             }                        
                             if(!empty($slink_tpath[$link_id]) && isset($sampleFile) && $linkItem['sample']['type'] = 'file')
                             { 
                             $linkSampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                 Mage_Downloadable_Model_Link::getBaseSampleTmpPath(),
                                 Mage_Downloadable_Model_Link::getBaseSamplePath(),
                                 $sampleFile
                             );
                             $linkModel->setSampleFile($linkSampleFileName);
                             }                        
                         Mage::helper('marketplace/marketplace')->saveDownLoadLink($linkModel);                  }                
             } 
        }catch(Exception $e){ Mage::getSingleton('core/session')->addError($this->__($e->getMessage())); }
        
    }
       // Edit downloable product sample and link data   
       public function editDownloadableProductData($download_product_id,$store){
           
        // Initilize downloadable product sample and link data   
        $sample_tpath =  $link_tpath = $slink_tpath = array();
        $uploadsData        = new Zend_File_Transfer_Adapter_Http();
        $filesDataArray     = $uploadsData->getFileInfo();
        foreach($filesDataArray as $key => $result)
        {       
        if (isset($filesDataArray[$key]['name']) && (file_exists($filesDataArray[$key]['tmp_name']))) {               
        $type = '';    
        if(substr($key, 0, 5) == 'sampl')
        {
        $tmpPath = Mage_Downloadable_Model_Sample::getBaseTmpPath();
        $type = 'samples';                         
        }
        elseif(substr($key, 0, 5) == 'links') {
            $tmpPath = Mage_Downloadable_Model_Link::getBaseTmpPath();
             $type = 'links'; 
        } elseif(substr($key, 0, 5) == 'l_sam') {
            $tmpPath = Mage_Downloadable_Model_Link::getBaseSampleTmpPath();
             $type = 'link_samples'; 
        }       
        if($type == 'samples' || $type == 'links' || $type == 'link_samples')
        {
           $result = array(); 
        try {
            // Initilize validate flag
        $validate_flag = 0;
        // Getting uploaded file extension type
        $uploader_extension = pathinfo($filesDataArray[$key]['name'], PATHINFO_EXTENSION);
        $validate_image = array('jpg', 'jpeg', 'gif', 'png');
        if(in_array($uploader_extension,$validate_image)){
        $img_size = getimagesize($filesDataArray[$key]['tmp_name']);
        if(!$img_size){
        $validate_flag = 1;
        }
        }

        // Disallow php file for downloadable product
        if($uploader_extension != 'php' && $validate_flag == 0)
        {
            $uploader = new Varien_File_Uploader($key);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($tmpPath);
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] = str_replace(DS, "/", $result['path']);         
            if (isset($result['file'])) {
                $fullPath = rtrim($tmpPath, DS) . DS . ltrim($result['file'], DS);
                Mage::helper('core/file_storage_database')->saveFile($fullPath);
            }               
            $file_name = $file_path = $file_size = $sample_no = '';
            $file_name = $uploader->getUploadedFileName();
            $file_path = ltrim($result['file'], DS);
            $file_size =  $result['size'];                       
        if($type == 'samples')
        {  
            $sample_no = substr($key, 7); 
            $sample_tpath[$sample_no] = array(
                'file' => $file_path,
                'name' => $file_name,
                'size' => $file_size,
                'status' => 'new'
             );                        
        }
        elseif($type == 'links') {   
            $sample_no = substr($key, 6); 
            $link_tpath[$sample_no] = array(
                'file' => $file_path,
                'name' => $file_name,
                'size' => $file_size,
                'status' => 'new'
             ); 
        } elseif($type == 'link_samples') {           
            $sample_no = substr($key, 9); 
            $slink_tpath[$sample_no] = array(
                'file' => $file_path,
                'name' => $file_name,
                'size' => $file_size,
                'status' => 'new'
             );
        }
        }else{
         Mage::getSingleton('core/session')->addError($this->__('Disallowed file type.'));
        }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
        }               
        }
        }
        }    
              
        // Edit downloadable product sample data
      try{          
          
        // Getting downloadable product sample collection  
        $downloadable_sample =  Mage::getModel('downloadable/sample')->getCollection()
        ->addProductToFilter($download_product_id)
        ->addTitleToResult($store);        
          $sample_delete_items = array();
          
         // Removing all sample data
          foreach($downloadable_sample as $sample_delete)
          {
          $sample_delete_items[] = $sample_delete->getSampleId();
          }          
          if(!empty($sample_delete_items)){
          Mage::getResourceModel('downloadable/sample')->deleteItems($sample_delete_items); 
          }          
        // Getting downloadable product link collection  
        $downloadable_link =  Mage::getModel('downloadable/link')->getCollection()
        ->addProductToFilter($download_product_id)
        ->addTitleToResult($store);    
        
          // Removing all link data
        $link_delete_items = array();
        foreach($downloadable_link as $link_delete){
        $link_delete_items[] =  $link_delete->getLinkId();            
        }       
        if(!empty($link_delete_items)){
        Mage::getResourceModel('downloadable/link')->deleteItems($link_delete_items);     
        }          
        
         // Initilize downloadable product data
         $downloadable_data = $this->getRequest()->getPost('downloadable');         
         if(isset($downloadable_data['sample'])) {             
             foreach($downloadable_data['sample'] as $sampleItem) {                       
             $sample_id = ''; 
             $sample = array();
             $sample_id =  $sampleItem['sample_id']; 
             if(empty($sample_id) && isset($sample_tpath[$sample_id])){
                $sample[] = $sample_tpath[$sample_id]; 
             }
             if(isset($sample)){
             $sampleModel = Mage::getModel('downloadable/sample');                 
                         $sampleModel->setData($sample)
                             ->setSampleType($sampleItem['type'])
                             ->setProductId($download_product_id)
                             ->setStoreId($store)
                             ->setWebsiteIds(array(Mage::app()->getStore($store)->getWebsiteId()))
                             ->setTitle($sampleItem['title'])
                             ->setDefaultTitle(false)                             
                             ->setSortOrder($sampleItem['sort_order']);                         
                         if($sampleItem['type'] == 'url'){ 
                            $sampleModel ->setSampleUrl($sampleItem['sample_url']); 
                         }                               
                         if(!empty($sample_tpath[$sample_id]) && $sampleItem['type'] == 'file'){                      
                         if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                             $sampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                 Mage_Downloadable_Model_Sample::getBaseTmpPath(),
                                Mage_Downloadable_Model_Sample::getBasePath(),
                                 $sample
                            );      
                          }
                          $sampleModel->setSampleFile($sampleFileName);  
                         }
                         else {                             
                         if(!empty($sampleItem['sample_file']))
                         {
                         $sampleFileName    = $sampleItem['sample_file'];
                         $sampleModel->setSampleFile($sampleFileName);                        
                         }                            
                         }         
                        Mage::helper('marketplace/marketplace')->saveDownLoadLink($sampleModel);
                }
            }
         }
             // Editing downloadable product link data   
             if (isset($downloadable_data['link'])){                   
             foreach ($downloadable_data['link'] as $linkItem) {
             $link_id = ''; 
             $linkfile = $samplefile = array();
             $link_id =  $linkItem['link_id']; 
             if(isset($link_tpath[$link_id])){
                $linkfile[] = $link_tpath[$link_id]; 
                   
             }
             if(isset($slink_tpath[$link_id])){
                 $samplefile[] = $slink_tpath[$link_id];        
             }
                      $linkModel = Mage::getModel('downloadable/link')
                             ->setData($linkfile)
                             ->setLinkType($linkItem['type'])
                             ->setProductId($download_product_id)
                             ->setWebsiteIds(array(Mage::app()->getStore($store)->getWebsiteId())) 
                             ->setStoreId(0)
                             ->setSortOrder($linkItem['sort_order'])
                             ->setTitle($linkItem['title'])                              
                             ->setIsShareable($linkItem['is_shareable']);                            
                              if($linkItem['type'] == 'url'){                                  
                                $linkModel->setLinkUrl($linkItem['link_url']);
                              }                     
                             $linkModel->setPrice($linkItem['price']);                                             
                             $linkModel->setNumberOfDownloads($linkItem['number_of_downloads']);                                               
                              if(isset($linkItem['sample']['type']) && $linkItem['sample']['type'] == 'url'){                                  
                                $linkModel->setSampleUrl($linkItem['sample']['url']);
                                $linkModel->setSampleType($linkItem['sample']['type']);
                              }                      
                             
                             $sampleFile = '';
                             $sampleFile = Zend_Json::decode(json_encode($samplefile));              
                             if(!empty($link_tpath[$link_id]) && $linkItem['type'] == 'file')
                             {    
                             $linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseTmpPath(),
                                 Mage_Downloadable_Model_Link::getBasePath(),
                                $linkfile
                             );                             
                             $linkModel->setLinkFile($linkFileName);
                             }
                             else {                                 
                             if(!empty($linkItem['link_file']))
                             {
                             $linkFileName    = $linkItem['link_file'];
                             $linkModel->setLinkFile($linkFileName); 
                             }                              
                             }                    
                             if(!empty($slink_tpath[$link_id]) && isset($sampleFile) && $linkItem['sample']['type'] = 'file')
                             { 
                             $linkSampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                 Mage_Downloadable_Model_Link::getBaseSampleTmpPath(),
                                 Mage_Downloadable_Model_Link::getBaseSamplePath(),
                                 $sampleFile
                             );
                             $linkModel->setSampleFile($linkSampleFileName);
                             }
                             else{                                 
                         if(!empty($linkItem['link_sample_file']))
                         {
                         $linkSampleFileName    = $linkItem['link_sample_file'];
                          $linkModel->setSampleFile($linkSampleFileName);
                         }                                
                         }
                         Mage::helper('marketplace/marketplace')->saveDownLoadLink($linkModel);                        
                      }                
               } 
        }catch(Exception $e){ 
            Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));      
        }                        
       }
}