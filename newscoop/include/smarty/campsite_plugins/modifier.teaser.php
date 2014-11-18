<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Includes the {@link shared.make_timestamp.php} plugin
 */
$smarty->smarty->loadPlugin('smarty_shared_make_timestamp');

/**
 * Campsite teaser modifier plugin
 *
 * Type:     modifier
 * Name:     teaser
 * Purpose:  build an teaser our of input
 *
 * @param string
 *     $p_input the string or object
 * @param string
 *     $p_format the date format wanted
 *
 * @return
 *     string the formatted date
 *     null in case a non-valid format was passed
 */
function smarty_modifier_teaser($p_input, $p_length=null)
{   
    $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

    if (empty($length)) {
        $length = is_null($preferencesService->teaser_length) ? 100 : $preferencesService->teaser_length;
    }
    $pattern = '/<!-- *break *-->/i';

    if (is_object($p_input) && method_exists($p_input, '__toString')) {
        $input = $p_input->__toString();
    } else {
        $input = (string) $p_input;
    }

    $output = node_teaser($input, null, $length);

    return $output;
} // fn smarty_modifier_camp_date_format


/**
 * Generate a teaser for a node body.
 *
 * If the end of the teaser is not indicated using the <!--break--> delimiter
 * then we generate the teaser automatically, trying to end it at a sensible
 * place such as the end of a paragraph, a line break, or the end of a
 * sentence (in that order of preference).
 *
 * @param $body
 *   The content for which a teaser will be generated.
 * @param $format
 *   The format of the content. If the content contains PHP code, we do not
 *   split it up to prevent parse errors. If the line break filter is present
 *   then we treat newlines embedded in $body as line breaks.
 * @param $size
 *   The desired character length of the teaser. If omitted, the default
 *   value will be used. Ignored if the special delimiter is present
 *   in $body.
 * @return
 *   The generated teaser.
 */
function node_teaser($body, $format = NULL, $size = NULL) {

    /*
    if (!isset($size)) {
        $size = variable_get('teaser_length', 600);
    }
    */

    // Find where the delimiter is in the body
    $delimiter = strpos($body, '<!--break-->');

    // If the size is zero, and there is no delimiter, the entire body is the teaser.
    if ($size == 0 && $delimiter === FALSE) {
        return $body;
    }

    // If a valid delimiter has been specified, use it to chop off the teaser.
    if ($delimiter !== FALSE) {
        return substr($body, 0, $delimiter);
    }

    /*
    // We check for the presence of the PHP evaluator filter in the current
    // format. If the body contains PHP code, we do not split it up to prevent
    // parse errors.
    if (isset($format)) {
    $filters = filter_list_format($format);
    if (isset($filters['php/0']) && strpos($body, '<?') !== FALSE) {
    return $body;
    }
    }
    */

    // If we have a short body, the entire body is the teaser.
    if (drupal_strlen($body) <= $size) {
        return $body;
    }

    // If the delimiter has not been specified, try to split at paragraph or
    // sentence boundaries.

    // The teaser may not be longer than maximum length specified. Initial slice.
    $teaser = truncate_utf8($body, $size);

    // Store the actual length of the UTF8 string -- which might not be the same
    // as $size.
    $max_rpos = strlen($teaser);

    // How much to cut off the end of the teaser so that it doesn't end in the
    // middle of a paragraph, sentence, or word.
    // Initialize it to maximum in order to find the minimum.
    $min_rpos = $max_rpos;

    // Store the reverse of the teaser.  We use strpos on the reversed needle and
    // haystack for speed and convenience.
    $reversed = strrev($teaser);

    // Build an array of arrays of break points grouped by preference.
    $break_points = array();

    // A paragraph near the end of sliced teaser is most preferable.
    $break_points[] = array('</p>' => 0);

    // If no complete paragraph then treat line breaks as paragraphs.
    $line_breaks = array('<br />' => 6, '<br>' => 4);
    // Newline only indicates a line break if line break converter
    // filter is present.
    if (isset($filters['filter/1'])) {
        $line_breaks["\n"] = 1;
    }
    $break_points[] = $line_breaks;

    // If the first paragraph is too long, split at the end of a sentence.
    $break_points[] = array('. ' => 1, '! ' => 1, '? ' => 1, '。' => 0, '؟ ' => 1);

    // Iterate over the groups of break points until a break point is found.
    foreach ($break_points as $points) {
        // Look for each break point, starting at the end of the teaser.
        foreach ($points as $point => $offset) {
            // The teaser is already reversed, but the break point isn't.
            $rpos = strpos($reversed, strrev($point));
            if ($rpos !== FALSE) {
                $min_rpos = min($rpos + $offset, $min_rpos);
            }
        }

        // If a break point was found in this group, slice and return the teaser.
        if ($min_rpos !== $max_rpos) {
            // Don't slice with length 0.  Length must be <0 to slice from RHS.
            return ($min_rpos === 0) ? $teaser : substr($teaser, 0, 0 - $min_rpos);
        }
    }

    // If a break point was not found, still return a teaser.
    return $teaser;
}



