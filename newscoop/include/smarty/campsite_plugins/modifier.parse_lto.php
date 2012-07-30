<?php
/**
 * @package Newscoop 
 */

/**
 * Newscoop split paragraphs modifier plugin
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
    preg_match_all('#<p>(.*)</p>#Us', $text, $matches);
    if (!isset($matches[1])) {
        return null;
    }

    $items = !is_null($limit) ? array_slice($matches[1], 0, $limit) : $matches[1];
    $newstext = '';
    foreach ($items as $item) {
        $pattern = '/^(\w+\.)/i';
        $replace = '<strong>$1</strong>';
        $boldeditem = preg_replace($pattern, $replace, $item, 1);
        $newstext .= '<p>' . $boldeditem . '</p>';
    }

    return $newstext;
}
