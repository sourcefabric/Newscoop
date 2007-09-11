<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite url function plugin
 *
 * Type:     function
 * Name:     url
 * Purpose:
 *
 * @param array
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_url($p_params, &$p_smarty)
{
    $uriObj = CampSite::GetURI();

    return $uriObj->getURL();
} // fn smarty_function_url

?>