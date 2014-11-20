<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com> & Mugur Rus <mugur.rus at sourcefabric dot org>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @param integer $from
 * @param integer $len
 * @return string
 */

function smarty_utf8_substr($str,$from,$len){
# utf8 substr
# www.yeap.lv
  return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
                       '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
                       '$1',$str);
} // fn smarty_utf8_substr


function smarty_modifier_truncate_utf8($string, $length = 80, $etc = '...',
									   $break_words = false, $middle = false)
{
    if ($length == 0) {
        return '';
    }

    if (strlen(utf8_decode($string)) > $length) {
        $length -= strlen(utf8_decode($etc));
        $initial_length = $length;
        $str_length = 0;

        // compute the cut length by adding the HTML tags
        $matches_no = preg_match_all('/(<[^>]+>)*([^<]*)/', $string, $matches, PREG_PATTERN_ORDER);
        for ($index = 0; $index < $matches_no; $index++) {
        	$length += mb_strlen($matches[1][$index]);
        	$str_length += mb_strlen($matches[2][$index]);
        	if ($str_length >= $initial_length) {
        		break;
        	}
        }
        $html_length = $length;

        // compute the cut length by adding the size of international characters encoded in HTML
        $str_length = 0;
        $matches_no = preg_match_all('/([^&]*)(&[a-zA-Z]+;|&#[0-9]+;)/', $string, $matches, PREG_PATTERN_ORDER);
        for ($index = 0; $index < $matches_no; $index++) {
        	$str_length += mb_strlen($matches[1][$index]) + 1;
        	if ($str_length >= $html_length) {
        		break;
        	}
        	$length += mb_strlen($matches[2][$index]) - 1;
        }

        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/u', '', smarty_utf8_substr($string, 0, $length+1));
        }
        if(!$middle) {
            return smarty_utf8_substr($string, 0, $length).$etc;
        } else {
            return smarty_utf8_substr($string, 0, $length/2) . $etc . smarty_utf8_substr($string, -$length/2);
        }
    } else {
        return $string;
    }
} // smarty_modifier_truncate

?>