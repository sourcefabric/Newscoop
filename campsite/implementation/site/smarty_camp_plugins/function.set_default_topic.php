<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_default_topic function plugin
 *
 * Type:     function
 * Name:     set_default_topic
 * Purpose:
 *
 * @param array
 *     $p_params[name] The Name of the topic to be set
 *     $p_params[identifier] The Identifier of the topic to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_default_topic($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    $campsite->topic = $campsite->default_topic;
} // fn smarty_function_set_default_topic

?>