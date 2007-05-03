<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_language function plugin
 *
 * Type:     function
 * Name:     unset_language
 * Purpose:  
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_language($p_params, &$p_smarty)
{
    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');
    if (!is_object($camp->language) || !$camp->language->defined) {
        return;
    }

    unset($camp->language);
    $p_smarty->assign('language', null);
} // fn smarty_function_unset_language

?>