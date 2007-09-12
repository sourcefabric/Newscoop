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
function smarty_function_uri($p_params)
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
            $uriString = $uriObj->getRequestURI();
        }
        break;
    }

    return $uriString;
} // fn smarty_function_uri

function get_language($p_uriObj)
{
    $uriLanguage = '';
    if ($p_uriObj->getURLType() == 1) {
        $uriLanguage = '/'.$p_uriObj->getLanguageCode().'/';
    } elseif ($p_uriObj->getURLType() == 2) {
        $uriLanguage = $p_uriObj->getPath()
            .'?IdLanguage='.$p_uriObj->getQueryVar('IdLanguage');
    }

    return $uriLanguage;
} // fn get_language;

function get_publication($p_uriObj)
{
    $uriPublication = get_language($p_uriObj);
    if ($p_uriObj->getURLType() == 2) {
        $uriPublication .= '&IdPublication='.$p_uriObj->getQueryVar('IdPublication');
    }

    return $uriPublication;
} // fn get_publication

function get_issue($p_uriObj)
{
    $uriIssue = get_publication($p_uriObj);
    if ($p_uriObj->getURLType() == 1) {
        $uriIssue .= $p_uriObj->getIssueShortName().'/';
    } elseif ($p_uriObj->getURLType() == 2) {
        $uriIssue .= '&NrIssue='.$p_uriObj->getQueryVar('NrIssue');
    }

    return $uriIssue;
} // fn get_issue

function get_section($p_uriObj)
{
    $uriSection = get_issue($p_uriObj);
    if ($p_uriObj->getURLType() == 1) {
        $uriSection .= $p_uriObj->getSectionShortName().'/';
    } elseif ($p_uriObj->getURLType() == 2) {
        $uriSection .= '&NrSection='.$p_uriObj->getQueryVar('NrSection');
    }

    return $uriSection;
} // fn get_section

function get_article($p_uriObj)
{
    $uriArticle = get_section($p_uriObj);
    if ($p_uriObj->getURLType() == 1) {
        $uriArticle .= $p_uriObj->getArticleShortName().'/';
    } elseif ($p_uriObj->getURLType() == 2) {
        $uriArticle .= '&NrArticle='.$p_uriObj->getQueryVar('NrArticle');
    }

    return $uriArticle;
} // fn get_article

?>