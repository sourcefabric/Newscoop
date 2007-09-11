<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite uripath function plugin
 *
 * Type: function
 * Name: uripath
 * Purpose:
 *
 * @param array
 *
 * @return string $uriStr
 *      The URI path
 */
function smarty_function_uripath($p_params)
{
    $uriObj = CampSite::GetURI();
    
    return $uriObj->getPath();
} // fn smarty_function_uripath

?>