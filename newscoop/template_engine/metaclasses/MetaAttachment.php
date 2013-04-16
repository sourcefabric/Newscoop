<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');


/**
 * @package Campsite
 */
final class MetaAttachment extends MetaDbObject {

	private static $m_baseProperties = array(
	'identifier'=>'id',
    'file_name'=>'file_name',
    'mime_type'=>'mime_type',
    'extension'=>'extension',
    'size_b'=>'size_in_bytes'
	);
	
	private static $m_defaultCustomProperties = array(
    'description'=>'getDescription',
    'size_kb'=>'getSizeKB',
    'size_mb'=>'getSizeMB',
    'defined'=>'defined'
	);


    public function __construct($p_attachmentId = null)
    {
    	$this->m_properties = self::$m_baseProperties;
    	$this->m_customProperties = self::$m_defaultCustomProperties;

        $this->m_dbObject = new Attachment($p_attachmentId);
        if (!$this->m_dbObject->exists()) {
        	$this->m_dbObject = new Attachment();
        }
    } // fn __construct


    protected function getDescription($p_languageId = null)
    {
    	if (is_null($p_languageId)) {
    		$smartyObj = CampTemplate::singleton();
    		$contextObj = $smartyObj->getTemplateVars('gimme');
    		$p_languageId = $contextObj->language->number;
    	}
    	return $this->m_dbObject->getDescription($p_languageId);
    }


	protected function getSizeKB()
	{
		return (int)($this->m_dbObject->getSizeInBytes() / 1024);
	}


	protected function getSizeMB()
	{
		return (int)($this->m_dbObject->getSizeInBytes() / 1048576);
	}

} // class MetaAttachment

?>