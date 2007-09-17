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
function smarty_function_urlparameters($p_params, &$p_smarty)
{
    $uriString = '';
    $campContext = $p_smarty->get_template_vars('campsite');

    switch (strtolower($p_params['options'])) {
    case 'language':
        $uriString = get_language($campContext);
        break;
    case 'publication':
        $uriString = get_publication($campContext);
        break;
    case 'issue':
        $uriString = get_issue($campContext);
        break;
    case 'section':
        $uriString = get_section($campContext);
        break;
    case 'article':
        $uriString = get_article($campContext);
        break;
    default:
        if (empty($p_params)) {
            $uriString = $campContext->url->query;
        }
        break;
    }

    return $uriString;
} // fn smarty_function_urlparameters

function get_language(&$p_context)
{
    $langParam = '';
    if ($p_context->url->type == 2) {
        $langParam = 'IdLanguage='.$p_context->language->number;
    }

    return $langParam;
} // fn get_language

function get_publication(&$p_context)
{
    $pubParam = get_language($p_context);
    if ($p_context->url->type == 2) {
        $pubParam .= '&IdPublication='.$p_context->publication->identifier;
    }

    return $pubParam;
} // fn get_publication

function get_issue(&$p_context)
{
    $issueParam = get_publication($p_context);
    if ($p_context->url->type == 2) {
        $issueParam .= '&NrIssue='.$p_context->issue->number;
    }

    return $issueParam;
} // fn get_issue

function get_section(&$p_context)
{
    $sectParam = get_issue($p_context);
    if ($p_context->url->type == 2) {
        $sectParam .= '&NrSection='.$p_context->section->number;
    }

    return $sectParam;
} // fn get_section

function get_article(&$p_context)
{
    $artParam = get_section($p_context);
    if ($p_context->url->type == 2) {
        $artParam .= '&NrArticle='.$p_context->article->number;
    }

    return $artParam;
} // fn get_article

?>