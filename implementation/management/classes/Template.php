<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class Template extends DatabaseObject {
	var $m_dbTableName = 'Templates';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'Type', 'Level');
	
	/**
	 *
	 * @param int p_templateId
	 */
	function Template($p_templateId = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_templateId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * @param array $p_sqlOptions
	 */
	function GetAllTemplates($p_sqlOptions = null) 
	{
		$queryStr = 'SELECT * FROM Templates';
		if (!is_null($p_sqlOptions)) {
			$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		}
		else {
			$queryStr .= ' ORDER BY Level ASC, Name ASC';
		}
		$templates =& DbObjectArray::Create('Template', $queryStr);
		return $templates;
	} // fn GetAllTemplates
	
	
	function getTemplateId() 
	{
		return $this->getProperty('Id');
	} // fn getTemplateId
	
	
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName
	
	
	function getType() 
	{
		return $this->getProperty('Type');
	} // fn  getType
	
	
	function getLevel() 
	{
		return $this->getProperty('Level');
	} // fn getLevel
	
	
	function getAbsoluteUrl() 
	{
		global $Campsite;
		return $Campsite['WEBSITE_URL'].'/look/'.$this->getProperty('Name');
	}

	/**
	 * Returns true if the template path was valid
	 * @param string $p_path
	 * @return bool
	 */
	function IsValidPath($p_path)
	{
		global $Campsite;

		foreach (split("/", $p_path) as $index=>$dir) {
			if ($dir == "..")
				return false;
		}

		if (!is_dir($Campsite['HTML_DIR'] . "/look/$p_path"))
			return false;

		return true;
	}
} // class Template
?>