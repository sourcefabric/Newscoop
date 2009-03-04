<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Includes the {@link shared.make_timestamp.php} plugin
 */
require_once $smarty->_get_plugin_filepath('shared','make_timestamp');

/**
 * Campsite camp_date_format modifier plugin
 *
 * Type:     modifier
 * Name:     camp_date_format
 * Purpose:  format datestamps via MySQL date and time functions
 *
 * @param string
 *     $p_unixtime the date in unixtime format from $smarty.now
 * @param string
 *     $p_format the date format wanted
 *
 * @return
 *     string the formatted date
 *     null in case a non-valid format was passed
 */
function smarty_modifier_teaser($p_input)
{
    $pattern = '/<!-- *break *-->/i';
    
    if (preg_match($pattern, $p_input, $matches, PREG_OFFSET_CAPTURE)) {
        $length = $matches[0][1];
        $output = substr($p_input, 0, $length);
        $output .= '[...]';
    } else {
        static $length;
        
        if (empty($length)) {
            $length = is_null(SystemPref::Get('teaser_length')) ? 150 : SystemPref::Get('teaser_length');
        }
        $output = mb_substr($p_input, 0, $length);
        $output .= '[...]';
    }
    
    return $output;
} // fn smarty_modifier_camp_date_format

?>