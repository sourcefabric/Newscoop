<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite set_article function plugin
 *
 * Type:     function
 * Name:     set_article
 * Purpose:  
 *
 * @param array
 *     $p_params[name] The Name of the article to be set
 *     $p_params[number] The Number of the article to be set
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_set_article($p_params, &$p_smarty)
{
    global $g_ado_db;

    // gets the context variable
    $camp = $p_smarty->get_template_vars('camp');

    $attrValue = 0;
    if (isset($p_params['number']) && !empty($p_params['number'])) {
        $attrValue = intval($p_params['number']);
    } elseif (isset($p_params['name']) && !empty($p_params['name'])) {
        $queryStr = "SELECT Number FROM Articles "
                  . "WHERE IdLanguage = ".$camp->language->number
                  . " AND Name = '".$g_ado_db->escape($p_params['name'])."'";
        $row = $g_ado_db->GetRow($queryStr);
        if ($row['Number'] > 0) {
            $attrValue = $row['Number'];
        }
    }

    if (!$attrValue) {
        return false;
    }
    if ($camp->article->defined && $camp->article->number == $attrValue) {
        return;
    }

    $article = new MetaArticle($camp->language->number, $attrValue);
    if ($article->defined == 'defined') {
        $camp->article = $article;
        $p_smarty->assign('article', $camp->article);
    }

} // fn smarty_function_set_article

?>