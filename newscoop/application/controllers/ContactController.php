<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Contact controller
 */
class ContactController extends Zend_Controller_Action
{

    public function indexAction() 
    {
        $this->_helper->layout->disableLayout();

        if ($this->getRequest()->isPost()) {
            $container = \Zend_Registry::get('container');
            $mailer = $container->getService('email');
            $parameters = $this->getRequest()->getParams();
            $configOptions = $this->getInvokeArg('bootstrap')->getOption('email');
            $errors = array();

            $publicationObj = new Publication(CampRequest::GetVar('publicationId', '', 'POST'));
            if ($publicationObj->isCaptchaEnabled()) {
                $captchaResult = $this->_processCaptcha();
                if (is_string($captchaResult)) {
                    $errors[] = $captchaResult;
                }
            }

            if (count($errors) == 0) {
                $mailer->sendHtml($parameters['topic'], $parameters['subject'] .'<br />'. $parameters['contact_email'] .'<br />'. $parameters['contact_message'], array($configOptions['contact']));
                $this->view->success = true;
            } else {
                $this->view->errors = $errors;
            }
        }
    }

    /**
     * @return void
     */
    private function _processCaptcha()
    {
        $captchaHandler = CampRequest::GetVar('f_captcha_handler', '', 'POST');
        if (!empty($captchaHandler)) {
            $captcha = Captcha::factory($captchaHandler);
            if (!$captcha->validate()) {
                return $this->view->translate('The code you entered is not the same as the one shown.');
            }
        } else {
            $f_captcha_code = CampRequest::GetVar('f_captcha_code');
            if (is_null($f_captcha_code) || empty($f_captcha_code)) {
                return $this->view->translate('Please enter the code shown in the image.');
            }
            if (!PhpCaptcha::Validate($f_captcha_code, true)) {
                return $this->view->translate('The code you entered is not the same with the one shown in the image.');
            }
        }

        return true;
    }
}
