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
 * @param array $p_params
 *
 * @return string $uriString
 *      The URL parameters requested
 */
function smarty_function_urlparameters($p_params)
{
    $uriString = '';
    $uriObj = CampSite::GetURI();

    switch ($p_params['options']) {
    case 'language':
        $uriString = get_language($uriObj);
        break;
    case 'publication':
        $uriString = get_publication($uriObj);
        break;
    case 'issue':
        $uriString = get_issue($uriObj);
        break;
    case 'section':
        $uriString = get_section($uriObj);
        break;
    case 'article':
        $uriString = get_article($uriObj);
        break;
    default:
        if (empty($p_params)) {
            $uriString = $uriObj->getQuery();
        }
        break;
    }

    return $uriString;
} // fn smarty_function_urlparameters

function get_language($p_uriObj)
{
    $langParam = '';
    if ($p_uriObj->getURLType() == 2) {
        $langParam = 'IdLanguage='.$p_uriObj->getQueryVar('IdLanguage');
    }

    return $langParam;
} // fn get_language

function get_publication($p_uriObj)
{
    $pubParam = get_language($p_uriObj);
    if ($p_uriObj->getURLType() == 2) {
        $pubParam .= '&IdPublication='.$p_uriObj->getQueryVar('IdPublication');
    }

    return $pubParam;
} // fn get_publication

function get_issue($p_uriObj)
{
    $issueParam = get_publication($p_uriObj);
    if ($p_uriObj->getURLType() == 2) {
        $issueParam .= '&NrIssue='.$p_uriObj->getQueryVar('NrIssue');
    }

    return $issueParam;
} // fn get_issue

function get_section($p_uriObj)
{
    $sectParam = get_issue($p_uriObj);
    if ($p_uriObj->getURLType() == 2) {
        $sectParam .= '&NrSection='.$p_uriObj->getQueryVar('NrSection');
    }

    return $sectParam;
} // fn get_section

function get_article($p_uriObj)
{
    $artParam = get_section($p_uriObj);
    if ($p_uriObj->getURLType() == 2) {
        $artParam .= '&NrArticle='.$p_uriObj->getQueryVar('NrArticle');
    }

    return $artParam;
} // fn get_article

?>