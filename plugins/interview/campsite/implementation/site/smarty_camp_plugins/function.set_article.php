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
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('campsite');

    if (isset($p_params['number'])) {
    	$attrName = 'number';
        $attrValue = $p_params['number'];
        $articleNumber = intval($p_params['number']);
    } elseif (isset($p_params['name'])) {
    	$articles = Article::GetByName($p_params['name'],
    								   $campsite->publication->identifier,
    								   $campsite->issue->number,
    								   $campsite->section->number,
    								   $campsite->language->number);
        if (isset($articles[0])) {
        	$attrName = 'name';
        	$attrValue = $p_params['name'];
            $articleNumber = intval($articles[0]->getArticleNumber());
        } else {
	    	$campsite->article->trigger_invalid_value_error($attrName, $attrValue, $p_smarty);
        	return false;
        }
    } else {
    	$property = array_shift(array_keys($p_params));
    	CampTemplate::singleton()->trigger_error("invalid parameter '$property' in set_article");
        return false;
    }

    if ($campsite->article->defined && $campsite->article->number == $attrValue) {
        return;
    }

    $articleObj = new MetaArticle($campsite->language->number, $articleNumber);
    if ($articleObj->defined) {
        $campsite->article = $articleObj;
    } else {
    	$campsite->article->trigger_invalid_value_error($attrName, $attrValue, $p_smarty);
    }
} // fn smarty_function_set_article

?>