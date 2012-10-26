<?php
/**
 * Newscoop customized Smarty plugin
 * @package Newscoop
 */

/**
 * Newscoop reCAPTCHA function plugin
 *
 * Type: function
 * Name: recaptcha
 * Purpose: Provide access to reCAPTCHA services
 *
 * @param empty
 *
 * @param object
 *     $p_smarty The Smarty object
 *
 * @return string
 */
function smarty_function_recaptcha($p_params, &$p_smarty)
{
    $html = '';
    $captcha = Captcha::factory('ReCAPTCHA');
    if ($captcha->isEnabled($p_params['form'] ?: '')) {
        $html = $captcha->render();
        if (is_array($html) && isset($html['error'])) {
            $html = '<p style="color:red;">' . $html['error'] . '</p>';
            return $html;
        }
        $html .= "\n<input type=\"hidden\" name=\"f_captcha_handler\" value=\"ReCAPTCHA\" />\n";
    }
    return $html;
}
