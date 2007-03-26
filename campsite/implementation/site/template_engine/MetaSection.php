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

require_once($g_documentRoot.'/classes/Section.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');

/**
 * @package Campsite
 */
class MetaSection extends MetaDbObject {

    public function __construct($p_publicationId, $p_issueNumber,
                                $p_languageId, $p_sectionNumber)
    {
        $sectionObj = new Section($p_publicationId, $p_issueNumber,
                                  $p_languageId, $p_sectionNumber);
		if (!is_object($sectionObj) || !$sectionObj->exists()) {
			return false;
		}
		$this->m_dbObject = $sectionObj;
    } // fn __construct

} // class MetaSection

?>