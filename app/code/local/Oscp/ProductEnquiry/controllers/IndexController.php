<?php
/*
 * @category :  Oscp
 * @package  :  Oscp_ProductEnquiry
 */
class Oscp_ProductEnquiry_IndexController extends Mage_Core_Controller_Front_Action {

   
    const XML_PATH_EMAIL_SENDER = 'productenquiry/email/sender_email_identity_sender';
    const XML_PATH_EMAIL_TEMPLATE = 'productenquiry/email/email_template';  

    public function indexAction() {

        $this->loadLayout();
        $this->renderLayout();
    }

    public function postAction() {

        $post = $this->getRequest()->getPost();
		$recipient = $post['retaileremailid'];
        $model = Mage::getModel('productenquiry/productenquiry');
        $model->setData($post);
		
        $translate = Mage::getSingleton('core/translate');

        $translate->setTranslateInline(false);


        try {
            $postObject = new Varien_Object();
            $postObject->setData($post);
            /* @var validation of posted data */
            $this->dataValidation();

            /* @var $mailTemplate Mage_Core_Model_Email_Template */

            $mailTemplate = Mage::getModel('core/email_template');

            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE), Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER), $recipient, null, array('data' => $postObject)
            );

            if (!$mailTemplate->getSentSuccess()) {
                throw new Exception();
            }

            $translate->setTranslateInline(true);
            
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('productenquiry')->__('Thank you for your enquiry, we will get back to you shortly .'));
            $this->_redirectUrl($_SERVER['HTTP_REFERER']);
			//$this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $translate->setTranslateInline(true);
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('productenquiry')->__('Unable to submit your request. Please, check all required fields'));
            $this->_redirectUrl($_SERVER['HTTP_REFERER']);
			//$this->_redirect('*/*/');
            return;
        }
    }

    /*
      @var posted data validation
      return object Varien_Object()
     */

    public function dataValidation() {
        $post = $this->getRequest()->getPost();
        try {
            $error = false;
            if (!Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                $error = true;
            }
            if (!Zend_Validate::is(trim($post['email']), 'NotEmpty')) {
                $error = true;
            }
       
            if (!Zend_Validate::is(trim($post['question']), 'NotEmpty')) {
                $error = true;
            }
			if (!Zend_Validate::is(trim($post['telephone']), 'NotEmpty')) {
                $error = true;
            }


            if ($error) {
                throw new Exception();
            }
            return;
        } catch (Exception $e) {
            return;
        }
    }


	

}