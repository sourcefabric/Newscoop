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
    	foreach ($p_params as $param=>$value) {
    		$param = strtolower($param);
    		switch ($param) {
    			case 'length':
    			case 'columns':
    			case 'name':
    			case 'constraints':
    			case 'order':
    				if ($param == 'length' || $param == 'columns') {
    					$intValue = (int)$value;
    					if ("$intValue" != $value) {
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $param in statement list_article");
    					}
	    				$$param = (int)$value;
    				} else {
	    				$$param = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $param in list_article", $p_smarty);
    		}
    	}
    	echo "<p>length: $length</p>\n";
    	echo "<p>columns: $columns</p>\n";
    	echo "<p>name: $name</p>\n";
    	echo "<p>constraints: $constraints</p>\n";
    	echo "<p>order: $order</p>\n";
    	$html .= "</pre>\n";
    	$html .= "<pre>content:\n$p_content\n</pre>\n";
    }

    return $html;
}

?>
