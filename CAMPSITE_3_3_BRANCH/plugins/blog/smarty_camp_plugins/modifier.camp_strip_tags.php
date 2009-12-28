<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite camp_strip_tags modifier plugin
 *
 * Type:     modifier
 * Name:     camp_strip_tags
 * Purpose:  strip html tags from input
 * Note:     if input is an object, it will use the __toString() method if available
 *
 * @param string
 *     $p_input the string or object
 * @param string
 *     $p_allowed_tags allowed tags, see description of pgp build-in strip_tags
 *
 * @return
 *     string the stripped string
 */
function smarty_modifier_camp_strip_tags($p_input, $p_allowed_tags = '<a>,<i>,<b>')
{
    if (is_object($p_input) && method_exists($p_input, '__toString')) {
        $input = $p_input->__toString();
    } else {
         $input = (string) $p_input;
    }

    return strip_tags($input, $p_allowed_tags);
} // fn smarty_modifier_camp_strip_tags


?>
