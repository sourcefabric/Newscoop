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
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');
require_once($g_documentRoot.'/template_engine/classes/CampTemplate.php');


/**
 * @package Campsite
 */
final class MetaAttachment extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['file_name'] = 'file_name';
		$this->m_properties['mime_type'] = 'mime_type';
		$this->m_properties['extension'] = 'extension';
		$this->m_properties['size_b'] = 'size_in_bytes';
	}


    public function __construct($p_attachmentId = null)
    {
        $this->m_dbObject =& new Attachment($p_attachmentId);

		$this->InitProperties();
        $this->m_customProperties['description'] = 'getDescription';
        $this->m_customProperties['size_kb'] = 'getSizeKB';
        $this->m_customProperties['size_mb'] = 'getSizeMB';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    public function getDescription($p_languageId = null)
    {
    	if (is_null($p_languageId)) {
    		$smartyObj = CampTemplate::singleton();
    		$contextObj = $smartyObj->get_template_vars('campsite');
    		$p_languageId = $contextObj->language->number;
    	}
    	return $this->m_dbObject->getDescription($p_languageId);
    }


	public function getSizeKB()
	{
		return (int)($this->m_dbObject->getSizeInBytes() / 1024);
	}


	public function getSizeMB()
	{
		return (int)($this->m_dbObject->getSizeInBytes() / 1048576);
	}

} // class MetaAttachment

?>