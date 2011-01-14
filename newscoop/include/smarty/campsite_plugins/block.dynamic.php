<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite tr block plugin
 *
 * Type:     block
 * Name:     dynamic
 * Purpose:  Used to avoid template caching
 *
 * @param array
 *     $p_params
 * @param string
 *     $p_content
 * @param object
 *     $p_smarty
 */
function smarty_block_dynamic($p_params, $p_content, &$p_smarty)
{
    return $p_content;
}

?>