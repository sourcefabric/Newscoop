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
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');

/**
 * @package Campsite
 */
class MetaTopic {

    public function __construct($p_topicId, $p_languageId = null)
    {
        if (is_null($p_languageId)) {
            $p_languageId = 1;
        }

        $topicObj = new Topic($p_topicId);
		if (!is_object($topicObj) || !$topicObj->exists()) {
			return false;
		}

        $this->m_data['Name'] = $topicObj->getName($p_languageId);
		$this->m_instance = true;
    } // fn __construct


    public function __get($p_property)
    {
        if (!is_array($this->m_data)) {
            return false;
        }
        if (!array_key_exists($p_property, $this->m_data)) {
            return false;
        }

        return $this->m_data[$p_property];
    } // fn __get

} // class MetaTopic

?>