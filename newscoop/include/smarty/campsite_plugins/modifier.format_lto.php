<?php
/**
 * @package Newscoop 
 */

/**
 * Newscoop lto item formater
 *
 * Type:    modifier
 * Name:    format_lto 
 *
 * @param string $text
 * @return string
 */
function smarty_modifier_format_lto($text)
{
    $pattern = '/^(\w+\.)/i';
    $replace = '<strong>$1</strong>';
    $boldedItem = preg_replace($pattern, $replace, $text, 1);
    return sprintf("<p>%s</p>\n", $boldedItem);
}
