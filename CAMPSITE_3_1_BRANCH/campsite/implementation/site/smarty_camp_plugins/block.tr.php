<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite tr block plugin
 *
 * Type:     block
 * Name:     tr
 * Purpose:  Translates the given string to the current language
 *
 * @param array
 *     $p_params The string to be translated
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_block_tr($p_params, $p_content, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');
    if (!$campsite->language->defined) {
        return;
    }

    return translate($p_content, $campsite->language->number);
} // fn smarty_block_tr

?>