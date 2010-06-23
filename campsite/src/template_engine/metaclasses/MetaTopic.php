<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Topic.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');

/**
 * @package Campsite
 */
final class MetaTopic extends MetaDbObject {

    public function __construct($p_topicIdOrName = null)
    {
        $this->m_dbObject = new Topic($p_topicIdOrName);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Topic();
        }

        $this->m_properties['identifier'] = 'Id';

        $this->m_customProperties['name'] = 'getName';
        $this->m_customProperties['value'] =  'getValue';
        $this->m_customProperties['is_root'] = 'isRoot';
        $this->m_customProperties['parent'] = 'getParent';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    public function getName($p_languageId = null)
    {
    	if (is_null($p_languageId)) {
    		$p_languageId = CampTemplate::singleton()->context()->language->number;
    	}
    	return $this->m_dbObject->getName($p_languageId);
    }


    protected function getValue()
    {
        if (!isset($this->m_dbObject) || !$this->m_dbObject->exists()) {
            return null;
        }

        $language = CampTemplate::singleton()->context()->language;
        $name = $this->m_dbObject->getName($language->number);
        if (empty($name)) {
            return null;
        }
        return $name.':'.$language->code;
    }


    protected function isRoot()
    {
        return (int)$this->m_dbObject->isRoot();
    }


    protected function getParent()
    {
        return new MetaTopic($this->m_dbObject->getParentId());
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