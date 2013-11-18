<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Get maximum upload file size
 */
class Admin_View_Helper_MaxFileSize extends Zend_View_Helper_Abstract
{
    /**
     * Get maximum upload file size
     *
     * @return string
     */
    public function maxFileSize()
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $maxFileSize = $preferencesService->MaxUploadFileSize;
        if (!$maxFileSize) {
            $maxFileSize = ini_get('upload_max_filesize');
        }

        return strtolower((string) $maxFileSize) . 'b';
    }
}
