<?php
require_once('DatabaseObject.php');
require_once('DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');

/**
 * @Annotation()
 */
class Template extends DatabaseObject {
    var $m_dbTableName = 'Templates';
    var $m_keyColumnNames = array('Id');
    var $m_keyIsAutoIncrement = true;
    var $m_columnNames = array('Id', 'Name', 'Type', 'Level', 'CacheLifetime');

    /**
     * A template is an HTML file with Campsite parser tags inside.
     *
     * @param mixed p_templateIdOrName
     * 		Give the template ID or the template name relative
     * 		to the template base directory.
     * @param string $p_templateIdOrName
     *
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
     * Wrapper around DatabaseObject::setProperty
     * @see classes/DatabaseObject#setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql)
     * @param string $p_dbColumnName
     * @param integer $p_value
     */
    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        if ($p_dbColumnName == 'Name') {
            $this->m_keyColumnNames = array('Name');
            $this->resetCache();
            $this->m_keyColumnNames = array('Id');
        }
        return parent::setProperty($p_dbColumnName, $p_value);
    }


    /**
     * Return TRUE if the file exists.
     *
     * @return boolean
     */
    public function fileExists()
    {
        global $Campsite;

        if (!self::IsValidPath(dirname($this->m_data['Name']))) {
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
     * @return int
     */
    public function getCacheLifetime()
    {
        return $this->m_data['CacheLifetime'];
    } // fn getCacheLifetime

    /**
     * @return boolean
     */
    public function setCacheLifetime($p_lifetime = 0)
    {
        return $this->setProperty('CacheLifetime', $p_lifetime, true);
    } // fn setCacheLifetime

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
     * @param boolenan $p_checkIfExists
     * @return bool
     */
    public static function IsValidPath($p_path, $p_checkIfExists=true)
    {
        global $Campsite;

        foreach (explode("/", $p_path) as $index=>$dir) {
            if ($dir == "..") {
                return false;
            }
        }

        if ($p_checkIfExists &&
              !is_dir($Campsite['TEMPLATE_DIRECTORY'] ."/$p_path") &&
              !is_file($Campsite['TEMPLATE_DIRECTORY'] ."/$p_path") &&
              !is_link($Campsite['TEMPLATE_DIRECTORY'] ."/$p_path")) {
            return false;
        }
        return true;
    } // fn IsValidPath


    /**
     * Returns TRUE if the template is being used in an Issue or Section.
     * @param string $p_templateName
     * @return boolean
     */
    public static function InUse($p_templateName)
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
        $tplFindObj->setExtensions(array('tpl','css'));
        $tplFindObj->setSearchKey($p_templateName);
        $result = $tplFindObj->findReplace($Campsite['TEMPLATE_DIRECTORY']);
        if (is_array($result) && sizeof($result) > 0) {
                return $result[0];
        }

        if (pathinfo($p_templateName, PATHINFO_EXTENSION) == 'tpl') {
            $p_templateName = ' ' . $p_templateName;
        }
        $tplFindObj->setSearchKey($p_templateName);
        $result = $tplFindObj->findReplace($Campsite['TEMPLATE_DIRECTORY']);
        if (is_array($result) && sizeof($result) > 0) {
                return $result[0];
        }
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
     * @return false|null
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
        $templates = self::GetAllTemplates(null, false);
        foreach ($templates as $index=>$template) {
            if (!$template->fileExists()) {
                $g_ado_db->Execute("DELETE FROM Templates WHERE Id = "
                    . $template->m_data['Id']);
            }
        }

        // insert new templates
        // TODO there's some bug here maybe, you get a notice on this line cause that key is undefined
        $rootDir = @$Campsite['TEMPLATE_DIRECTORY'];
        $dirs[] = $rootDir;
        while (($currDir = array_pop($dirs)) != "") {
            if (!is_readable($currDir)) {
                continue;
            }
            if (!$dirHandle = opendir($currDir)) {
                continue;
            }

            while ($file = readdir($dirHandle)) {
                $fullPath = $currDir . '/' . $file;
                    if (!is_readable($fullPath)) {
                        continue;
                }
                if ($file == "." || $file == "..") {
                    continue;
                }

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
     * @param string $f_fileVarName
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
        $newname = $p_baseUpload . DIR_SEP . $fileName;
        if (file_exists($newname) && !is_dir($newname)) {
            if (!unlink($newname)) {
                return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $newname), CAMP_ERROR_DELETE_FILE);
            }
        }

        $fType = $_FILES[$f_fileVarName]['type'];
        if (!is_null($p_charset)) {
            // original file, destination file, file type, file charset
            $success = self::ChangeCharset($_FILES[$f_fileVarName]['tmp_name'], $newname, $fType, $p_charset);
        } else {
            $success = move_uploaded_file($_FILES[$f_fileVarName]['tmp_name'], $newname);
            if (!$success) {
                return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $newname), CAMP_ERROR_CREATE_FILE);
            }
        }

        if ($success) {
            self::UpdateStatus();
        }
        return $success;
    } // fn OnUpload


    /**
     * @param p_tmpFile
     * @param p_newFile
     * @param p_charset
     *
     * @return
     */
    public static function ProcessFile($p_tmpFile, $p_newFile, $p_charset = null)
    {
        // remove existing file if one exists
        if (file_exists($p_newFile) && !is_dir($p_newFile)) {
            if (!unlink($p_newFile)) {
                return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $p_newFile), CAMP_ERROR_DELETE_FILE);
            }
        }

        $fileName = basename($p_newFile);
        $fileType = @mime_content_type($p_tmpFile);
        if (!empty($p_charset)) {
            if (strncmp($fileType, 'text', 4) == 0) {
                $success = self::ChangeCharset($p_tmpFile, $p_newFile, $fileType, $p_charset);
            } else {
                $success = rename($p_tmpFile, $p_newFile);
            }
            if (PEAR::isError($success) || !$success) {
                return $success;
            }
        } else {
            $success = rename($p_tmpFile, $p_newFile);
            if (!$success) {
                return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $fileName), CAMP_ERROR_CREATE_FILE);
            }
        }

        self::UpdateStatus();

        return true;
    } // fn ProcessFile


    /**
     * @param p_oFile
     * @param p_dFile
     * @param p_type
     * @param p_charset
     *
     * @return
     */
    public static function ChangeCharset($p_oFile, $p_dFile, $p_type, $p_charset)
    {
        if (strncmp($p_type, 'text', 4) == 0) {
            $origFile = $p_dFile . '.orig';
            $success = rename($p_oFile, $origFile);
            if ($success) {
                $command = 'iconv -f ' . escapeshellarg($p_charset) . ' -t UTF-8 '
                    . escapeshellarg($origFile) . ' > ' . escapeshellarg($p_dFile);
                system($command, $status);
                if ($status == 0) {
                    $success = unlink($origFile);
                } else {
                    $success = false;
                    unlink($p_dFile);
                    return new PEAR_Error("Unable to convert the character set of the file.");
                }
            } else {
                return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $origFile), CAMP_ERROR_CREATE_FILE);
            }
        }

        return $success;
    } // fn ChangeCharset


    /**
     * @param array $p_sqlOptions
     * @param boolean $p_update
     * @param boolean $p_useFilter
     *		filter templates matching setting in SystemPreferences
     * @param boolean $p_strict
     *      if true, retrieves only template (tpl) files
     *
     * @return array
     */
    public static function GetAllTemplates($p_sqlOptions = null, $p_update = true, $p_useFilter = false,
                                           $p_strict = false)
    {
        if ($p_update) {
            self::UpdateStatus();
        }
        $queryStr = 'SELECT * FROM Templates';

        if ($p_strict) {
            $queryStr .= ' WHERE Type < 5';
        }
        if ($p_useFilter && $rexeg = self::GetTemplateFilterRegex(true)) {
            $queryStr .= ($p_strict == false) ? ' WHERE ' : ' AND ';
            $queryStr .= 'Name NOT REGEXP "'.self::GetTemplateFilterRegex(true).'"';
        }

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
                        $p_folders = self::GetAllFolders($p_folders);
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
     * @return boolean
     */
    public function delete() {
        global $g_user;

        $rootDir = '/';
        if ($this->exists()) {
            $Path = dirname($rootDir . $this->getName());
            $Name = basename($this->getName());
            $fileFullPath = $this->getFullPath($Path, $Name);
            if (!self::InUse($this->getName())) {
                if (unlink($fileFullPath)) {
                    return true;
                }
            }
        }
        return false;
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

        if (self::IsValidPath($p_current_folder) &&
            self::IsValidPath($p_destination_folder)) {
            $currentFolder = ($p_current_folder == '/') ? '' : $p_current_folder;
            $destinationFolder = ($p_destination_folder == '/') ? '' : $p_destination_folder;
            $currentFullPath = $Campsite['TEMPLATE_DIRECTORY']
                        . $currentFolder
                        . '/' . basename($this->getName());
            $destinationFullPath = $Campsite['TEMPLATE_DIRECTORY']
                        . $destinationFolder
                        . '/' . basename($this->getName());
            if (rename($currentFullPath, $destinationFullPath)) {
                return true;
            }
        }
        return false;
    } // fn move


    /**
     * Return an regular expression to filter template lists
     *
     * @param boolean $p_sql indicates usage for sql query
     * @return string
     */
    static public function GetTemplateFilterRegex($p_sql = false) {
        $filters = array();
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        if ($filterStr = $preferencesService->TemplateFilter) {
          foreach (explode(',', $filterStr) as $filter) {




               if ($p_sql) {
                  $filter = preg_quote(trim($filter), '/');       // quote the filter
                  $filter = str_replace('\\', '\\\\', $filter);
                  $filter = str_replace('\\*', '.*', $filter);    // * becomes .*
                  $filter = str_replace('\\?', '.', $filter);     //  becomes .
                  $filter1 = "(^$filter$)";
                  $filters[] = $filter1;
                  $filter2 = "(/$filter$)";
                  $filters[] = $filter2;
                  $filter1 = "(^$filter/)";
                  $filters[] = $filter1;
                  $filter2 = "(/$filter/)";
                  $filters[] = $filter2;
              } else {
                  $filter = preg_quote(trim($filter), '/');   // quote the filter
                  $filter = str_replace('\\*', '.*', $filter); // * becomes .*
                  $filter = str_replace('\\?', '.', $filter);     //  becomes .
                  $filter = "(^$filter$)";
                  $filters[] = $filter;
              }
          }
        }
        $filterRegex = implode('|', $filters);

        return $filterRegex;
    }

} // class Template

?>
