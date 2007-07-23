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

require_once($g_documentRoot.'/classes/Template.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaTemplate extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['name'] = 'Name';
	}


    public function __construct($p_templateId = null)
    {
        $this->m_dbObject =& new Template($p_templateId);

		$this->InitProperties();
        $this->m_customProperties['type'] = 'getType';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    public function getType()
    {
    	global $g_ado_db;

    	$templateTypeId = $this->m_dbObject->getType();
    	return $g_ado_db->GetOne("SELECT Name FROM TemplateTypes WHERE Id = $templateTypeId");
    }

} // class MetaTopic

?>