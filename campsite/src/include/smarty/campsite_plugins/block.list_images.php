<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_images block plugin
 *
 * Type:     block
 * Name:     list_images
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
function smarty_block_list_images($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('ImagesList');
        $imagesList = new ImagesList($start, $p_params);
        if ($imagesList->isEmpty()) {
            $campContext->setCurrentList($imagesList, array());
            $campContext->resetCurrentList();
        	$p_repeat = false;
            return null;
        }
    	$campContext->setCurrentList($imagesList, array('image'));
    	$campContext->image = $campContext->current_images_list->current;
    	$p_repeat = true;
    } else {
        $campContext->current_images_list->defaultIterator()->next();
        if (!is_null($campContext->current_images_list->current)) {
            $campContext->image = $campContext->current_images_list->current;
            $p_repeat = true;
        } else {
            $campContext->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
}

?>