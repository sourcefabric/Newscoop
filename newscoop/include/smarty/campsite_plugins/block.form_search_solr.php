<?php
/**
 * @package Newscoop
 */

/**
 * Search Form
 *
 * @param string $params
 * @param string $content
 * @param object $smarty
 * @return string
 */
function smarty_block_form_search_solr($params, $content, $smarty)
{
    if (empty($content)) {
        return;
    }

    $view = $smarty->getTemplateVars('view');
    $params += array(
        'method' => 'GET',
        'action' => $view->url(array('controller' => 'search', 'action' => 'index'), 'default'),
    );

    return $view->form('search_articles', $params, $content);
}
