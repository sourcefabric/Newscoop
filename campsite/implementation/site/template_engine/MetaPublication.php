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
require_once($g_documentRoot.'/classes/Alias.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaPublication extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['name'] = 'Name';
		$this->m_properties['identifier'] = 'Id';
	}


    public function __construct($p_publicationId)
    {
        $publicationObj =& new Publication($p_publicationId);
		if (!is_object($publicationObj) || !$publicationObj->exists()) {
			return false;
		}
		$this->m_dbObject =& $publicationObj;
		$this->InitProperties();
		$this->m_customProperties['site'] = 'getDefaultSiteName';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


	protected function getDefaultSiteName()
	{
		$defaultAlias =& new Alias($this->m_dbObject->getDefaultAliasId());
		if (!$defaultAlias->exists()) {
			return null;
		}
		return $defaultAlias->getName();
	}

} // class MetaPublication

?>