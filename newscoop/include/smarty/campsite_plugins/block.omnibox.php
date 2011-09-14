<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite omnibox block plugin
 *
 * Type:     block
 * Name:     comment_form
 * Purpose:  Displays a form for comment input
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
function smarty_block_omnibox($p_params, $p_content, &$p_smarty, &$p_repeat)
{
	global $controller;
	
	$gimme = $p_smarty->get_template_vars('gimme');
	
	$parameters = $p_params;
	$parameters['gimme'] = $gimme;
	
	$p_content = $controller->view->action(
		'index',
		'omnibox',
		'default',
		$parameters
	);
    
    return($p_content);
} // fn smarty_block_omnibox

?>