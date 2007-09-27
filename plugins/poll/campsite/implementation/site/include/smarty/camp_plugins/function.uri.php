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
            $uriString = $campContext->url->request_uri;
        }
        break;
    }

    return $uriString;
} // fn smarty_function_uri

function get_language(&$p_context)
{
    $uriLanguage = '';
    if ($p_context->url->type == 1) {
        $uriLanguage = '/'.$p_context->language->code.'/';
    } elseif ($p_context->url->type == 2) {
        $uriLanguage = $p_context->url->path
            .'?IdLanguage='.$p_context->language->number;
    }

    return $uriLanguage;
} // fn get_language;

function get_publication($p_context)
{
    $uriPublication = get_language($p_context);
    if ($p_context->url->type == 2) {
        $uriPublication .= '&IdPublication='.$p_context->publication->identifier;
    }

    return $uriPublication;
} // fn get_publication

function get_issue($p_context)
{
    $currentListName = $p_context->getCurrentListName();
    $uriIssue = get_publication($p_context);
    if ($p_context->url->type == 1) {
        if (!is_null($currentListName)
                && $currentListName == 'current_issues_list') {
            $uriIssue .= $p_context->current_issues_list->current->url_name.'/';
        } else {
            $uriIssue .= $p_context->issue->url_name.'/';
        }
    } elseif ($p_context->url->type == 2) {
        if (!is_null($currentListName)
                && $currentListName == 'current_issues_list') {
            $uriIssue .= '&NrIssue='.$p_context->current_issues_list->current->number;
        } else {
            $uriIssue .= '&NrIssue='.$p_context->issue->number;
        }
    }

    return $uriIssue;
} // fn get_issue

function get_section($p_context)
{
    $currentListName = $p_context->getCurrentListName();
    $uriSection = get_issue($p_context);
    if ($p_context->url->type == 1) {
        if (!is_null($currentListName)
                && $currentListName == 'current_sections_list') {
            $uriSection .= $p_context->current_sections_list->current->url_name.'/';
        } else {
            $uriSection .= $p_context->section->url_name.'/';
        }
    } elseif ($p_context->url->type == 2) {
        if (!is_null($currentListName)
                && $currentListName == 'current_sections_list') {
            $uriSection .= '&NrSection='.$p_context->current_sections_list->current->number;
        } else {
            $uriSection .= '&NrSection='.$p_context->section->number;
        }
    }

    return $uriSection;
} // fn get_section

function get_article(&$p_context)
{
    $currentListName = $p_context->getCurrentListName();
    $uriArticle = get_section($p_context);
    if ($p_context->url->type == 1) {
        if (!is_null($currentListName)
                && $currentListName == 'current_articles_list') {
            $uriArticle .= $p_context->current_articles_list->current->url_name.'/';
        } else {
            $uriArticle .= $p_context->article->url_name.'/';
        }
    } elseif ($p_context->url->type == 2) {
        if (!is_null($currentListName)
                && $currentListName == 'current_articles_list') {
            $uriArticle .= '&NrArticle='.$p_context->current_articles_list->current->number;
        } else {
            $uriArticle .= '&NrArticle='.$p_context->article->number;
        }
    }

    return $uriArticle;
} // fn get_article

?>