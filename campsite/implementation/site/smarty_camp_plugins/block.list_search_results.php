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
        $p_params['search_results'] = $campContext->search_articles_action->search_results;
        
        $searchResultsList = new SearchResultsList($start, $p_params);
        if ($searchResultsList->isEmpty()) {
            $p_repeat = false;
            return null;
        }

        $campContext->url->set_parameter('f_search_articles', $campContext->search_articles_action->submit_button);
        $campContext->url->set_parameter('f_search_keywords', $p_params['search_phrase']);
        $campContext->url->set_parameter('f_search_level', $p_params['search_level']);
        $campContext->url->set_parameter('f_match_all', $p_params['match_all']);

        $campContext->setCurrentList($searchResultsList, array('publication', 'language',
    	                                                       'issue', 'section', 'article',
    	                                                       'image', 'attachment', 'comment',
    	                                                       'audioclip', 'subtitle'));
        $campContext->article = $campContext->current_search_results_list->current;
        $p_repeat = true;
    } else {
        $campContext->current_search_results_list->defaultIterator()->next();
        if (!is_null($campContext->current_search_results_list->current)) {
            $campContext->article = $campContext->current_search_results_list->current;
            $p_repeat = true;
        } else {
            $fields = array('f_search_articles', 'f_search_level', 'f_search_keywords', 'f_match_all',
            $campContext->current_list_id());
            foreach ($fields as $field) {
                $campContext->url->reset_parameter($field);
            }
            $campContext->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
}

?>