<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite urlparameters function plugin
 *
 * Type:     function
 * Name:     urlparameters
 * Purpose:
 *
 * @param array
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_urlparameters($p_params)
{
    $uriObj = CampSite::GetURI();

    return $uriObj->getQuery();
} // fn smarty_function_urlparameters

?>