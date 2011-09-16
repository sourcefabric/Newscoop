<?php

function smarty_function_omnibox($p_params, &$p_smarty)
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
}

?>