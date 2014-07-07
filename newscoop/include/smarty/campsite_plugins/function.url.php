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
    $validValues = array('true', 'false', 'http', 'https');

    if (isset($p_params['useprotocol']) && in_array($p_params['useprotocol'], $validValues)) {
        $useprotocol = $p_params['useprotocol'];
    } else {
        $systemPref = \Zend_Registry::get('container')->get('system_preferences_service');
        $useprotocol = ($systemPref->get('SmartyUseProtocol') === 'Y') ? 'true' : 'false';
    }

    switch ($useprotocol) {
        case 'true':
                $urlString = $context->url->base;
            break;
        case 'false':
                $urlString = $context->url->base_relative;
            break;
        case 'http':
                $urlString = 'http:'. $context->url->base_relative;
            break;
        case 'https':
                $urlString = 'https:'. $context->url->base_relative;
            break;
    }

    // appends the URI path and query values to the base
    $urlString .= smarty_function_uri($p_params, $p_smarty);

    return $urlString;
} // fn smarty_function_url

?>
