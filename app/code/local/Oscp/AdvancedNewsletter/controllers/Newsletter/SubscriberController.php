<?php
/**
 * Oscp.com
 *
 * Oscp   Advanced Newsletter
 *
 * @category     Magento Extension
 * @copyright    Copyright (c) 2011 Ecommerce Team (http://www.ecommerce-team.com)
 * @author       Ecommerce Team
 * @version      1.0
 */
require_once "Mage/Newsletter/controllers/SubscriberController.php";  
class Oscp_AdvancedNewsletter_Newsletter_SubscriberController extends Mage_Newsletter_SubscriberController{

    /**
     * New subscription action
     */

    public function newAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.',             Mage::helper('customer')->getRegisterUrl()));
                }

                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($this->__('Confirmation request has been sent.'));
                }
                else {
			        $session->setSetnewsdate("true");
                }
            }
            catch (Mage_Core_Exception $e) {
				$session = Mage::getSingleton('core/session',array('name' => 'frontend'));
			    $session->setSetnewsdate("false");
            }
            catch (Exception $e) {
				$session = Mage::getSingleton('core/session',array('name' => 'frontend'));
			    $session->setSetnewsdate("false");
            }
        }
        $this->_redirectReferer();
    }
}


 
				
