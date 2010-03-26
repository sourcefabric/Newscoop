<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_subtitles block plugin
 *
 * Type:     block
 * Name:     list_subtitles
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
function smarty_block_list_subtitles($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    static $subtitleURLId = null;
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('SubtitlesList');
        $subtitlesList = new SubtitlesList($start, $p_params);
        if ($subtitlesList->isEmpty()) {
            $campContext->setCurrentList($subtitlesList, array());
            $campContext->resetCurrentList();
        	$p_repeat = false;
            return null;
        }
        $campContext->setCurrentList($subtitlesList, array('subtitle'));
        $campContext->subtitle = $campContext->current_subtitles_list->current;
        $subtitleURLId = $campContext->article->subtitle_url_id($campContext->subtitle->field_name);
        $campContext->url->set_parameter($subtitleURLId, $campContext->subtitle->number);
        $p_repeat = true;
    } else {
        $campContext->current_subtitles_list->defaultIterator()->next();
        if (!is_null($campContext->current_subtitles_list->current)) {
            $campContext->subtitle = $campContext->current_subtitles_list->current;
            $subtitleURLId = $campContext->article->subtitle_url_id($campContext->subtitle->field_name);
            $campContext->url->set_parameter($subtitleURLId, $campContext->subtitle->number);
            $p_repeat = true;
        } else {
            $campContext->url->reset_parameter($subtitleURLId);
            $campContext->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
}

?>