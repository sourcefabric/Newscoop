<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_default_article function plugin
 *
 * Type:     function
 * Name:     set_default_article
 * Purpose:
 *
 * @param array
 *     $p_params[name] The Name of the article to be set
 *     $p_params[number] The Number of the article to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_default_article($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    $campsite->article = $campsite->default_article;
} // fn smarty_function_set_default_article

?>