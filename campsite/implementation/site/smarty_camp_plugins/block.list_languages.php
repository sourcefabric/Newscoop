<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_languages block plugin
 *
 * Type:     block
 * Name:     list_languages
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
function smarty_block_list_languages($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('LanguagesList');
        $languagesList = new LanguagesList($start, $p_params);
        if ($languagesList->isEmpty()) {
            $campContext->setCurrentList($languagesList, array());
            $campContext->resetCurrentList();
        	$p_repeat = false;
            return null;
        }
    	$campContext->setCurrentList($languagesList, array('publication', 'language',
    	                                                  'issue', 'section', 'article',
    	                                                  'image', 'attachment', 'comment',
    	                                                  'audioclip', 'subtitle'));
    	$campContext->language = $campContext->current_list->current;
    	$p_repeat = true;
    } else {
        $campContext->current_list->defaultIterator()->next();
    	if (!is_null($campContext->current_list->current)) {
    	    $campContext->language = $campContext->current_list->current;
    	    $p_repeat = true;
    	} else {
    	    $campContext->resetCurrentList();
            $p_repeat = false;
    	}
    }

    return $p_content;
}

?>