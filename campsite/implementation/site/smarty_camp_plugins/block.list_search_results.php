<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_search_results block plugin
 *
 * Type:     block
 * Name:     list_search_results
 * Purpose:  Provides a...
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_list_search_results($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!$campContext->search_articles_action->defined
    || $campContext->search_articles_action->is_error) {
        $p_repeat = false;
        return '';
    }

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('SearchResultsList');
        $p_params['template'] = $campContext->search_articles_action->template;
        $p_params['match_all'] = $campContext->search_articles_action->match_all;
        $p_params['search_level'] = $campContext->search_articles_action->search_level;
        $p_params['search_phrase'] = $campContext->search_articles_action->search_phrase;
        $searchResultsList = new SearchResultsList($start, $p_params);
        $campContext->setCurrentList($searchResultsList, array('publication', 'language',
    	                                                       'issue', 'section', 'article',
    	                                                       'image', 'attachment', 'comment',
    	                                                       'audioclip', 'subtitle'));
    }

    $currentSearchResult = $campContext->current_search_results_list->defaultIterator()->current();
    if (is_null($currentSearchResult)) {
        $p_repeat = false;
        $campContext->resetCurrentList();
        return $html;
    } else {
        $campContext->article = $currentSearchResult;
        $p_repeat = true;
    }

    if (isset($p_content)) {
        $html = $p_content;
        if ($p_repeat) {
            $campContext->current_search_results_list->defaultIterator()->next();
            if (!is_null($campContext->current_search_results_list->current)) {
                $campContext->article = $campContext->current_search_results_list->current;
            }
        }
    }

    return $html;
}

?>