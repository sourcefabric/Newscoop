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
 * @param array
 *     $p_params[name] The Name of the article to be set
 *     $p_params[number] The Number of the article to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_uri($p_params)
{
    $uriObj = CampSite::GetURI();
    switch ($p_params['options']) {
    case 'language':
    case 'publication':
        if ($uriObj->getURLType() == 1) {
            $uriStr = '/'.$uriObj->getLanguageCode().'/';
        }
        break;
    case 'issue':
        if ($uriObj->getURLType() == 1) {
            $uriStr = '/'.$uriObj->getLanguageCode().'/'
                .$uriObj->getIssueShortName().'/';
        }
        break;
    case 'section':
        if ($uriObj->getURLType() == 1) {
            $uriStr = '/'.$uriObj->getLanguageCode().'/'
                .$uriObj->getIssueShortName().'/'
                .$uriObj->getSectionShortName().'/';
        }
        break;
    case 'article':
        if ($uriObj->getURLType() == 1) {
            $uriStr = '/'.$uriObj->getLanguageCode().'/'
                .$uriObj->getIssueShortName().'/'
                .$uriObj->getSectionShortName().'/'
                .$uriObj->getArticleShortName().'/';
        }
        break;
    default:
        $uriStr = $uriObj->getRequestURI();
        break;
    }

    return $uriStr;
} // fn smarty_function_uri

?>