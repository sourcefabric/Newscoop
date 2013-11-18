<?php
/**
 * @package Newscoop
 */

//
require_once(dirname(__FILE__) . '/../include/recaptchalib.php');

class Captcha_ReCAPTCHA extends Captcha
{
    /**
     * @return string
     */
    private function _getPrivateKey()
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        return $preferencesService->PLUGIN_RECAPTCHA_PRIVATE_KEY;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        return $preferencesService->PLUGIN_RECAPTCHA_PUBLIC_KEY;
    }

    /**
     * @param string $form
     * @return boolean
     */
    public function isEnabled($form = '')
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        if (!empty($form)) {
            $form = strtoupper($form . '_');
        }

        return ($preferencesService->get("PLUGIN_RECAPTCHA_{$form}ENABLED") == 'Y') ? TRUE : FALSE;
    }

    /**
     * @return string
     */
    public function render()
    {
        global $Campsite;

        $publicKey = $this->getPublicKey();
        return recaptcha_get_html(htmlspecialchars($publicKey), NULL, $Campsite['SSL_SITE']);
    }

    /**
     * @return boolean
     */
    public function validate()
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $privateKey = $preferencesService->PLUGIN_RECAPTCHA_PRIVATE_KEY;
        $resp = recaptcha_check_answer($privateKey,
            $_SERVER['REMOTE_ADDR'],
            $_POST['recaptcha_challenge_field'],
            $_POST['recaptcha_response_field']);
        return $resp->is_valid;
    }
}
