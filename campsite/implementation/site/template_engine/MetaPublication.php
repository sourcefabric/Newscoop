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

require_once($g_documentRoot.'/classes/Publication.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');

/**
 * @package Campsite
 */
class MetaPublication extends MetaDbObject {

    public function __construct($p_publicationId)
    {
        $publicationObj =& new Publication($p_publicationId);
		if (!is_object($publicationObj) || !$publicationObj->exists()) {
			return false;
		}
		$this->m_dbObject =& $publicationObj;
    } // fn __construct

} // class MetaPublication

?>