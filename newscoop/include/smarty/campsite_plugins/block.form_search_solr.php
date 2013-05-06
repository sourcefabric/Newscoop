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

    $language = null;
    $gimme = $smarty->getTemplateVars('gimme');
    if ($gimme->language->code !== $gimme->publication->default_language->code) {
        $language = $gimme->language->code;
    }

    $view = $smarty->getTemplateVars('view');
    $params += array(
        'method' => 'GET',
        'action' => $view->url(array('language' => $language), 'search'),
    );

    return $view->form('search_articles', $params, $content);
}
