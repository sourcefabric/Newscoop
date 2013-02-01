<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite pollanswer_edit function plugin
 *
 * Type:     function
 * Name:     pollanswer_edit
 * Purpose:  
 *
 * @param array
 *     $p_params the date in unixtime format from $smarty.now
 * @param object
 *     $p_smarty the date format wanted
 *
 * @return
 *     string the html form element
 *     string empty if something is wrong
 */
function smarty_function_pollanswer_edit($p_params, &$p_smarty)
{
    global $g_ado_db;

    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');

    // gets the context variable
    $campsite = $p_smarty->getTemplateVars('gimme');
    $html = '';

    if (!isset($p_params['html_code']) || empty($p_params['html_code'])) {
        $p_params['html_code'] = '';
    }
    
    $html .= "<input type=\"radio\" name=\"f_pollanswer_nr\" value=\"{$campsite->pollanswer->number}\" {$p_params['html_code']} />";  

    return $html;
} // fn smarty_function_pollanswer_edit

?>