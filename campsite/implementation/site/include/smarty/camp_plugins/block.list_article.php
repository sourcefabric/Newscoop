<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_article block plugin
 *
 * Type:     block
 * Name:     list_article
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
function smarty_block_list_article($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (isset($p_content)) {
	    CampTemplate::singleton()->trigger_error("Campsite error: cucu", $p_smarty);

    	$html .= "<pre>parameters:\n";
    	foreach ($p_params as $param=>$value) {
    		$html .= "$param = $value\n";
    	}
    	$html .= "</pre>\n";
    	$html .= "<pre>content:\n$p_content\n</pre>\n";
    }

    return $html;
}

?>
