<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

require_once dirname(__FILE__) . '/function.uri.php';

/**
 * Campsite url function plugin
 *
 * Type:     function
 * Name:     url
 * Purpose:
 *
 * @param array $p_params
 * @param object $p_smarty
 *      The Smarty object
 *
 * @return string $urlString
 *      The full URL string
 */
function smarty_function_url($p_params, &$p_smarty)
{
    $context = $p_smarty->getTemplateVars('gimme');

    // gets the URL base
    $urlString = $context->url->base;

    if (isset($p_params['noprotocol'])) {
        $noprotocol = ($p_params['noprotocol'] === 'true') ? 1 : 0;
    } else {
        $systemPref = \Zend_Registry::get('container')->get('preferences');
        $noprotocol = ($systemPref->get('SmartyUseProtocol') === 'Y') ? 0 : 1;
    }

    if ($noprotocol) {
        $urlString = preg_replace('@^https?:@', '', $urlString);
    }

    // appends the URI path and query values to the base
    $urlString .= smarty_function_uri($p_params, $p_smarty);

    return $urlString;
} // fn smarty_function_url

?>
