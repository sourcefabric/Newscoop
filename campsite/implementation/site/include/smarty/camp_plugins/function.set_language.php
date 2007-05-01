<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_language function plugin
 *
 * Type:     function
 * Name:     set_language
 * Purpose:  
 *
 * @param array
 *     $p_params The English name of the language to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_language($p_params, &$p_smarty)
{
    global $g_ado_db;

    if (!isset($p_params['name']) || empty($p_params['name'])) {
        return false;
    }

    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');
    if ($camp->language->defined && $camp->language->english_name == $p_params['name']) {
        return;
    }

    $queryStr = "SELECT Id FROM Languages WHERE Name = '"
               .$g_ado_db->escape($p_params['name'])."'";
    $row = $g_ado_db->GetRow($queryStr);
    if (!sizeof($row) || $row['Id'] < 1) {
        return false;
    }
    $language = new MetaLanguage($row['Id']);
    if ($language->defined == 'defined') {
        $camp->language = $language;
        $p_smarty->assign('language', $camp->language);
    }

} // fn smarty_function_set_language

?>