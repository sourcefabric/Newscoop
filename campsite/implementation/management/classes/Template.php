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
	function GetAllTemplates($p_sqlOptions = null, $p_update = true)
	{
		if ($p_update) {
			Template::UpdateStatus();
		}
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
	
	
	function UpdateStatus()
	{
		global $Campsite;
		
		// check if each template record in the database has the corresponding file
		$templates = Template::GetAllTemplates(null, false);
		foreach ($templates as $index=>$template) {
			if (!$template->fileExists()) {
				$Campsite['db']->Execute("DELETE FROM Templates WHERE Id = "
					. $template->m_data['Id']);
			}
		}
		
		// insert new templates
		$rootDir = $Campsite['HTML_DIR'] . "/look";
		$dirs[] = $rootDir;
		while (($currDir = array_pop($dirs)) != "") {
			if (!$dirHandle = opendir($currDir)) {
				continue;
			}
			
			while ($file = readdir($dirHandle)) {
				if ($file == "." || $file == "..") {
					continue;
				}
				
				$fullPath = $currDir . "/" . $file;
				$fileType = filetype($fullPath);
				if ($fileType == "dir") {
					$dirs[] = $fullPath;
					continue;
				}
				
				if ($fileType != "file") { // ignore special files and links
					continue;
				}
				$ending = substr($file, strlen($file) - 4);
				if ($ending != ".tpl") { // ignore files that are not templates (end in .tpl)
					continue;
				}
				
				$relPath = substr($fullPath, strlen($rootDir) + 1);
				$level = $relPath == "" ? 0 : substr_count($relPath, "/");
				$sql = "SELECT count(*) AS nr FROM Templates WHERE Name = '" . $relPath . "'";
				$existingTemplates = $Campsite['db']->GetOne($sql);
				if ($existingTemplates == 0) {
					$sql = "INSERT IGNORE INTO Templates (Name, Level) VALUES('$relPath', $level)";
					$Campsite['db']->Execute($sql);
				}
			}
		}
	}
	
	
	function fileExists()
	{
		global $Campsite;
		
		if (!Template::IsValidPath(dirname($this->m_data['Name']))) {
			return false;
		}
		if (!is_file($Campsite['HTML_DIR'] . "/look/" . $this->m_data['Name'])) {
			return false;
		}
		return true;
	}
	
	
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