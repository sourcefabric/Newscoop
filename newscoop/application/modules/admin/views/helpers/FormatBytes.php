<?php

/**
 * Format bytes helper
 */
class Admin_View_Helper_FormatBytes extends Zend_View_Helper_Abstract
{
    /** @var array */
    private static $units = array('B&nbsp;', 'kB', 'MB', 'GB', 'TB');

    /**
     * Convert bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    public function formatBytes($bytes)
    {
        foreach (self::$units as $unit) {
            if ($bytes < 100) {
                return sprintf('%.2f %s', $bytes, $unit);
            }

            $bytes /= 1024;
        }

    }
}
