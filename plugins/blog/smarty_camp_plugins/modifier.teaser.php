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
function smarty_modifier_teaser($p_input)
{
    $pattern = '/<!-- *break *-->/i';
    
    if (is_object($p_input) && method_exists($p_input, '__toString')) {
        $input = $p_input->__toString();
    } else {
         $input = (string) $p_input;
    }
    
    if (preg_match($pattern, $input, $matches, PREG_OFFSET_CAPTURE)) {
        $length = $matches[0][1];
        $output = substr($input, 0, $length);
        $output .= '[...]';
    } else {
        static $length;
        
        if (empty($length)) {
            $length = is_null(SystemPref::Get('teaser_length')) ? 150 : SystemPref::Get('teaser_length');
        }
        $output = mb_substr($input, 0, $length, 'UTF-8');
        $output .= '[...]';
    }
    
    return $output;
} // fn smarty_modifier_camp_date_format

?>