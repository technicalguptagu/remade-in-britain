<?php
/*
 * @category :  Oscp
 * @package  :  Oscp_AdvancedNewsletter
 * Author	 :  developer (29 1/2/2014)
 */

require_once(Mage::getModuleDir('controllers','Mage_Newsletter').DS.'SubscriberController.php');
class Oscp_AdvancedNewsletter_Newsletter_SubscriberController extends Mage_Newsletter_SubscriberController
{
	/* return string */
  public function newAction()
   {

	   

	   if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');

			//As per custom layout change it should display message directly on popup.
			$_subscribeErr = false;
            $_ajaxSubscribeFlag = $this->getRequest()->getPost('ajaxAction');
          
            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
					$session->setMyMessage('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl());
					$_subscribeErr = true;
                }

                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();

				if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
					$session->setMyMessage('This email address is already assigned to another user.');
					$_subscribeErr = true;
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($this->__('Confirmation request has been sent.'));
                }
                else {
                   $_SESSION['news'] = 'true';   
                   //$session->addSuccess($this->__('Thank you for your subscription'));
                }
				

            }
            catch (Mage_Core_Exception $e) {
		$_SESSION['news'] = 'false';
                //$session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
                $_subscribeErr = true;
            }
            catch (Exception $e) {
		$_SESSION['news'] = 'false';
                //$session->addException($e, $this->__('There was a problem with the subscription.'));
		$_subscribeErr = true;
            }
        }

		//----------------------//
		if($_ajaxSubscribeFlag == 1){ //as per ajax subscribe requirement

			$_subscribeAjaxMessage = array();
			$_subscribeSuccess = ($_subscribeErr !== true) ? 'true':'false'; 
			$_subscribeMessage = array();
			
			$_successMessage = $this->__('Thank you for subscribing to our Newsletter.');

			$messages = Mage::getSingleton('core/session')->getMessages(true);
			  foreach($messages->getItems() as $message)
			  {
				 // Do something   
				$_subscribeMessage[] = $message->getText();
				
			  }

			$_subscribeAjaxMessage['success'] = $_subscribeSuccess;			
			$_subscribeAjaxMessage['msg'] = implode('<br>',$_subscribeMessage);			

			$_jsonEncode = json_encode($_subscribeAjaxMessage);
			echo $_jsonEncode;
			exit;
		}
		//----------------------//        
		$this->_redirectReferer();		
    }
}
