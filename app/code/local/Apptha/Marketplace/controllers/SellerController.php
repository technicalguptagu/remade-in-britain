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
 * @Modified Date : March 26,2014
 * */
/*
 * ********************************************************* */
class Apptha_Marketplace_SellerController extends Mage_Core_Controller_Front_Action{
        /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('loginPost', 'createpost');

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    protected function _isVatValidationEnabled($store = null)
    {
        return Mage::helper('customer/address')->isVatValidationEnabled($store);
    }
    //Function to display seller login and registration page 
    public function indexAction() {
        
          if(Mage::helper('marketplace')->checkMarketplaceKey()!= ''){
        $msg = Mage::helper('marketplace')->checkMarketplaceKey();
        Mage::app()->getResponse()->setBody($msg);  
        return;
        } else {
            if (!$this->_getSession()->isLoggedIn()) {
            Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
            $this->_redirect('*/*/login');
            return;
        }
	  $this->loadLayout();
	  $this->renderLayout();
        }  
    }
    //Function to display login page
    public function loginAction() {
       if(Mage::helper('marketplace')->checkMarketplaceKey()!= ''){
        $msg = Mage::helper('marketplace')->checkMarketplaceKey();
        Mage::app()->getResponse()->setBody($msg);  
        return;
        } else {
      if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('marketplace/seller/dashboard');
            return;
        }
	  $this->loadLayout();
	 $this->renderLayout();	  
    }
    }
    //Function to post login page data's
    public function loginPostAction()
    {
    if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('marketplace/seller/dashboard');
            return;
        }
        $session = $this->_getSession();
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = Mage::getModel('customer/customer');
                    $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                             ->loadByEmail($login['username']);
                    $customer_groupid = $customer->getGroupId();
                    $groupId = Mage::helper('marketplace')->getGroupId();
                    if($customer_groupid != $groupId ){  
                        Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
                        $this->_redirect('*/*/login');
                        return;
                    }
                  $customer_status = $customer->getCustomerstatus();
                    if($customer_status == 2 || $customer_status == 0){
                        Mage::getSingleton('core/session')->addError($this->__('Admin Approval is required for Seller Account'));
                        $this->_redirect('*/*/login');
                        return;
                    }
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);                        
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                }
            } else {
                $session->addError($this->__('Login and password are must.'));
            }
        }         
         $this->_redirect('marketplace/seller/dashboard');
    }
        //Function to display registration page
        public function createAction() {
            if(Mage::helper('marketplace')->checkMarketplaceKey()!= ''){
        $msg = Mage::helper('marketplace')->checkMarketplaceKey();
        Mage::app()->getResponse()->setBody($msg);  
        return;
        } else {
              $this->loadLayout();
               $this->renderLayout();
        }
        }
        //Function to post the registration page data's
        public function createPostAction()
        {
            $admin_approval = Mage::getStoreConfig('marketplace/admin_approval_seller_registration/need_approval');

			//BOF developer 06 'check for file existance, image's validation , image resizing and upload image to respective directories'.
			$uploadsData = new Zend_File_Transfer_Adapter_Http();
            $filesDataArray = $uploadsData->getFileInfo();						
			$store_logo         = $filesDataArray['example_image']['name'];
			$basedir            = Mage::getBaseDir('media');
			$file               = new Varien_Io_File();
			//create a folder to save the logo and banner images in media folder
                if (!is_dir($basedir . '/sellerimage')){
                   $file->mkdir($basedir . '/sellerimage');
                }
				if (isset($filesDataArray['example_image']['name']) && (file_exists($filesDataArray['example_image']['tmp_name']))) {
                    try {
                        $uploader = new Varien_File_Uploader($filesDataArray['example_image']);     
                                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                                    $uploader->addValidateCallback('catalog_product_image',
                                    Mage::helper('catalog/image'), 'validateUploadFile');                      
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $path = $basedir . DS . 'sellerimage';                            
                        $uploader->save($path, $filesDataArray['example_image']['name']);                            
                        $images_path = $uploader->getUploadedFileName();
                                 } catch (Exception $e) {
                                     // Display error message for images upload   
                                     Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                                 }
                       if (!is_dir($basedir . '/marketplace/resized')){
                            $file->mkdir($basedir . '/marketplace/resized');
                        }
                        $imageUrl_logo = Mage::getBaseDir('media'). DS .'sellerimage'. DS .$images_path;     
                        $imageResized_logo = Mage::getBaseDir('media'). DS .'marketplace'. DS .'resized'. DS .$images_path;
                       if(file_exists($imageUrl_logo) && !file_exists($imageResized_logo)) 
                       {            
                           $imageObj = new Varien_Image($imageUrl_logo);
                           $imageObj->constrainOnly(TRUE);
                           $imageObj->keepAspectRatio(False);
                           $imageObj->keepFrame(FALSE);
                           $imageObj->resize(124,124);
                           $imageObj->save($imageResized_logo); 
                       }
                }
			//EOF developer 06

            $session = $this->_getSession();
            if ($session->isLoggedIn()) {
                $this->_redirect('*/*/');
                return;
            }
            $session->setEscapeMessages(true); // prevent XSS injection in user input
            if ($this->getRequest()->isPost()) {
                $errors = array();
                if (!$customer = Mage::registry('current_customer')) {
                   $customer = Mage::getModel('customer/customer')->setId(null); 
                }
                $groupId = Mage::helper('marketplace')->getGroupId();
                $customer->setGroupId($groupId);

                if($admin_approval == 1){
               $customer->setCustomerstatus('0');
                } else {
                    $customer->setCustomerstatus('1'); 
                }
                /* @var $customerForm Mage_Customer_Model_Form */
                $customerForm = Mage::getModel('customer/form');
                $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);
                $customerData = $customerForm->extractData($this->getRequest());
                $customer->getGroupId();           
                if ($this->getRequest()->getPost('create_address')) {
                    /* @var $address Mage_Customer_Model_Address */
                    $address = Mage::getModel('customer/address');
                    /* @var $addressForm Mage_Customer_Model_Form */
                    $addressForm = Mage::getModel('customer/form');
                    $addressForm->setFormCode('customer_register_address')
                        ->setEntity($address);
                    $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
                    $addressErrors  = $addressForm->validateData($addressData);
                    if ($addressErrors === true) {
                            $address->setId(null)
                            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                            $addressForm->compactData($addressData);
                             $customer->addAddress($address);

                        $addressErrors = $address->validate();
                        if (is_array($addressErrors)) {
                            $errors = array_merge($errors, $addressErrors);
                        }
                    } else {
                        $errors = array_merge($errors, $addressErrors);
                    }
                }
                try {             
                    $customerErrors = $customerForm->validateData($customerData);
                    if ($customerErrors !== true) {
                        $errors = array_merge($customerErrors, $errors);
                    } else {
						$customerForm->compactData($customerData);
                        $customer->setPassword($this->getRequest()->getPost('password'));
                        $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                        $customerErrors = $customer->validate();
                        if (is_array($customerErrors)) {
                            $errors = array_merge($customerErrors, $errors);
                        }
                    }
                    $validationResult = count($errors) == 0;                          
                    if (true === $validationResult) {
                       $customer_id = $customer->save()->getId();
                     if($admin_approval ==1){                    
                            Mage::getModel('marketplace/sellerprofile')->adminApproval($customer_id);  
                            Mage::dispatchEvent('customer_register_success',array('account_controller' => $this, 'customer' => $customer));                     
                            Mage::getSingleton('core/session')->addSuccess($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
                            $this->_redirect('*/*/login');
                            return;
                       }
                       else
                       {
                           
                           Mage::getModel('marketplace/sellerprofile')->newSeller($customer_id); 
                            Mage::dispatchEvent('customer_register_success',
                            array('account_controller' => $this, 'customer' => $customer)
                        ); 
                            $session->setCustomerAsLoggedIn($customer);
                            $session->renewSession();
                            $url = $this->_welcomeCustomer($customer);
                            $this->_redirectSuccess($url);
                       }
                    } else {
                        $session->setCustomerFormData($this->getRequest()->getPost());
                        if (is_array($errors)) {
                            foreach ($errors as $errorMessage) {
                                $session->addError($errorMessage);
                            }
                        } else {
                            $session->addError($this->__('Invalid customer data'));
                        }
                    }
                }  catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = Mage::getUrl('customer/account/forgotpassword');              
                Mage::getSingleton('core/session')->addError($this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url));
               $this->_redirect('*/*/login');              
            } else {
                $message = $e->getMessage();
            }
            $session->addError($message);
                } catch (Exception $e) {
                    $session->setCustomerFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('Customer details not saved.'));
                }
            }
            //Mage::getSingleton('core/session')->addError($message);
            $this->_redirectError(Mage::getUrl('*/*/login', array('_secure' => true)));
        }
            //Function to display welcome message
            protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
                {
                    $this->_getSession()->addSuccess(
                        $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
                    );
                    if ($this->_isVatValidationEnabled()) {
                        // Show corresponding VAT message to customer
                        $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();
                        $userPrompt = '';
                        switch ($configAddressType) {
                            case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                                $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
                                break;
                            default:
                                $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
                        }
                        $this->_getSession()->addSuccess($userPrompt);
                    }
                    $customer->sendNewAccountEmail(
                        $isJustConfirmed ? 'confirmed' : 'registered','',
                        Mage::app()->getStore()->getId()
                    );
                    $successUrl = Mage::getUrl('*/*/index', array('_secure'=>true));
                    if ($this->_getSession()->getBeforeAuthUrl()) {
                        $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
                    }
                    return $successUrl;
                }
        //Function to display add profile form
        function addprofileAction(){
            Mage::helper('marketplace')->checkMarketplaceKey();
           if (!$this->_getSession()->isLoggedIn()) {
                Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
                $this->_redirect('marketplace/seller/login');
                return;
            }
             $this->loadLayout();
             $this->renderLayout();
        }
        function saveprofileAction(){
                $data = $this->getRequest()->getPost(); 
                $seller_id = $store_name = $store_name = $store_logo = $description = $meta_keyword = $meta_keyword = $meta_description = '';
                $bank_payment = $paypal_id = $remove = $contact = $show_profile = '';
                $uploadsData        = new Zend_File_Transfer_Adapter_Http();
                $filesDataArray     = $uploadsData->getFileInfo();
                $seller_id          = $data['seller_id'];
                $store_name         = $data['store_name'];
				//BOF developer 06 'fetch existing website field and additional information'
                $website         = $data['exist_website'];
                $additional_info      = $data['additional_info'];
				//EOF developer 06
                $store_logo         = $filesDataArray['store_logo']['name'];
                //$country            = $data['country'];
                $description        = $data['description'];
                $meta_keyword       = $data['meta_keyword'];
                $meta_description   = $data['meta_description'];
                //$twitter_id         = $data['twitter_id'];
                //$facebook_id        = $data['facebook_id'];        
                $bank_payment       = $data['bank_payment'];
                $paypal_id          = $data['paypal_id'];
                if(isset($data['remove'])){
                    $remove             = $data['remove'];  
                }
                $contact            = $data['contact'];
                if(isset($data['show_profile'])){
                    $show_profile       = $data['show_profile'];
                }
                $basedir            = Mage::getBaseDir('media');
                $file               = new Varien_Io_File();

                //create a folder to save the logo and banner images in media folder
                if (!is_dir($basedir . '/sellerimage')){
                   $file->mkdir($basedir . '/sellerimage');
                }     
                if (isset($filesDataArray['store_logo']['name']) && (file_exists($filesDataArray['store_logo']['tmp_name']))) {                    
                    try {
                        $uploader = new Varien_File_Uploader($filesDataArray['store_logo']);     
                                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                                    $uploader->addValidateCallback('catalog_product_image',
                                    Mage::helper('catalog/image'), 'validateUploadFile');                      
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        $path = $basedir . DS . 'sellerimage';                            
                        $uploader->save($path, $filesDataArray['store_logo']['name']);                            
                        $images_path = $uploader->getUploadedFileName();
                                 } catch (Exception $e) {
                                     // Display error message for images upload   
                                     Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                                 }
                       if (!is_dir($basedir . '/marketplace/resized')){
                            $file->mkdir($basedir . '/marketplace/resized');
                        } 
                        $imageUrl_logo = Mage::getBaseDir('media'). DS .'sellerimage'. DS .$images_path;     
                        $imageResized_logo = Mage::getBaseDir('media'). DS .'marketplace'. DS .'resized'. DS .$images_path;
                       if(file_exists($imageUrl_logo) && !file_exists($imageResized_logo)) 
                       {            
                           $imageObj = new Varien_Image($imageUrl_logo);
                           $imageObj->constrainOnly(TRUE);
                           $imageObj->keepAspectRatio(False);
                           $imageObj->keepFrame(FALSE);
                           $imageObj->resize(124,124);
                           $imageObj->save($imageResized_logo); 
                       }
                }          
                $collection = Mage::getModel('marketplace/sellerprofile')->load($seller_id, 'seller_id');
                $getId      = $collection->getId();
                 try {
                    if($getId){                     
                    //Update form input data in database
                    $collection = Mage::getModel('marketplace/sellerprofile')->load($seller_id, 'seller_id');
                    $collection->setSellerId($seller_id);
                    $collection->setStoreTitle($store_name);
					//BOF developer 06 'set existing website field and additional information to collection'
                    $collection->setExistWebsite($website);
                    $collection->setAdditionalInfo($additional_info);
					//EOF developer 06
                    if (!empty($store_logo)) {
                        $collection->setStoreLogo($images_path);
                    }
                    if($remove==1){
                        $collection->setStoreLogo('');
                    }
                   // $collection->setCountry($country);
                    $collection->setDescription($description);
                    $collection->setMetaKeyword($meta_keyword);
                    $collection->setMetaDescription($meta_description);
                   // $collection->setTwitterId($twitter_id);
                    //$collection->setFacebookId($facebook_id);
                    $collection->setContact($contact);
                    $collection->setBankPayment($bank_payment);
                    $collection->setPaypalId($paypal_id);
                    if($show_profile){
                        $collection->setShowProfile($show_profile);}
                    else {
                        $collection->setShowProfile('');
                    }
                    $collection->save();

                    $target_path        = 'marketplace/seller/displayseller/id/' . $seller_id;
                    $mainUrlRewrite     = Mage::getModel('core/url_rewrite')->load($target_path, 'target_path');
                    $getRequestPath     = $mainUrlRewrite->getRequestPath();
                    $get_requestPath    = Mage::getUrl($getRequestPath);
                    if($get_requestPath == Mage::getBaseUrl()){           
                        Mage::getModel('marketplace/sellerprofile')->addRewriteUrl($store_name, $seller_id);
                    }
                    Mage::getSingleton('core/session')->addSuccess($this->__('Your profile information is saved successfully'));
                    $this->_redirect('marketplace/seller/addprofile'); 
                    return true;
                } else {
                    
                    //insert form input data in database    
                    $collection = Mage::getModel('marketplace/sellerprofile');
                    $collection->setSellerId($seller_id);
                    $collection->setStoreTitle($store_name);
					//BOF developer 06 'set existing website field and additional information to collection'.
                    $collection->setExistWebsite($website);
                    $collection->setAdditionalInfo($additional_info);
					//EOF developer 06
                    $collection->setStoreLogo($images_path);                       
                    //$collection->setCountry($country);
                    $collection->setContact($contact);
                    $collection->setDescription($description);
                    $collection->setMetaKeyword($meta_keyword);
                    $collection->setMetaDescription($meta_description);
                    //$collection->setTwitterId($twitter_id);
                    //$collection->setFacebookId($facebook_id);            
                    $collection->setBankPayment($bank_payment);
                    $collection->setPaypalId($paypal_id);
                    $collection->setShowProfile($show_profile);
                    $collection->save();
                    //url management 
                    Mage::getModel('marketplace/sellerprofile')->addRewriteUrl($store_name, $seller_id);
                    Mage::getSingleton('core/session')->addSuccess($this->__('Your profile information is saved successfully'));
                    $this->_redirect('marketplace/seller/addprofile'); 
                    return true;
                } 
                 }
                catch (Exception $e) {
                // Error message redirect to create new product page
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
                $this->_redirect('marketplace/seller/addprofile');
            }
     }
     
         
        function changebuyerAction(){
            Mage::helper('marketplace')->checkMarketplaceKey();
            if (!$this->_getSession()->isLoggedIn()) {
                Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
                $this->_redirect('customer/account/login');
            return;
        }
         $this->loadLayout();
	 $this->renderLayout();
        }
        //Function to change buyer into seller
        function becomesellerAction(){           
            $admin_approval = Mage::getStoreConfig('marketplace/admin_approval_seller_registration/need_approval'); 
            $approval = 0;
            if($admin_approval == 1){
                $approval = 0;
            } else {
                $approval = 1;
            }                        
            $getGroupId    = Mage::helper('marketplace')->getGroupId();
            $customer      = Mage::getSingleton("customer/session")->getCustomer();
                            $customer->setGroupId($getGroupId)->save();
            $customer_id   = $customer->getId(); 
            $model         = Mage::getModel('customer/customer')->load($customer_id);
			      $model->setCustomerstatus($approval)
                                ->save();                              
            if($admin_approval == 1){
                Mage::getModel('marketplace/sellerprofile')->adminApproval($customer_id);                  
                Mage::getSingleton('core/session')->addSuccess($this->__('Admin Approval is required. Please wait until admin confirms your Seller Account'));
            } else {
                Mage::getModel('marketplace/sellerprofile')->newSeller($customer_id); 
                Mage::getSingleton('core/session')->addSuccess($this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName()));               
            }                            
            $this->_redirect('customer/account');  
         } 
         //Function to display seller profile information
        function displaysellerAction(){
               Mage::helper('marketplace')->checkMarketplaceKey();
               $this->loadLayout();
               $this->renderLayout();
               $id           = $this->getRequest()->getParam('id');
               $seller_page  = Mage::getModel('marketplace/sellerprofile')->collectprofile($id);
               $head         = $this->getLayout()->getBlock('head');
                                $head->setTitle($seller_page->getStoreTitle());
                                $head->setKeywords($seller_page->getMetaKeyword());
                                $head->setDescription($seller_page->getMetaDescription());

         }
       //Function to display seller dashboard
       function dashboardAction(){
           Mage::helper('marketplace')->checkMarketplaceKey();
           if (!$this->_getSession()->isLoggedIn()) {
                Mage::getSingleton('core/session')->addError($this->__('You must have a Seller Account to access this page'));
                $this->_redirect('marketplace/seller/login');
                return;
        }
          $this->loadLayout();
          $this->renderLayout();
      }
      //Function to display top seller
      function topsellerAction(){
          $this->loadLayout();
          $this->renderLayout();      
      }
      //Function to display home page content
       function homeAction(){
          $this->loadLayout();
          $this->renderLayout();      
      }
      //Function to display All seller information
      function allsellerAction(){
          $this->loadLayout();
          $this->renderLayout();      
      }
      //Function to display category wise seller products
      function categorylistAction(){
          $this->loadLayout();
          $this->renderLayout();      
      }
      //Function to save reviews and ratings
      function savereviewAction(){
          $data         = $this->getRequest()->getPost();
          $id           = $data['seller_id'];
          $url          = Mage::getModel('marketplace/sellerreview')->backUrl($id);
          $save_review  = Mage::getModel('marketplace/sellerreview')->saveReview($data);
          if($save_review ==1){
                Mage::getSingleton('core/session')->addSuccess($this->__('Your review has been posted successfully'));
                $this->_redirectUrl($url); 
          }else{
                Mage::getSingleton('core/session')->addError($this->__('Sorry there was an error occured while submiting your review'));   
                $this->_redirectUrl($url);             
          }
          
      }
      //Function to display all reviews in seller profile page
      function allreviewAction(){
          $this->loadLayout();
          $this->renderLayout();
      }
      //Function to display customer review to seller
      function customerreviewAction(){
          $this->loadLayout();
          $this->renderLayout();
      }       
}