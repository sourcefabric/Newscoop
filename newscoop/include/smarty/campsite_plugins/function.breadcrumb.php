<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite breadcrumb function plugin
 *
 * Type:     function
 * Name:     breadcrumb
 * Purpose:  builds the breadcrumb for the current page. A 'first_level'
 *           attribute can be passed who indicates where the breadcrumb
 *           should start.
 *
 * @param array
 *     $p_params the date in unixtime format from $smarty.now
 * @param object
 *     $p_smarty The Smarty object
 *
 * @return
 string     string the html string for the breadcrumb
 *
 * @todo make it linkable
 */
function smarty_function_breadcrumb($p_params, &$p_smarty)
{
    $p_smarty->smarty->loadPlugin('smarty_shared_escape_special_chars');
    $campsite = $p_smarty->getTemplateVars('gimme');

    if (!isset($p_params['first_level']) || empty($p_params['first_level'])) {
        $p_params['first_level'] = 'home';
    }
    if (!isset($p_params['separator']) || empty($p_params['separator'])) {
        $p_params['separator'] = '&nbsp;&gt;&nbsp;';
    }

    $html = '';
    $breadcrumbStarted = 0;
    if ($p_params['first_level'] == 'home') {
        $html .= 'Home ' . $p_params['separator'];
        $breadcrumbStarted = 1;
    }
    if ($p_params['first_level'] == 'publication' || $breadcrumbStarted == 1
            && $campsite->publication->defined) {
        $html .= smarty_function_escape_special_chars($campsite->publication->name)
              . $p_params['separator'];
        $breadcrumbStarted = 1;
    }
    if ($p_params['first_level'] == 'issue' || $breadcrumbStarted == 1
            && $campsite->issue->defined) {
        $html .= smarty_function_escape_special_chars($campsite->issue->name)
              . $p_params['separator'];
        $breadcrumbStarted = 1;
    }
    if ($p_params['first_level'] == 'section' || $breadcrumbStarted == 1
            && $campsite->section->defined) {
        $html .= smarty_function_escape_special_chars($campsite->section->name)
              . $p_params['separator'];
        $breadcrumbStarted = 1;
    }
    if ($campsite->article->defined) {
        $html .= smarty_function_escape_special_chars($campsite->article->name)
              . $p_params['separator'];
    }

    return $html;

} // fn smarty_function_breadcrumb

?>
