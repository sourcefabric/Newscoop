<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite enable_html_encoding function plugin
 *
 * Type:     function
 * Name:     enable_html_encoding
 * Purpose:
 *
 * @param array
 *     $p_params[name] The Name of the publication to be set
 *     $p_params[identifier] The Identifier of the publication to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_enable_html_encoding($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    if ($campsite->htmlencoding == false) {
        $campsite->htmlencoding = true;
    }
} // fn smarty_function_enable_html_encoding

?>