/**
 * Count the amount of characters in a UTF-8 string. This is less than or
 * equal to the byte count.
 */
function drupal_strlen($text) {
    global $multibyte;
    if ($multibyte == UNICODE_MULTIBYTE) {
        return mb_strlen($text);
    }
    else {
        // Do not count UTF-8 continuation bytes.
        return strlen(preg_replace("/[\x80-\xBF]/", '', $text));
    }
}



/**
 * Truncate a UTF-8-encoded string safely to a number of characters.
 *
 * @param $string
 *   The string to truncate.
 * @param $len
 *   An upper limit on the returned string length.
 * @param $wordsafe
 *   Flag to truncate at last space within the upper limit. Defaults to FALSE.
 * @param $dots
 *   Flag to add trailing dots. Defaults to FALSE.
 * @return
 string   The truncated string.
 */
function truncate_utf8($string, $len, $wordsafe = FALSE, $dots = FALSE) {

    if (drupal_strlen($string) <= $len) {
        return $string;
    }

    if ($dots) {
        $len -= 4;
    }

    if ($wordsafe) {
        $string = drupal_substr($string, 0, $len + 1); // leave one more character
        if ($last_space = strrpos($string, ' ')) { // space exists AND is not on position 0
        $string = substr($string, 0, $last_space);
        }
        else {
            $string = drupal_substr($string, 0, $len);
        }
    }
    else {
        $string = drupal_substr($string, 0, $len);
    }

    if ($dots) {
        $string .= ' ...';
    }

    return $string;
}


/**
 * Cut off a piece of a string based on character indices and counts. Follows
 * the same behavior as PHP's own substr() function.
 *
 * Note that for cutting off a string at a known character/substring
 * location, the usage of PHP's normal strpos/substr is safe and
 * much faster.
 * @param integer $start
 */
function drupal_substr($text, $start, $length = NULL) {
    global $multibyte;
    if ($multibyte == UNICODE_MULTIBYTE) {
        return $length === NULL ? mb_substr($text, $start) : mb_substr($text, $start, $length);
    }
    else {
        $strlen = strlen($text);
        // Find the starting byte offset
        $bytes = 0;
        if ($start > 0) {
            // Count all the continuation bytes from the start until we have found
            // $start characters
            $bytes = -1; $chars = -1;
            while ($bytes < $strlen && $chars < $start) {
                $bytes++;
                $c = ord($text[$bytes]);
                if ($c < 0x80 || $c >= 0xC0) {
                    $chars++;
                }
            }
        }
        else if ($start < 0) {
            // Count all the continuation bytes from the end until we have found
            // abs($start) characters
            $start = abs($start);
            $bytes = $strlen; $chars = 0;
            while ($bytes > 0 && $chars < $start) {
                $bytes--;
                $c = ord($text[$bytes]);
                if ($c < 0x80 || $c >= 0xC0) {
                    $chars++;
                }
            }
        }
        $istart = $bytes;

        // Find the ending byte offset
        if ($length === NULL) {
            $bytes = $strlen - 1;
        }
        else if ($length > 0) {
            // Count all the continuation bytes from the starting index until we have
            // found $length + 1 characters. Then backtrack one byte.
            $bytes = $istart; $chars = 0;
            while ($bytes < $strlen && $chars < $length) {
                $bytes++;
                $c = ord($text[$bytes]);
                if ($c < 0x80 || $c >= 0xC0) {
                    $chars++;
                }
            }
            $bytes--;
        }
        else if ($length < 0) {
            // Count all the continuation bytes from the end until we have found
            // abs($length) characters
            $length = abs($length);
            $bytes = $strlen - 1; $chars = 0;
            while ($bytes >= 0 && $chars < $length) {
                $c = ord($text[$bytes]);
                if ($c < 0x80 || $c >= 0xC0) {
                    $chars++;
                }
                $bytes--;
            }
        }
        $iend = $bytes;

        return substr($text, $istart, max(0, $iend - $istart + 1));
    }
}
?>