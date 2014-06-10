<?php
/**
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
class Apptha_Sociallogin_Model_Observer extends Mage_Core_Model_Abstract {

    /**
     * Captcha validation for create account form
     * 
     * @return string $message for validation failed if any
     */
    public function checkCaptcha($observer) {

        $formId = 'Apptha_Sociallogin';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $request = $controller->getRequest();
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();

            $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
            if (!$captchaModel->isCorrect($this->_getCaptchaString($request, $formId))) {



                if ((isset($this->getRequest()->isXmlHttpRequest()) && strtolower($this->getRequest()->isXmlHttpRequest()) == 'xmlhttprequest')) {
                    /**
                     * If the form using AJAX returns $message
                     */
                    $action = $request->getActionName();
                    Mage::app()->getFrontController()->getAction()->setFlag(
                            $action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);

                    $controller->getResponse()->setHttpResponseCode(200);
                    $controller->getResponse()->setHeader('Content-type', 'application/json');

                    $controller->getResponse()->setBody(json_encode(
                                    array(
                                        "msg" => Mage::helper('module')->__('Incorrect CAPTCHA.')
                                    )
                    ));
                } else {
                    /**
                     * If the form submit returns $message
                     */
                    Mage::getSingleton('customer/session')
                            ->addError(Mage::helper('module')->__('Incorrect CAPTCHA.'));
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    Mage::getSingleton('customer/session')
                            ->setCustomerFormData($controller->getRequest()->getPost());
                    $controller->getResponse()->setRedirect(Mage::getUrl('*/*'));
                }
            }
        }

        return $this;
    }

}
