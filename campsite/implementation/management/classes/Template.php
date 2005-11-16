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
	} // fn getAbsoluteUrl

	
	function GetContents($p_path)
	{
		if (file_exists($p_path)) {
			$contents = file_get_contents($p_path);
		} else {
			$contents = "";
		}
		return $contents;
	} // fn GetContents
	
	
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
	} // fn IsValidPath
	
	
	/**
	 * Returns TRUE if the template is being used in an Issue or Section.
	 * @return boolean
	 */
	function InUse($p_templateName)
	{
		global $Campsite;
		$queryStr = "SELECT * FROM Templates WHERE Name = '$p_templateName'";
		$row = $Campsite['db']->GetRow($queryStr);
		if (!$row) {
			return false;
		}
		$id = $row['Id'];
	
		$queryStr = "SELECT COUNT(*) FROM Issues "
					." WHERE IssueTplId = " . $id
		     		." OR SectionTplId = " . $id 
		     		." OR ArticleTplId = " . $id;
		$numMatches = $Campsite['db']->GetOne($queryStr);
		if ($numMatches > 0) {
			return true;
		}
		
		$queryStr = "SELECT COUNT(*) FROM Sections "
					." WHERE SectionTplId = " . $id
		     		." OR ArticleTplId = " . $id;
		$numMatches = $Campsite['db']->GetOne($queryStr);
		if ($numMatches > 0) {
			return true;
		}
		return false;
	} // fn InUse
	
	
	/**
	 * Get the relative path to the given file in the template directory.
	 * @return string
	 */
	function GetPath($p_path, $p_filename)
	{
		$lookDir = "/look";
	
		$p_path = str_replace("//", "/", $p_path);
		
		// Remove any path that occurs before the 'look' dir.
		$p_path = strstr($p_path, $lookDir);
		
		// Remove the 'look' dir if it occurs at the beginning of the string.
		if (strncmp($p_path, $lookDir, strlen($lookDir)) == 0) {
			$p_path = substr($p_path, strlen($lookDir));
		}
		
		// Remove beginning and trailing slashes
		if ($p_path[0] == '/') {
			$p_path = substr($p_path, 1);
		}
		if ($p_path[strlen($p_path) - 1] == '/') {
			$p_path = substr($p_path, 0, strlen($p_path) - 1);
		}
		
		$p_filename = str_replace("//", "/", $p_filename);
		
		// Remove beginning slash
		if ($p_filename[0] == '/') {
			$p_filename = substr($p_filename, 1);
		}
	
		if (!empty($p_path)) {
			$templatePath = $p_path . "/" . $p_filename;
		} else {
			$templatePath = $p_filename;
		}
		return $templatePath;
	} // fn GetPath

	
	function GetFullPath($p_path, $p_filename)
	{
		global $Campsite;
		$fileFullPath = $Campsite['TEMPLATE_DIRECTORY'].$p_path."/".$p_filename;
		return $fileFullPath;
	}
	
	
	/** 
	 * Sync the database with the filesystem.
	 * @return void
	 */
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
	} // fn UpdateStatus
	
	
	function OnUpload($p_fileNameStr, $p_charset, $p_baseUpload, $p_desiredName = null)
	{
		global $Campsite;
		$p_baseUpload = $Campsite['TEMPLATE_DIRECTORY'].$p_baseUpload;
		$fileName = $GLOBALS["$p_fileNameStr"];
	
		if (trim($fileName) == "") {
			return false;
		}
	
		$fninForm = $GLOBALS["$p_fileNameStr"."_name"];
	
		$dotpos = strrpos($fninForm, ".");
		$name = substr ($fninForm, 0, $dotpos);
		$ext = substr ($fninForm, $dotpos + 1);
	
		if ($p_desiredName != "") {
			$fninForm = "$p_desiredName.$ext";
		}
	
		// strip out the &, because when transmitting filename list over the todolist,
		// the & sign will be interpreted as separator, and this will destroy the
		// consistency of the todolist
		$fninForm = str_replace('&', '', $fninForm);
		$newname = "$p_baseUpload/".$fninForm;
		if(file_exists($newname) && !is_dir($newname)) {
			unlink($newname);
		}
	
		$origFile = $newname.".orig";
		@$renok = move_uploaded_file($fileName, $origFile);
		if ($renok == false){
			return false;
		}
	
		$fType = $GLOBALS["$p_fileNameStr"."_type"];
		if (strncmp($fType, "text", 4) == 0)
		{
			$command = "iconv -f $p_charset -t UTF-8 \"$origFile\" > \"$newname\"";
			$res_out = system($command, $status);
			unlink($origFile);
			if ($status != 0) {
				unlink($newname);
				return false;
			}
		} else {
			rename($origFile, $newname);
		}
	
		return true;
	} // fn OnUpload
	
	
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
		$templates = DbObjectArray::Create('Template', $queryStr);
		return $templates;
	} // fn GetAllTemplates
	

} // class Template
?>