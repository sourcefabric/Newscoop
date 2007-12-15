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
	public function Template($p_templateIdOrName = null)
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
	public function fileExists()
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
	public function getTemplateId()
	{
		return $this->m_data['Id'];
	} // fn getTemplateId


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->m_data['Type'];
	} // fn  getType


	/**
	 * @return int
	 */
	public function getLevel()
	{
		return $this->m_data['Level'];
	} // fn getLevel


	/**
	 * @return string
	 */
	public function getAbsoluteUrl()
	{
		global $Campsite;
		return $Campsite['TEMPLATE_BASE_URL'].$this->m_data['Name'];
	} // fn getAbsoluteUrl


	/**
	 * @param string $p_path
	 * @return string
	 */
	public static function GetContents($p_path)
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
	public static function IsValidPath($p_path)
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
	public static function InUse($p_templateName)
	{
		global $Campsite;
		global $g_ado_db;

		$p_templateName = ltrim($p_templateName, '/');
		// var_dump(pathinfo($p_templateName, PATHINFO_EXTENSION)); exit;
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

		if (pathinfo($p_templateName, PATHINFO_EXTENSION) == 'tpl') {
			$p_templateName = ' ' . $p_templateName;
		}
		$tplFindObj = new FileTextSearch();
		$tplFindObj->setExtensions(array('tpl','css'));
		$tplFindObj->setSearchKey($p_templateName);
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
	public static function GetPath($p_path, $p_filename)
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
	public static function GetFullPath($p_path, $p_filename)
	{
		global $Campsite;
		$fileFullPath = $Campsite['TEMPLATE_DIRECTORY'].$p_path."/".$p_filename;
		return $fileFullPath;
	} // fn GetFullPath


	/**
	 * Update the Name field for a template on Renaming/Moving.
	 *
	 * It is called before any UpdateStatus() when renaming/moving
	 * a template to avoid a new Id is set for the changed template.
	 *
	 * @param string $p_tplOrig
	 *		The original template Name
	 * @param string $p_tplNew
	 *		The new template Name
	 * @return mixed
	 */
	public static function UpdateOnChange($p_tplOrig, $p_tplNew)
	{
		global $Campsite;
		global $g_ado_db;

		// Remove beginning slashes
		$p_tplOrig = ltrim($p_tplOrig, '/');
		$p_tplNew = ltrim($p_tplNew, '/');

		if (is_file($Campsite['TEMPLATE_DIRECTORY']."/".$p_tplNew)) {
			$sql = "SELECT * FROM Templates WHERE Name = '$p_tplOrig'";
			$row = $g_ado_db->GetRow($sql);
			if (!$row) {
				return false;
			}
			$id = $row['Id'];
			$sql = "UPDATE Templates SET Name = '$p_tplNew' WHERE Id = '$id'";
			$g_ado_db->Execute($sql);
		}
	} // fn UpdateOnChange


	/**
	 * Sync the database with the filesystem.
	 * @return void
	 */
	public static function UpdateStatus()
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

				$relPath = substr($fullPath, strlen($rootDir) + 1);
				$level = $relPath == "" ? 0 : substr_count($relPath, "/");
				$sql = "SELECT count(*) AS nr FROM Templates WHERE Name = '" . $relPath . "'";
				$existingTemplates = $g_ado_db->GetOne($sql);
				if ($existingTemplates == 0) {
					$ending = substr($file, strlen($file) - 4);
					if ($ending != ".tpl") { // ignore files that are not templates (end in .tpl)
						$sql = "SELECT Id FROM TemplateTypes WHERE Name = 'nontpl'";
						$nonTplTypeId = $g_ado_db->GetOne($sql);
						$sql = "INSERT IGNORE INTO Templates (Name, Type, Level) "
									. "VALUES('$relPath', "
										. "$nonTplTypeId, "
										. "$level)";
					} else {
						$sql = "INSERT IGNORE INTO Templates (Name, Level) "
									. "VALUES('$relPath', "
										. "$level)";
					}
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
	public static function OnUpload($f_fileVarName, $p_baseUpload,
	                                $p_desiredName = null, $p_charset = null)
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
	public static function GetAllTemplates($p_sqlOptions = null, $p_update = true)
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


	/**
	 * Returns an array containing the directories tree for the given path.
	 *
	 * @param array $p_folders
	 * @return array $p_folders
	 */
	public static function GetAllFolders($p_folders)
	{
		global $Campsite;

		$path = $Campsite['TEMPLATE_DIRECTORY'] . '/';
		if ($dirHandle = opendir($path)) {
			$i = 0;
			while (($file = readdir($dirHandle)) !== false) {
				if ($file != '.' && $file != '..' && is_dir($path . $file)) {
					$i++;
					$p_folders[$i] = $path . $file;
				}
			}
		}
		closedir($dirHandle);

		$i = count($p_folders);
		foreach ($p_folders as $folder) {
			if ($subDirHandle = opendir($folder)) {
				while (($file = readdir($subDirHandle)) !== false) {
					$pathTo = $folder. '/' . $file;
					if ($file != '.' && $file != '..' &&
							is_dir($pathTo) &&
							!in_array($pathTo, $p_folders)) {
						$i++;
						$p_folders[$i] = $pathTo;
						$p_folders = Template::GetAllFolders($p_folders);
					}
				}
			}
			closedir($subDirHandle);
		}
		sort($p_folders);
		return $p_folders;
	} // fn GetAllFolders


	/**
	 * Deletes a template file.
	 * It does not take care on database info upgrade because
	 * it trusts of the cool Template::UpdateStatus() function.
	 *
	 * @return mixed
	 */
	public function delete() {
		global $g_user;

		$deleted = false;
		$rootDir = '/';
		if ($this->fileExists()) {
			$Path = dirname($rootDir . $this->getName());
			$Name = basename($this->getName());
			$fileFullPath = $this->getFullPath($Path, $Name);
			if (!Template::InUse($this->getName())) {
				$deleted = unlink($fileFullPath);
				if($deleted) {
					$logtext = getGS('Template $1 was deleted', mysql_real_escape_string($this->getName()));
					Log::Message($logtext, $g_user->getUserId(), 112);
				}
			}
		}
		return $deleted;
	} // fn delete


	/**
	 * Moves a template from current folder to destination folder.
	 * It does not take care on database info upgrade because
	 * it trusts of the cool Template::UpdateStatus() function.
	 *
	 * @param string $p_current_folder
	 * @param string $p_destination_folder
	 * @return bool true on succes or false on failure
	 */
	public function move($p_current_folder, $p_destination_folder) {
		global $Campsite;
		global $g_user;

		if (Template::IsValidPath($p_current_folder) &&
			Template::IsValidPath($p_destination_folder)) {
			$currentFolder = ($p_current_folder == '/') ? '' : $p_current_folder;
			$destinationFolder = ($p_destination_folder == '/') ? '' : $p_destination_folder;
			$currentFullPath = $Campsite['TEMPLATE_DIRECTORY']
						. $currentFolder
						. '/' . basename($this->getName());
			$destinationFullPath = $Campsite['TEMPLATE_DIRECTORY']
						. $destinationFolder
						. '/' . basename($this->getName());
			if (rename($currentFullPath, $destinationFullPath)) {
				$logtext = getGS('Template $1 was moved to $2',
						 mysql_real_escape_string($currentFolder . '/' . basename($this->getName())),
						 mysql_real_escape_string($destinationFolder . '/' . basename($this->getName())));
				Log::Message($logtext, $g_user->getUserId(), 117);
				return true;
			}
		}
		return false;
	} // fn move

} // class Template

?>
