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

require_once($g_documentRoot.'/classes/Topic.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');
require_once($g_documentRoot.'/template_engine/CampTemplate.php');

/**
 * @package Campsite
 */
final class MetaTopic extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['identifier'] = 'Id';
	}


    public function __construct($p_topicId)
    {
        $topicObj = new Topic($p_topicId);
		if (!is_object($topicObj) || !$topicObj->exists()) {
			return false;
		}
        $this->m_dbObject =& $topicObj;

		$this->InitProperties();
		$this->m_customProperties['name'] = 'getName';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    public function getName($p_languageId = null)
    {
    	if (is_null($p_languageId)) {
    		$smartyObj = CampTemplate::singleton();
    		$languageObj = $smartyObj->get_template_vars('language');
    		$p_languageId = $languageObj->number;
    	}
    	return $this->m_dbObject->getName($p_languageId);
    }

} // class MetaTopic

?>