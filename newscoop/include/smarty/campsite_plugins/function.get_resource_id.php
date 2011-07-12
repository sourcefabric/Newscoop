<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite get_resource_id function plugin
 *
 * Type:     function
 * Name:     get_resource_id
 * Purpose:
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_get_resource_id($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('gimme');

    $context = $p_smarty->get_template_vars('gimme');
    // gets the URL base
    $urlString = $context->url->base;

    // includes the smarty camp uri plugin
    require_once($p_smarty->_get_plugin_filepath('function', 'uri'));
    // appends the URI path and query values to the base
    $urlString = smarty_function_uri(array("options"=>"id ".$p_params['template']), $p_smarty);
    $resourceId = NULL;

    $urlStringParams =  explode('=', $urlString);
    for($i = 0; $i < count($urlStringParams) - 1; $i++) {
        if( (substr($urlStringParams[$i], -3) == 'tpl')  ) {
            $resourceIdArray = explode('&', $urlStringParams[$i + 1]);
            $resourceId = $resourceIdArray[0];
            break;
        }
    }

    return $resourceId;

} // fn smarty_function_get_resource_id