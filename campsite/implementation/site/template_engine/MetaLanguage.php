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
final class MetaLanguage extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['name'] = 'OrigName';
		$this->m_properties['number'] = 'Id';
		$this->m_properties['english_name'] = 'Name';
		$this->m_properties['code'] = 'Code';
	}


    public function __construct($p_languageId)
    {
        $languageObj =& new Language($p_languageId);
		if (!is_object($languageObj) || !$languageObj->exists()) {
			return false;
		}
		$this->m_dbObject =& $languageObj;
		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct

} // class MetaLanguage

?>