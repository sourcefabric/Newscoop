<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite disable_html_encoding function plugin
 *
 * Type:     function
 * Name:     disable_html_encoding
 * Purpose:
 *
 * @param array
 *     $p_params empty
 * @param object
 *     $p_smarty The Smarty object
 *
 * @return void
 */
function smarty_function_disable_html_encoding($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    if ($campsite->htmlencoding == true) {
        $campsite->htmlencoding = false;
    }
} // fn smarty_function_disable_html_encoding

?>