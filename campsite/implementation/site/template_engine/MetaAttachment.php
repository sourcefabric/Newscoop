<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/Attachment.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');


/**
 * @package Campsite
 */
final class MetaAttachment {

    public function __construct($p_attachmentId)
    {
        $attachmentObj =& new Attachment($p_attachmentId);

        if (!is_object($attachmentObj) || !$attachmentObj->exists()) {
            return false;
        }
        $this->m_dbObject =& $attachmentObj;
    } // fn __construct

} // class MetaAttachment

?>