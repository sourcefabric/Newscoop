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
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');
require_once($g_documentRoot.'/template_engine/classes/CampTemplate.php');

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


    public function __construct($p_topicIdOrName = null)
    {
        $this->m_dbObject = new Topic($p_topicIdOrName);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = null;
            throw new InvalidValueException($p_topicIdOrName, 'topic');
        }

		$this->InitProperties();
		$this->m_customProperties['name'] = 'getName';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    public function getName($p_languageId = null)
    {
    	if (is_null($p_languageId)) {
    		$smartyObj = CampTemplate::singleton();
    		$contextObj = $smartyObj->get_template_vars('campsite');
    		$p_languageId = $contextObj->language->number;
    	}
    	return $this->m_dbObject->getName($p_languageId);
    }


    public function getValue()
    {
        if (!isset($this->m_dbObject) || !$this->m_dbObject->exists()) {
            return null;
        }

        $campContext = CampTemplate::singleton()->context();
        $name = $this->m_dbObject->getName($campContext->language->number);
        $languageCode = $campContext->language->code;
        return "$name:$languageCode";
    }


    public function IsValid($p_value)
    {
        $topic = Topic::GetByFullName($p_value);
        return !is_null($topic);
    }


    public static function GetTypeName()
    {
        return 'topic';
    }
} // class MetaTopic

?>