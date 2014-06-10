<?php
/**
 * In this file used to rendering the apptha social login operations in our site.
 * 
 * In this class contains the login and create account and  forget password operations.
 * Also it will connects social networks such as Google, Twitter, Yahoo and Facebook oAuth connections.
 *
 * @package         Apptha Social Login
 * @version         0.1.7
 * @since           Magento 1.5
 * @author          Apptha Team
 * @copyright       Copyright (C) 2014 Powered by Apptha
 * @license         http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date   July 26 2012
 * @Modified By     Murali B
 * @Modified Date   Mar 24 2014
 *
 * */
class Apptha_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Render Apptha sociallogin pop-up layout
     */
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer Register Action
     * 
     * @return string
     */
    public function customerAction($firstname, $lastname, $email, $provider) {

        /**
         * Fetching the customer collection
         */
        $customer = Mage::getModel('customer/customer');
        $collection = $customer->getCollection();
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId());
        }
        if ($this->_getCustomerSession()->isLoggedIn()) {
            $collection->addFieldToFilter('entity_id', array('neq' => $this->_getCustomerSession()->getCustomerId()));
        }
        
        /**
         * Retrieves the customer details depends on @email
         */
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
        $customer_id_by_email = $customer->getId();

        if ($customer_id_by_email == '') {
            $standardInfo['email'] = $email;
        } else {
            $standardInfo['email'] = $email;
        }

        /**
         * Retrieving the customer form posted values. @param array $standardInfo
         *
         * array values such as	@first_name,@last_name and @email 
         */
        $standardInfo['first_name'] = $firstname;
        $standardInfo['last_name'] = $lastname;

        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($standardInfo['email']);

        /**
         * Check if Already registered customer. 
         */
        if ($customer->getId()) {

            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
            $this->_getCustomerSession()->addSuccess($this->__('Your account has been successfully connected through' . ' ' . $provider));

            /**
             * Get customer current URL from customer session. 
             */
            $link = Mage::getSingleton('customer/session')->getLink();

            if (!empty($link)) {
                $requestPath = trim($link, '/');
            }

            /**
             * Check if customer current URL is checkout URL.
             */
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect($requestPath);
                return;
            } else {
                $enable_redirect_status = Mage::getStoreConfig('sociallogin/general/enable_redirect');

                if ($enable_redirect_status) {
                    $redirect = $this->_loginPostRedirect();
                } else {
                    $redirect = Mage::getSingleton('core/session')->getReLink();
                }

                $this->_redirectUrl($redirect);
                return;
            }
        }

        /**
         * Generate Random Password .
         *
         * Set Login provider if customer uses social networks such as @google, @yahoo, @facebook and @twitter.
         *
         */
        $randomPassword = $customer->generatePassword(8);
        /**
         * Set Login provider if customer uses social networks such as @google, @yahoo, @facebook and @twitter.
         */
        $customer->setId(null)
                ->setSkipConfirmationIfEmail($standardInfo['email'])
                ->setFirstname($standardInfo['first_name'])
                ->setLastname($standardInfo['last_name'])
                ->setEmail($standardInfo['email'])
                ->setPassword($randomPassword)
                ->setConfirmation($randomPassword)
                ->setLoginProvider($provider);

        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Registration will fail if tax required, also if @DOB, @Gender aren't allowed in your profile
         */
        $errors = array();
        $validationCustomer = $customer->validate();
        if (is_array($validationCustomer)) {
            $errors = array_merge($validationCustomer, $errors);
        }
        $validationResult = true;

        if (true === $validationResult) {
            $customer->save();

            $this->_getCustomerSession()->addSuccess(
                    $this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()) .
                    '. ' .
                    $this->__('You will receive welcome email with registration info in a moment.')
            );
            /**
             * If not a change password or click here forget password
             */

            $customer->sendNewAccountEmail();

            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);

            $link = Mage::getSingleton('customer/session')->getLink();

            if (!empty($link)) {

                $requestPath = trim($link, '/');
            }

            if ($requestPath == 'checkout/onestep') {
                $this->_redirect($requestPath);
                return;
            } else {
                $enable_redirect_status = Mage::getStoreConfig('sociallogin/general/enable_redirect');
                if ($enable_redirect_status) {
                    $redirect = $this->_loginPostRedirect();
                } else {
                    $redirect = Mage::getSingleton('core/session')->getReLink();
                }
                $this->_redirectUrl($redirect);
                return;
            }
            /**
             * If doesn't set form data it will redirects to Registration page
             */
        } else {
            $this->_getCustomerSession()->setCustomerFormData($customer->getData());
            $this->_getCustomerSession()->addError($this->__('User profile can\'t provide all required info, please register and then connect with Apptha Social login.'));
            if (is_array($errors)) {
                foreach ($errors as $errorMessage) {
                    $this->_getCustomerSession()->addError($errorMessage);
                }
            }
            $this->_redirect('customer/account/create');
            return;
        }
    }

    /**
     * Retrieve customer session from core customer session
     * 
     * @return array
     */
    private function _getCustomerSession() {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Redirect customer dashboard URL after logging in 
     *
     * @return string URL
     */
    protected function _loginPostRedirect() {
        $session = $this->_getCustomerSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            /**
             * Set Default Account URL to customer session
             */
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());
            /**
             * Redirect customer to the last page visited after logging in
             */
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
                    if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
                        $referer = Mage::helper('core')->urlDecode($referer);
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        return $session->getBeforeAuthUrl(true);
    }

    /**
     * @Twitter login action
     *
     */
    public function twitterloginAction() {

        /**
         * Include Twitter files for oAuth connection
         */
        require 'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';

        /**
         * Retrives @Twitter consumer key and secret key from core session
         */
        $tw_oauth_token = Mage::getSingleton('customer/session')->getTwToken();
        $tw_oauth_token_secret = Mage::getSingleton('customer/session')->getTwSecret();
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $tw_oauth_token, $tw_oauth_token_secret);

        /**
         * Get Accesss token from @Twitter oAuth
         */
        $oauth_verifier = $this->getRequest()->getParam('oauth_verifier');
        $access_token = $twitteroauth->getAccessToken($oauth_verifier);

        /**
         * Get @Twitter User Details from twitter account
         * 
         * @return string Redirect URL or Customer save action
         */
        $user_info = $twitteroauth->get('account/verify_credentials');
        if (isset($user_info->error)) {
            Mage::getSingleton('customer/session')->addError($this->__('Twitter Login connection failed'));
            $url = Mage::helper('customer')->getAccountUrl();
            return $this->_redirectUrl($url);
        } else {

            $firstname = $user_info->name;
            $twitter_id = $user_info->id;
            $email = Mage::getSingleton('customer/session')->getTwemail();
            $lastname = ' ';

            if ($email == '' || $firstname == '') {
                Mage::getSingleton('customer/session')->addError($this->__('Twitter Login connection failed'));
                $url = Mage::helper('customer')->getAccountUrl();
                return $this->_redirectUrl($url);
            } else {
                $this->customerAction($firstname, $lastname, $email, 'Twitter');
            }
        }
    }

    /**
     * @Twitter post action 
     * 
     * @return string Returns Twitter page URL for Authendication  
     */
    public function twitterpostAction() {
        $provider = '';
        $twitter_email = (string) $this->getRequest()->getPost('email_value');
        Mage::getSingleton('customer/session')->setTwemail($twitter_email);
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($twitter_email);
        $customer_id_by_email = $customer->getId();
        $customer = Mage::getModel('customer/customer')->load($customer_id_by_email);
        $google_uid = $customer->getGoogleUid();
        if ($google_uid != '') {
            $provider.=' Google';
        }

        $facebook_uid = $customer->getFacebookUid();
        if ($facebook_uid != '') {
            $provider.=', Facebook';
        }
        $linkedin_uid = $customer->getLinkedinUid();
        if ($linkedin_uid != '') {
            $provider.=', Linkedin';
        }
        $yahoo_uid = $customer->getYahooUid();
        if ($yahoo_uid != '') {
            $provider.=', Yahoo';
        }
        $twitter_uid = $customer->getTwitterUid();
        $provider = ltrim($provider, ',');

        if ($customer_id_by_email == '') {
            $url = Mage::helper('sociallogin')->getTwitterUrl();
            $this->getResponse()->setBody($url);
        } else if ($provider != '') {
            $url = Mage::helper('sociallogin')->getTwitterUrl();
            $this->getResponse()->setBody($url);
        } else if (($provider == '' ) && ( $twitter_uid != '')) {
            $url = Mage::helper('sociallogin')->getTwitterUrl();
            $this->getResponse()->setBody($url);
        } else {
            $url = Mage::helper('sociallogin')->getTwitterUrl();
            $this->getResponse()->setBody($url);
        }
    }

    /**
     * @facebook login action 
     * 
     * Connect facebook Using oAuth coonection. 
     * 
     * @return string redirect URL
     * 
     */
    public function fbloginAction() {
        require 'sociallogin/facebook/facebook.php';
        require 'sociallogin/config/fbconfig.php';
        /**
         * create facebook object using @APP_ID, @APP_SECRET
         */
        $facebook = new Slogin_Facebook(array(
            'appId' => APP_ID,
            'secret' => APP_SECRET,
            'cookie' => false,
        ));

        /**
         * Retrieve user information from @facebook 
         */
        $user = $facebook->getUser();

        if ($user) {
            try {

                /**
                 * Proceed the further action for customer who authenticated from @facebook
                 */
                $user_profile = $facebook->api('/me');
                $firstname = $user_profile['first_name'];
                $email = $user_profile['email'];
                $lastname = $user_profile['last_name'];

                if ($email == '') {
                    Mage::getSingleton('customer/session')->addError($this->__('Facebook Login connection failed'));
                    $url = Mage::helper('customer')->getAccountUrl();
                    return $this->_redirectUrl($url);
                } else {
                    $this->customerAction($firstname, $lastname, $email, 'Facebook');
                }
            } catch (SloginFacebookApiException $e) {

                Mage::log($e);
                $user = null;
                Mage::getSingleton('customer/session')->addError($e);
                $url = Mage::helper('customer')->getAccountUrl();
                print_r(Mage::getSingleton('customer/session'));
                
                Mage::getSingleton('customer/session')->clear();
                $this->_redirectUrl($url);
            }
        }
    }

    /**
     * @Google login action 
     * 
     * Connect Google Using oAuth coonection. 
     * 
     * @return string redirect URL either customer save and loggedin or an error if any occurs
     */
    public function googlepostAction() {
        /**
         * Include @Google library files for oAuth connection
         */
        require_once 'sociallogin/src/Google_Client.php';
        require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';
        /**
         * Retrieves the @google_client_id, @google_client_secret
         */
        $google_client_id = Mage::getStoreConfig('sociallogin/google/google_id');
        $google_client_secret = Mage::getStoreConfig('sociallogin/google/google_secret');
        $google_developer_key = Mage::getStoreConfig('sociallogin/google/google_develop');
        $google_redirect_url = Mage::getUrl() . 'sociallogin/index/googlepost/';
        $gClient = new Google_Client();
        $gClient->setApplicationName('login');
        $gClient->setClientId($google_client_id);
        $gClient->setClientSecret($google_client_secret);
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey($google_developer_key);
        $google_oauthV2 = new Google_Oauth2Service($gClient);
        $token = Mage::getSingleton('core/session')->getGoogleToken();
        $reset = $this->getRequest()->getParam('reset');
        if ($reset) {
            unset($token);
            $gClient->revokeToken();
            $this->_redirectUrl(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
        }

        $code = $this->getRequest()->getParam('code');

        if (isset($code)) {

            $gClient->authenticate($code);
            Mage::getSingleton('core/session')->setGoogleToken($gClient->getAccessToken());
            $this->_redirectUrl(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
            $this->_redirectUrl($google_redirect_url);
            return;
        }

        if (isset($token)) {
            $gClient->setAccessToken($token);
        }
        if ($gClient->getAccessToken()) {
            /**
             * Retrieve user details If user succesfully in Google 
             */
            $user = $google_oauthV2->userinfo->get();
            $user_id = $user['id'];
            $user_name = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
            $profile_url = filter_var($user['link'], FILTER_VALIDATE_URL);
            $token = $gClient->getAccessToken();
            Mage::getSingleton('core/session')->setGoogleToken($token);
        } else {
            /**
             * get google google Authendication URL
             */
            $authUrl = $gClient->createAuthUrl();
        }
        /**
         * If user doesn't logged-in redirects the login URL
         */
        if (isset($authUrl)) {
            $this->_redirectUrl($authUrl);
        } else {
            /**
             * Fetching user infor from an array, @param array $user
             * 
             * @param string $given_name, $familyname, $email general info for users from @google account.
             */
            $firstname = $user['given_name'];
            $lastname = $user['family_name'];

            $email = $user['email'];
            $google_user_id = $user['id'];

            if ($email == '') {
                Mage::getSingleton('customer/session')->addError($this->__('Google Login connection failed'));
                $url = Mage::helper('customer')->getAccountUrl();
                return $this->_redirectUrl($url);
            } else {

                $this->customerAction($firstname, $lastname, $email, 'Google');
            }
        }
    }

    /**
     * @Yahoo login action 
     * 
     * Connect Yahoo Using oAuth coonection. 
     * 
     * @return string URL eiether customer save and loggedin or an error if any occurs
     */
    public function yahoopostAction() {
        /**
         * Include @Yahoo library files for oAuth connection
         */
        require 'sociallogin/lib/Yahoo.inc';
        YahooLogger::setDebug(true);
        YahooLogger::setDebugDestination('LOG');
        /**
         * @param string $yahoo_client_id, $yahoo_client_secret and $yahoo_developer_key
         * 
         * Using this params setting up the connection to yahoo
         */
        $yahoo_client_id = Mage::getStoreConfig('sociallogin/yahoo/yahoo_id');
        $yahoo_client_secret = Mage::getStoreConfig('sociallogin/yahoo/yahoo_secret');
        $yahoo_developer_key = Mage::getStoreConfig('sociallogin/yahoo/yahoo_develop');
        $yahoo_domain = Mage::getUrl();
        /**
         * Use memcache to store oauth credentials via php native sessions
         * 
         * Make sure you obtain application keys before continuing by visiting:<https://developer.yahoo.com/dashboard/createKey.html>
         */
        define('OAUTH_CONSUMER_KEY', "$yahoo_client_id");
        define('OAUTH_CONSUMER_SECRET', "$yahoo_client_secret");
        define('OAUTH_DOMAIN', "$yahoo_domain");
        define('OAUTH_APP_ID', "$yahoo_developer_key");
        $para = $this->getRequest()->getParams();
        if (array_key_exists("logout", $para)) {

            /**
             * If session exists and the logoutand the logout flag is detected
             * 
             * Clears the session tokens and Reload the page.
             */
            YahooSession::clearSession();
            $url = "index.php";
            $this->_redirectUrl($url);
        }
        $hasSession = YahooSession::hasSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
        YahooUtil::current_url();
        if ($hasSession == FALSE) {
            /**
             * Create the callback URL for authendication process
             */
            $callback = YahooUtil::current_url() . "?in_popup";
            $sessionStore = new NativeSessionStore();
            /**
             * Pass the Yahoo credentials for creating the Auth URL using @params 
             * 
             * @param string OAUTH_CONSUMER_KEY Yahoo oAuth consumer key
             * @param string OAUTH_CONSUMER_SECRET Yahoo oAuth consumer secret key
             * @param string callback Yahoo callback URL
             * Auth URL will used in social login pop-up
             */
            $auth_url = YahooSession::createAuthorizationUrl(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $callback, $sessionStore);
            if ($auth_url) {
                $this->_redirectUrl($auth_url);
            }
        } else {
            /**
             * Pass the credentials to initiate a session
             */
            $session = YahooSession::requireSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
            /**
             * If the flag is detected @var string in_popup flag
             * The pop-up has loaded the callback_url and we can close this window.
             * If a session is initialized, fetch the user's profile information from yahoo
             */
            if ($session) {
                /**
                 * Get the current sessioned user from yahoo session.
                 * @return array Yahoo authendicated User session 
                 */
                $user = $session->getSessionedUser();

                /**
                 * Get the profile for current user.
                 * @return array which include profile information about current logged in user
                 */
                $profile = $user->getProfile();

                $yahoo_user_id = $profile->guid;

                $email = $profile->emails[0]->handle;
                $firstname = $profile->givenName;

                $lastname = $profile->familyName;


                if ($email == '') {
                    
                    /**
                     * Throws error message which is @param string $email is empty.
                     */
                    Mage::getSingleton('customer/session')->addError($this->__('Yahoo Login connection failed'));
                    $url = Mage::helper('customer')->getAccountUrl();
                    return $this->_redirectUrl($url);
                } else {
                    $this->customerAction($firstname, $lastname, $email, 'Yahoo');
                }
            }
        }
    }

    /**
     * Customer Login layout render Action 
     * 
     * Rendering the layout if social login extension is enabled
     */
    public function loginAction() {

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        } else if (Mage::getStoreConfig('sociallogin/general/enable_sociallogin') == 1) {
            return;
        }
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    /**
     * Customer Create Account layout render Action 
     * 
     * Rendering the layout if social login extension is enabled
     */
    public function createAction() {

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        } else {
            $enable_status = Mage::getStoreConfig('sociallogin/general/enable_sociallogin');
            if ($enable_status == 1) {
                return;
            }
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Validation for Tax/Vat field for current store
     */
    public function _isVatValidationEnabled($store = null) {
        return Mage::helper('customer/address')->isVatValidationEnabled($store);
    }

    /**
     * Customer welcome function 
     * 
     * Its used for print welcome message once successfully logged in
     * 
     * @return string customer success page URL. 
     */
    public function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getCustomerSession()->addSuccess(
                $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );

        $customer->sendNewAccountEmail(
                $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app()->getStore()->getId()
        );

        $successUrl = Mage::getUrl('customer/account', array('_secure' => true));

        if ($this->_getCustomerSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getCustomerSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /**
     * Customer login Action 
     * 
     * validate the social login form posted values if the user is registered user or not 
     * 
     * @return string Redirect URL. 
     */
    public function customerloginpostAction() {
        $session = $this->_getCustomerSession();
        /**
         * @param array $login contains email and password
         */
        $login['username'] = $this->getRequest()->getParam('email');
        $login['password'] = $this->getRequest()->getParam('password');
        
        /**
         * Check customer already logged in or not using the customet session
         */
        if ($session->isLoggedIn()) {
            $message = 'Already loggedin';
            $this->getResponse()->setBody($message);
            return;
        }
        if ($this->getRequest()->isPost()) {
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->getResponse()->setBody($this->_welcomeCustomer($session->getCustomer(), true));
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('customer')->__('Account Not Confirmed', $value);
                            $this->getResponse()->setBody($message);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $this->__('Invalid Email Address or Password');
                            $this->getResponse()->setBody($message);
                            break;
                        default:
                            $message = $e->getMessage();
                            $this->getResponse()->setBody($message);
                    }
                    $session->setUsername($login['username']);
                } catch (Exception $e) {

                    /**
                     *  @throws Exception message 
                     */
                    return $e;
                }
                /**
                 * After successful logged-in, its redirect to the respective page.
                 */
                if ($session->getCustomer()->getId()) {
                    $link = Mage::getSingleton('customer/session')->getLink();
                    $requestPath = '';
                    if (!empty($link)) {

                        $requestPath = trim($link, '/');
                    }
                    if ($requestPath == 'checkout/onestep') {
                        $this->getResponse()->setBody($requestPath);
                    } else {
                        $enable_redirect_status = 0;
                        $enable_redirect_status = Mage::getStoreConfig('sociallogin/general/enable_redirect');
                        if ($enable_redirect_status == 1) {

                            $this->getResponse()->setBody($this->_loginPostRedirect());
                        } else {

                            $this->getResponse()->setBody(Mage::getSingleton('core/session')->getReLink());
                        }
                    }
                }
            }
        }
    }

    /**
     * Customer Register Action 
     * 
     * validate the social regiter form posted values 
     * 
     * @return string Redirect URL. 
     */
    public function createPostAction() {
        $customer = Mage::getModel('customer/customer');
        $session = $this->_getCustomerSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        /**
         * Validate the captcha code if captcha is enabled
         * 
         * @return string if incorrect capatcha it will return error message
         */
        $enable_captcha = Mage::getStoreConfig('customer/captcha/enable');

        if ($enable_captcha == '1') {
            $newcaptch = $this->getRequest()->getPost('captcha');
            $_captcha = Mage::getModel('customer/session')->getData('user_create_word');
            $captcha_img_data = $_captcha['data'];
            if ($newcaptch['user_create'] != $captcha_img_data) {
                $this->getResponse()->setBody($this->__('Incorrect CAPTCHA.'));
                return;
            }
        }
        /**
         * Preventing the Cross-site Scripting (XSS) injection from an user inputs   
         */
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();


            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }
            /**
             * @var $customerForm Mage_Customer_Model_Form   
             */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());
            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Get customer group id from customer collection
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {

                /**
                 * @var $address Mage_Customer_Model_Address
                 */
                $address = Mage::getModel('customer/address');

                /**
                 * @var $addressForm Mage_Customer_Model_Form
                 */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                        ->setEntity($address);

                $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors = $addressForm->validateData($addressData);
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
                    $customer->save();

                    Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer)
                    );

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail(
                                'confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->getResponse()->setBody(Mage::getUrl('/index', array('_secure' => true)));
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        $this->getResponse()->setBody($url);
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->$errorMessage;
                        }
                        $this->getResponse()->setBody($errorMessage);
                        return;
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $message = $this->__('Email already exists');
                    $this->getResponse()->setBody($message);
                    $session->setEscapeMessages(false);
                    return;
                } else {
                    $message = $e->getMessage();
                    $this->getResponse()->setBody($message);
                    return;
                }
                $session->addError($message);
            } catch (Exception $e) {

                $session->setCustomerFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('Cannot save the customer.'));
            }
        }
        if (!empty($message)) {
            $this->getResponse()->setBody($message);
        }
        $this->getResponse()->setBody(Mage::getUrl('*/index', array('_secure' => true)));
    }

    /**
     * ForgetPassword Action
     * 
     * @param string $email Forget password action for forget password form
     * 
     * @return string $message. 
     */
    public function forgotPasswordPostAction() {
        $email = (string) $this->getRequest()->getParam('forget_password');

        /**
         * @var $customer Mage_Customer_Model_Customer
         */
        $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
        if ($customer->getId()) {
            try {
                $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                $customer->sendPasswordResetConfirmationEmail();
            } catch (Exception $exception) {
                $this->_getCustomerSession()->addError($exception->getMessage());
                return;
            }
            $message = $this->__(' you will receive an email (' . $email . ') with a link to reset your password');
        } else {
            $message = $this->__('If there is no account associated with this email please enter your correct email-id');
        }

        $this->getResponse()->setBody($message);
    }

}
