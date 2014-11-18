<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite captcha_image_link function plugin
 *
 * Type:     function
 * Name:     captcha_image_link
 * Purpose:
 *
 * @param empty
 *
 * @param object
 *     $p_smarty The Smarty object
 *
 * @return
 string     string the html string for the breadcrumb
 */
function smarty_function_captcha_image_link($p_params)
{
    $html = $GLOBALS['Campsite']['SUBDIR'].'/include/captcha/image.php';
    return $html;
} // fn smarty_function_captcha_image_link

?>