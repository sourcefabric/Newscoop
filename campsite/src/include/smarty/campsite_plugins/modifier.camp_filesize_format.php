<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite camp_filesize_format modifier plugin
 *
 * Type:    modifier
 * Name:    camp_filesize_format
 * Purpose: formats the size for the given file to human-readable format
 *
 * Usage:   In the template, use for example (assuming file size = 89522 bytes)
 *          {{ $campsite->attachment->size_b|camp_filesize_format }}
 *          which result in => 87.42 KB
 *          or
 *          {{ $campsite->attachment->size_b|camp_filesize_format:"MB" }}
 *          which result in => 0.09 MB
 *          or
 *          use whichever of the other format types as:
 *          B = Bytes
 *          KB = Kilobytes
 *          MB = Megabytes
 *          GB = Gigabytes
 *          TB = Terabytes
 *
 * @param string $p_size
 *      The original file size (in bytes)
 * @param string $p_format
 *      The file size format wanted
 * @param integer $p_precision
 *      The rounding preicision
 *
 * @return string
 *      The formatted file size
 */
function smarty_modifier_camp_filesize_format($p_size,
                                              $p_format = null,
                                              $p_precision = 2)
{
    $sizes = array(
                   'TB' => 1099511627776,
                   'GB' => 1073741824,
                   'MB' => 1048576,
                   'KB' => 1024,
                   'B'  => 1
                   );

    if (!empty($p_format) && !array_key_exists(strtoupper($p_format), $sizes)) {
        return $p_size;
    }
    
    foreach($sizes as $unit => $bytes) {
        if($p_size > $bytes || $unit == strtoupper($p_format)) {
            return number_format($p_size / $bytes, $p_precision) . " " . $unit;
        }
    }
} // fn smarty_modifier_camp_filesize_format

?>