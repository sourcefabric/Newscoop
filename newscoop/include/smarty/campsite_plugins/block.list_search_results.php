<?php
/**
 */

/**
 * Smarty block list search results
 *
 * @param array $params
 * @param string $content
 * @param Smarty $smarty
 * @param bool $repeat
 * @return string
 */
function smarty_block_list_search_results($params, $content, $smarty, &$repeat)
{
    if (empty($params['q'])) {
        $params += array('q_param' => 'q');
        $params['q'] = !empty($_GET[$params['q_param']]) ? $_GET[$params['q_param']] : null;
    }

    if (empty($params['q'])) {
        $repeat = false;
        return;
    }

    $gimme = $smarty->getTemplateVars('gimme');
    if (empty($params['language'])) {
        $params['language'] = $gimme->language->code;
    }

    if (empty($params['start'])) {
        $params['start'] = !empty($_GET['start']) ? (int) $_GET['start'] : 0;
    }

    if (empty($content)) {
        $start = $gimme->next_list_start('SearchResultsList');
        $searchResultsList = new SearchResultsList($start, $params);
        if ($searchResultsList->isEmpty()) {
        	$gimme->setCurrentList($searchResultsList, array());
            $gimme->resetCurrentList();
        	$repeat = false;
            return null;
        }

        $gimme->setCurrentList($searchResultsList, array(
            'publication',
            'language',
            'issue',
            'section',
            'article',
            'image',
            'attachment',
            'comment',
            'subtitle',
        ));

        $gimme->article = $gimme->current_search_results_list->current;
        $repeat = true;
    } else {
        $gimme->current_search_results_list->defaultIterator()->next();
        if (!is_null($gimme->current_search_results_list->current)) {
            $gimme->article = $gimme->current_search_results_list->current;
            $repeat = true;
        } else {
            $gimme->resetCurrentList();
            $repeat = false;
        }
    }

    return $content;
}
