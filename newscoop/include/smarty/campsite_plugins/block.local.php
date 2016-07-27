<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_issues block plugin
 *
 * Type:     block
 * Name:     list_issues
 * Purpose:  Provides a 
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_local($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
    $campContext = $p_smarty->getTemplateVars('gimme');

    if (!isset($p_content)) {
        $campContext->saveCurrentContext();
        $p_repeat = true;
    } else {
        $campContext->restoreContext();
        $p_repeat = false;
    }

    return $p_content;
}

?>
