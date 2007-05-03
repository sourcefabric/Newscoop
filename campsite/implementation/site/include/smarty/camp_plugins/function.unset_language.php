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
    $campsite = $p_smarty->get_template_vars('campsite');

    if (!is_object($campsite->language) || !$campsite->language->defined) {
        return;
    }

    $campsite->language = new MetaLanguage();

} // fn smarty_function_unset_language

?>