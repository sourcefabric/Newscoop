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

/**
 * @package Campsite
 */
class MetaTopic {
    //
    private $m_data = null;
    //
	private $m_instance = false;
	//
    private $m_baseFields = array(
                                  'Id',
                                  'Name'
                                  );


    public function __construct($p_topicId, $p_languageId = null)
    {
        if (is_null($p_languageId)) {
            $p_languageId = 1;
        }

        $topicObj = new Topic($p_topicId);
		if (!is_object($topicObj) || !$topicObj->exists()) {
			return false;
		}
		foreach ($topicObj->m_data as $key => $value) {
            if (in_array($key, $this->m_baseFields)) {
                $this->m_data[$key] = $value;
            }
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


    public function __set($p_property, $p_value)
    {
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


    public function defined()
    {
		return $this->m_instance;
    } // fn defined

} // class MetaTopic

?>