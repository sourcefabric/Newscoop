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

require_once($g_documentRoot.'/classes/Issue.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');

/**
 * @package Campsite
 */
class MetaIssue extends MetaDbObject {

    public function __construct($p_publicationId, $p_languageId, $p_issueNumber)
    {
        $issueObj =& new Issue($p_publicationId, $p_languageId, $p_issueNumber);
		if (!is_object($issueObj) || !$issueObj->exists()) {
			return false;
		}
		$this->m_dbObject =& $issueObj;
    } // fn __construct

} // class MetaIssue

?>