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

require_once($g_documentRoot.'/classes/Section.php');

/**
 * @package Campsite
 */
class MetaSection {
    //
    private $m_data = null;
    //
	private $m_instance = false;
	//
    private $m_baseFields = array(
                                  'Name',
                                  'Number'
                                  );


    public function __construct($p_publicationId, $p_issueNumber,
                                $p_languageId, $p_sectionNumber)
    {
        $sectionObj = new Section($p_publicationId, $p_issueNumber,
                                  $p_languageId, $p_sectionNumber);
		if (!is_object($sectionObj) || !$sectionObj->exists()) {
			return false;
		}
		foreach ($sectionObj->m_data as $key => $value) {
            if (in_array($key, $this->m_baseFields)) {
                $this->m_data[$key] = $value;
            }
		}
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

} // class MetaSection

?>