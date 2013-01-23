<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite uri function plugin
 *
 * Type:     function
 * Name:     uri
 * Purpose:
 *
 * @param array $p_params
 *
 * @return string $uriString
 *      The requested URI
 */
function smarty_function_uri($p_params, &$p_smarty)
{
    $context = $p_smarty->getTemplateVars('gimme');

    if (!isset($p_params['options'])) {
        $p_params['options'] = '';
    }

    $url = 'url';
    if (isset($p_params['static_file']) && !empty($p_params['static_file'])) {
    	$p_params['options'] = 'static_file ' . $p_params['static_file'];
    } else {
    	$params = preg_split("/[\s]+/", $p_params['options']);
    	foreach ($params as $index=>$param) {
    		if (strcasecmp('fromstart', $param) == 0) {
    			$url = 'default_url';
    			unset($params[$index]);
    			$p_params['options'] = implode(', ', $params);
    			break;
    		}
    	}
    }

    if ($p_params['options'] === 'author') {
        $view = $p_smarty->getTemplateVars('view');
        return $view->url(array('author' => $context->author->name), 'author');
    }

    if ($p_params['options'] === 'topic') {
        $view = $p_smarty->getTemplateVars('view');
        return $view->url(array('id' => $context->topic->identifier, 'language' => $context->language->code, 'topicName' => $context->topic->name), 'topic');
    }

    // sets the URL parameter option
    $context->$url->uri_parameter = $p_params['options'];

    return $context->$url->uri;
} // fn smarty_function_uri

?>
