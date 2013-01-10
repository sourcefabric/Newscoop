<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_weather_stats block plugin
 *
 * Type:     block
 * Name:     list_weather_stats
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
function smarty_block_list_weather_stats($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    $context = $p_smarty->getTemplateVars('gimme');

    if (!isset($p_content)) {
        $start = $context->next_list_start('WeatherStatList');
    	$weatherStatList = new WeatherStatList($start, $p_params);
    	if ($weatherStatList->isEmpty()) {
            $context->setCurrentList($weatherStatList, array());
            $context->resetCurrentList();
    		$p_repeat = false;
    	    return null;
    	}
    	$context->setCurrentList($weatherStatList, array('weather_location'));
    	$context->weather_location = $context->current_weather_stat_list->current;
    	$p_repeat = true;
    } else {
        $context->current_list->defaultIterator()->next();
        if (!is_null($context->current_weather_stat_list->current)) {
            $context->weather_location = $context->current_weather_stat_list->current;
            $p_repeat = true;
        } else {
            $context->resetCurrentList();
            $p_repeat = false;
        }
    }

    return $p_content;
}
