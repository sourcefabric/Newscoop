<?php

function smarty_function_omnibox($p_params, &$p_smarty)
{
    global $controller;
    
    $gimme = $p_smarty->getTemplateVars('gimme');
	
	$parameters = $p_params;
	$parameters['gimme'] = $gimme;
    
    try {
		$p_content = $controller->view->action(
			'index',
			'omnibox',
			'default',
			$parameters
		);
	}
	catch (Exception $e) {
		$p_content = '';
	}
    
    return($p_content);
}

?>
