<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite obfuscate_email modifier plugin
 *
 * Type:    modifier
 * Name:    obfuscate_email
 * Purpose: obfuscates the email address so that spambot web crawlers 
 *          won't be able to find it.
 *
 * @param string $p_email
 *      The email address
 *
 * @return string
 *      The obfuscated email address
 */
function smarty_modifier_obfuscate_email($p_email)
{
    if (empty($p_email)) {
        return;
    }

    $buffer = '';
    for ($i = 0; $i < strlen($p_email); $i++) {
        $buffer .= '&#' . ord($p_email[$i]) . ';';
    }

    return $buffer;
} // fn smarty_modifier_obfuscate_email

?>