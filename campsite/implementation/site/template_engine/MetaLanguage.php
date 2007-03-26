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

require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');

/**
 * @package Campsite
 */
class MetaLanguage extends MetaDbObject {

    public function __construct($p_languageId)
    {
        $languageObj =& new Language($p_languageId);
		if (!is_object($languageObj) || !$languageObj->exists()) {
			return false;
		}
		$this->m_dbObject =& $languageObj;
    } // fn __construct

} // class MetaLanguage

?>