<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite list_map_locations block plugin
 *
 * Type:     block
 * Name:     list_map_locations
 * Purpose:  Provides a list of locations for the multi-map in context
 *
 * @param string $p_params
 * @param string $p_smarty
 * @param string $p_content
 *
 * @return string
 */
function smarty_block_list_map_locations($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    // gets the context variable
    $campContext = $p_smarty->get_template_vars('gimme');

    if (!isset($p_content)) {
        $start = $campContext->next_list_start('MapLocationsList');
        $mapLocationsList = new MapLocationsList($start, $p_params);
        if ($mapLocationsList->isEmpty()) {
            $campContext->setCurrentList($mapLocationsList, array());
            $campContext->resetCurrentList();
            $p_repeat = false;
            return null;
        }
        $campContext->setCurrentList($mapLocationsList, array('location'));
        $campContext->location = $campContext->current_map_locations_list->current;
        $p_repeat = true;
    } else {
        $campContext->current_map_locations_list->defaultIterator()->next();
        if (!is_null($campContext->current_map_locations_list->current)) {
            $campContext->location = $campContext->current_map_locations_list->current;
            $p_repeat = true;
        } else {
            $campContext->resetCurrentList();
            $p_repeat = false;
        }
    }
    return $p_content;
}

?>
