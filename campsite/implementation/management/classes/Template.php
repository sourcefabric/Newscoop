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
	 * A template is an HTML file with Campsite parser tags inside.
	 *
	 * @param mixed p_templateIdOrName
	 * 		Give the template ID or the template name relative
	 * 		to the template base directory.
	 */
	function Template($p_templateIdOrName = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		if (is_numeric($p_templateIdOrName)) {
			$this->m_data['Id'] = $p_templateIdOrName;
		} elseif (is_string($p_templateIdOrName)) {
			$this->m_data['Name'] = $p_templateIdOrName;
			$this->m_keyColumnNames = array('Name');
		}
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Return TRUE if the file exists.
	 *
	 * @return boolean
	 */
	function fileExists()
	{
		global $Campsite;

		if (!Template::IsValidPath(dirname($this->m_data['Name']))) {
			return false;
		}
		if (!is_file($Campsite['TEMPLATE_DIRECTORY']."/".$this->m_data['Name'])) {
			return false;
		}
		return true;
	} // fn fileExists


	/**
	 * @return int
	 */
	function getTemplateId()
	{
		return $this->m_data['Id'];
	} // fn getTemplateId


	/**
	 * @return string
	 */
	function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * @return int
	 */
	function getType()
	{
		return $this->m_data['Type'];
	} // fn  getType


	/**
	 * @return int
	 */
	function getLevel()
	{
		return $this->m_data['Level'];
	} // fn getLevel


	/**
	 * @return string
	 */
	function getAbsoluteUrl()
	{
		global $Campsite;
		return $Campsite['TEMPLATE_BASE_URL'].$this->m_data['Name'];
	} // fn getAbsoluteUrl


	/**
	 * @param string $p_path
	 * @return string
	 */
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
	 * Returns true if the template path is valid.
	 *
	 * @param string $p_path
	 * @return bool
	 */
	function IsValidPath($p_path)
	{
		global $Campsite;

		foreach (split("/", $p_path) as $index=>$dir) {
			if ($dir == "..") {
				return false;
			}
		}

		if (!is_dir($Campsite['TEMPLATE_DIRECTORY'] ."/$p_path")) {
			return false;
		}
		return true;
	} // fn IsValidPath


	/**
	 * Returns TRUE if the template is being used in an Issue or Section.
	 * @return boolean
	 */
	function InUse($p_templateName)
	{
		global $Campsite;
		global $g_ado_db;

		$p_templateName = ltrim($p_templateName, '/');
		$queryStr = "SELECT * FROM Templates WHERE Name = '$p_templateName'";
		$row = $g_ado_db->GetRow($queryStr);
		if (!$row) {
			return false;
		}
		$id = $row['Id'];

		$queryStr = "SELECT COUNT(*) FROM Issues "
					." WHERE IssueTplId = " . $id
		     		." OR SectionTplId = " . $id
		     		." OR ArticleTplId = " . $id;
		$numMatches = $g_ado_db->GetOne($queryStr);
		if ($numMatches > 0) {
			return true;
		}

		$queryStr = "SELECT COUNT(*) FROM Sections "
					." WHERE SectionTplId = " . $id
		     		." OR ArticleTplId = " . $id;
		$numMatches = $g_ado_db->GetOne($queryStr);
		if ($numMatches > 0) {
			return true;
		}

		$tplFindObj = new FileTextSearch();
		$tplFindObj->setExtensions(array('tpl'));
		$tplFindObj->setSearchKey(' '.$p_templateName);
		$tplFindObj->findReplace($Campsite['TEMPLATE_DIRECTORY']);
		if ($tplFindObj->m_totalFound > 0) {
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


	/**
	 * Return the full path to the file.
	 *
	 * @param string $p_path
	 * 		Path of the file starting from the base template directory.
	 * @param string $p_filename
	 * @return string
	 */
	function GetFullPath($p_path, $p_filename)
	{
		global $Campsite;
		$fileFullPath = $Campsite['TEMPLATE_DIRECTORY'].$p_path."/".$p_filename;
		return $fileFullPath;
	} // fn GetFullPath


	/**
	 * Sync the database with the filesystem.
	 * @return void
	 */
	function UpdateStatus()
	{
		global $Campsite;
		global $g_ado_db;

		// check if each template record in the database has the corresponding file
		$templates = Template::GetAllTemplates(null, false);
		foreach ($templates as $index=>$template) {
			if (!$template->fileExists()) {
				$g_ado_db->Execute("DELETE FROM Templates WHERE Id = "
					. $template->m_data['Id']);
			}
		}

		// insert new templates
		$rootDir = $Campsite['TEMPLATE_DIRECTORY'];
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
				$existingTemplates = $g_ado_db->GetOne($sql);
				if ($existingTemplates == 0) {
					$sql = "INSERT IGNORE INTO Templates (Name, Level) VALUES('$relPath', $level)";
					$g_ado_db->Execute($sql);
				}
			}
		}
	} // fn UpdateStatus


	/**
	 * Call this to upload a template file.  Note: Template::UpdateStatus()
	 * will be called automatically for you if this is successful.
	 *
	 * @param string $p_fileVarName
	 * 		Name of the variable in the $_FILES global variable.
	 * @param string $p_charset
	 * 		Desired character set of the file.
	 * @param string $p_baseUpload
	 * 		Directory path to add to the base template directory.
	 * @param string $p_desiredName
	 * 		Desired name of the file.
	 *
	 * @return mixed
	 * 		TRUE on success, PEAR_Error on failure.
	 */
	function OnUpload($f_fileVarName, $p_baseUpload, $p_desiredName = null, $p_charset = null)
	{
		global $Campsite;
		$p_baseUpload = $Campsite['TEMPLATE_DIRECTORY'].$p_baseUpload;

		if (!isset($_FILES[$f_fileVarName]) || !isset($_FILES[$f_fileVarName]['name'])) {
			return new PEAR_Error("Invalid parameters given to Template::OnUpload()");
		}

		if (is_null($p_desiredName)) {
			$fileName = $_FILES[$f_fileVarName]['name'];
		} else {
			$fileName = $p_desiredName;
		}

		// remove existing file if one exists
		$newname = "$p_baseUpload/$fileName";
		if (file_exists($newname) && !is_dir($newname)) {
			if (!unlink($newname)) {
				return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $newname), CAMP_ERROR_DELETE_FILE);
			}
		}

		$fType = $_FILES[$f_fileVarName]['type'];
		if (!is_null($p_charset) && (strncmp($fType, 'text', 4) == 0)) {
			$origFile = "$newname.orig";
			$success = move_uploaded_file($_FILES[$f_fileVarName]['tmp_name'], $origFile);
			if ($success) {
				$command = "iconv -f $p_charset -t UTF-8 \"$origFile\" > \"$newname\"";
				system($command, $status);
				if ($status == 0) {
					$success = unlink($origFile);
				} else {
					$success = false;
					unlink($newname);
					return new PEAR_Error("Unable to convert the character set of the file.");
				}
			} else {
				return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $origFile), CAMP_ERROR_CREATE_FILE);
			}
		} else {
			$success = move_uploaded_file($_FILES[$f_fileVarName]['tmp_name'], $newname);
			if (!$success) {
				return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $newname), CAMP_ERROR_CREATE_FILE);
			}
		}

		if ($success) {
			Template::UpdateStatus();
			if (function_exists('camp_load_translation_strings')) {
				camp_load_translation_strings('api');
			}
			$logtext = getGS('Template $1 uploaded', $fileName);
			Log::Message($logtext, null, 111);
		}
		return $success;
	} // fn OnUpload


	/**
	 * @param array $p_sqlOptions
	 * @param boolean $p_update
	 */
	function GetAllTemplates($p_sqlOptions = null, $p_update = true)
	{
		if ($p_update) {
			Template::UpdateStatus();
		}
		$queryStr = 'SELECT * FROM Templates';
		if (!is_null($p_sqlOptions)) {
			$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		} else {
			$queryStr .= ' ORDER BY Level ASC, Name ASC';
		}
		$templates = DbObjectArray::Create('Template', $queryStr);
		return $templates;
	} // fn GetAllTemplates


} // class Template
?>
