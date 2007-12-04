<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite interview_form block plugin
 *
 * Type:     block
 * Name:     poll_form
 * Purpose:  Provides a form for an interview
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_interview_form($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $camp = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (isset($p_content)) {
        require_once $p_smarty->_get_plugin_filepath('function', 'urlparameters');
        require_once $p_smarty->_get_plugin_filepath('function', 'uripath');
        
        parse_str(smarty_function_urlparameters($p_params, &$p_smarty), $urlparameters);
        $uripath = smarty_function_uripath($p_params, &$p_smarty);
        
        $Interview = new Interview($camp->interview->identifier);
        $html = $Interview->getForm($uripath, $urlparameters, true);
    }

    return $html;
} // fn smarty_block_poll_form

?>