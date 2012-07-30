<?php
/**
 * @package Newscoop 
 */

/**
 * Newscoop lto article parser
 *
 * Type:    modifier
 * Name:    parse_lto 
 *
 * @param string $text
 *      Article content
 *
 * @return string
 *      Modified article text
 */
function smarty_modifier_parse_lto($text, $limit = null)
{
    $matches = array();
    preg_match_all('#<p>(.*)</p>#Us', $text, $matches);
    return isset($matches[1]) ? $matches[1] : array();
}
