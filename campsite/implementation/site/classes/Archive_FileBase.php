<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/XR_CcClient.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_File.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_FileXMLMetadata.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_FileDatabaseMetadata.php');
require_once('HTTP/Client.php');


/**
 * @package Campsite
 */
class Archive_FileBase
{
    /**
     *
     */
    protected $m_gunId = null;

    /**
     *
     */
    protected $m_fileType = null;

    /**
     *
     */
    protected $m_mask = array();

    /**
     *
     */
    protected $m_metaData = array();

    /**
     *
     */
    protected $m_fileTypes = array();

    /**
     *
     */
    protected $m_exists = false;

    /**
     * If true it will use the caching feature
     *
     * @var bool
     */
    private static $m_useCache = null;
    
    /**
     * The default engine to use
     * @var string
     */
    private static $m_cacheEngine = null;
    

    /**
     * Constructor
     *
     * @param string $p_gunId
     *      The file gunid
     */
    public function __construct($p_gunId = null)
    {
        if (!empty($p_gunId)) {
        	$this->fetch($p_gunId);
        }
    }


    /**
     * @param string $p_gunId
     *      The file gunid
     * @return boolen
     *      TRUE on success, FALSE on failure
     */
    public function fetch($p_gunId)
    {
        if (empty($p_gunId)) {
            $this->m_gunId = null;
            $this->m_fileType = null;
            $this->m_mask = array();
            $this->m_metaData = array();
            $this->m_fileTypes = array();
            $this->m_exists = false;
            return false;
        }

    	$fileDbMetadataObj = new Archive_FileDatabaseMetadata($p_gunId);
    	$this->m_metaData = $fileDbMetadataObj->fetch();
    	if ($this->m_metaData == false || sizeof($this->m_metaData) == 0) {
    		$fileXMLMetadataObj = new Archive_FileXMLMetadata($p_gunId);
    		$this->m_metaData = $fileXMLMetadataObj->m_metaData;
    		if ($fileXMLMetadataObj->exists()) {
    			$this->m_gunId = $p_gunId;
    			$this->m_exists = true;
    			$fileDbMetadataObj->create($this->m_metaData);
    		}
    	} else {
    		$this->m_gunId = $p_gunId;
    		$this->m_exists = true;
    	}
    	return $this->m_exists;
    } // fn fetch


    /**
     * Returns true if the current object is the same type as the given
     * object then has the same value.
     * @param mix $p_otherObject
     * @return boolean
     */
    public function sameAs($p_otherObject)
    {
        if (get_class($this) != get_class($p_otherObject)
                || $this->m_dbTableName != $p_otherObject->m_dbTableName) {
            return false;
        }
        if (!$this->m_exists && !$p_otherObject->m_exists) {
            return true;
        }
        foreach ($this->m_keyColumnNames as $keyColumnName) {
            if ($this->m_data[$keyColumnName] != $p_otherObject->m_data[$keyColumnName]) {
                return false;
            }
        }
        return true;
    }


    /**
     * Copies the given object
     *
     * @param object $p_source
     * @return object
     */
    public function duplicateObject($p_source)
    {
        foreach ($p_source as $key=>$value) {
            $this->$key = $value;
        }

        return $this;
    }


    /**
     * Returns whether the file exists
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function exists()
    {
        return $this->m_exists;
    } // fn exists


    /**
     * Return true if the object has all the values required
     * to fetch a unique object from the file archive.
     *
     * @return boolean
     */
    public function keyValuesExist()
    {
    	return !empty($this->m_gunId);
    }
    
    
    /**
     * Delete the file from the multimedia archive.
     * Returns true if the object was found and deleted, false otherwise.
     * 
     * @return boolean
     */
    public function delete()
    {
    	$fileXMLMetadataObj = new Archive_FileXMLMetadata($this->m_gunId, $this->m_fileType);
    	if ($fileXMLMetadataObj->delete()) {
    		$fileDbMetadataObj = new Archive_FileDatabaseMetadata($this->m_gunId);
    		$fileDbMetadataObj->delete();
    		return true;
    	}
    	return false;
    }


    /**
     * Returns the unique identifier value for the file
     *
     * @return string
     *      The file global unique identifier
     */
    public function getGunId()
    {
    	return $this->m_gunId;
    } // fn getGunId


    /**
     * @return string
     *      The file group format (image, audio, video, application, etc.)
     */
    public function getType()
    {
        $format = explode('/', $this->getMetatagValue('format'));
        return $format[0];
    }


    /**
     * @return string
     *      The file mime type
     */
    public function getMimeType()
    {
        return $this->getMetatagValue('format');
    }


    /**
     * Get modified time in a more human-readable form.
     *
     * @return string
     *      Formatted date/time
    */
    public function getModifiedTime()
    {
        if (!$this->getMetatagValue('mtime')) {
            return false;
        }

        $mtimeStr = '';
        list($date, $timeNzone) = explode('T', $this->getMetatagValue('mtime'));
        $time = substr($timeNzone, 0, 8);
        $zone = substr($timeNzone, 8);
        if ($date == date('Y-m-d')) {
            $mtimeStr = getGS('Today') . ', ' . $time;
        } else {
            $mtimeStr = $date . ' ' . $time;
        }
        return $mtimeStr;
    } // fn getModifiedTime


    /**
     * @return
     */
    public function getMetatagLabel($p_tagName)
    {
        return $this->m_metatagLabels[$p_tagName];
    }


    /**
     * Returns the value of the given meta tag
     *
     * @param string $p_tagName
     *      The name of the meta tag
     *
     * @return string
     *      The meta tag value
     */
    public function getProperty($p_property)
    {
    	return $this->getMetatagValue($p_property);
    }


    /**
     * Returns the value of the given meta tag
     *
     * @param string $p_tagName
     *      The name of the meta tag
     *
     * @return string
     *      The meta tag value
     */
    public function getMetatagValue($p_tagName)
    {
    	$namespaces = array('dc', 'ls', 'dcterms');

    	$p_tagName = trim(strtolower($p_tagName));
    	if (is_null($this->m_gunId) || sizeof($this->m_metaData) == 0) {
    		return null;
    	}
    	$splitPos = strpos($p_tagName, ':');
    	if ($splitPos !== false) {
    		$tagNs = substr($p_tagName, 0, $splitPos);
    		if (array_search($tagNs, $namespaces) === false) {
	    		return PEAR_Error::PEAR_Error("Invalid metatag namespace.");
    		}
    		if (!array_key_exists($p_tagName, $this->m_metaData)) {
	    		return null;
    		}
    		return $this->m_metaData[$p_tagName]->getValue();
    	}
    	foreach ($namespaces as $namespace) {
    		$tag = $namespace . ':' . $p_tagName;
    		if (array_key_exists($tag, $this->m_metaData)) {
    			return $this->m_metaData[$tag]->getValue();
    		}
    	}
    	return null;
    } // fn getMetaTagValue


    /**
     * Returns an array containing available meta tags
     *
     * @return array
     *      The available meta tags for the file
     */
    public function getAvailableMetaTags()
    {
    	if (is_null($this->m_gunId) || sizeof($this->m_metaData) == 0) {
    		return null;
    	}
    	return array_keys($this->m_metaData);
    } // fn getAvailableMetaTags


    /**
     * @param string
     *
     * @return array
     */
    public function getFileTypeInfo($p_fileName)
    {
        if (!$this->isValidFileType($p_fileName)) {
            return false;
        }
        $fileExtension = self::GetFileExtension($p_fileName);
        return $this->m_fileTypes[$fileExtension];
    } // fn getFileTypeInfo


    /**
     * Deletes all the metadata for the file.
     * It checks whether the file is attached to multiple articles
     * before deletes metadata.
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function deleteMetadata()
    {
        if (is_null($this->m_gunId)) {
            return false;
        }
        $fileDbMetadataObj = new Archive_FileDatabaseMetadata($this->m_gunId);
        if ($fileDbMetadataObj->inUse() == false) {
            return $fileDbMetadataObj->delete();
        }
        return true;
    } // fn deleteMetadata


    /**
     * Changes file metadata on both storage and local servers.
     *
     * @param array $p_formData
     *      The form data submitted with all the file metadata
     *
     * @return string|PEAR_Error
     *      Modified time on success, PEAR Error on failure
     */
    public function editMetadata($p_formData)
    {
        if (!is_array($p_formData)) {
            return new PEAR_Error(getGS('Invalid parameter given to Archive_FileBase::editMetadata()'));
        }

        $metaData = array();
        foreach ($this->m_mask['pages'] as $key => $val) {
        	foreach ($this->m_mask['pages'][$key] as $k => $v) {
        		$element_encode = str_replace(':','_',$v['element']);
        		if (isset($p_formData['f_'.$key.'_'.$element_encode])
                        && !empty($p_formData['f_'.$key.'_'.$element_encode])) {
        			list($predicate_ns, $predicate) = explode(':', $v['element']);
        			$recordSet['gunid'] = $this->m_gunId;
        			$recordSet['predicate_ns'] = $predicate_ns;
        			$recordSet['predicate'] = $predicate;
        			if ($predicate == 'mtime') {
        				$mtime = date('c');
        				$recordSet['object'] = $mtime;
        			} else {
        				$recordSet['object'] = $p_formData['f_'.$key.'_'.$element_encode];
        			}
        			$tmpMetadataObj = new Archive_FileMetadataEntry($recordSet);
        			$metaData[strtolower($v['element'])] = $tmpMetadataObj;
        		}
        	}
        }

        if (sizeof($metaData) == 0) return false;

        $fileXMLMetadataObj = new Archive_FileXMLMetadata($this->m_gunId,
                                                          $this->m_fileType);
        if ($fileXMLMetadataObj->update($metaData) == false) {
            return new PEAR_Error(getGS('Cannot update file metadata on storage server'));
        }
        $fileDbMetadataObj = new Archive_FileDatabaseMetadata($this->m_gunId);
        if ($fileDbMetadataObj->update($metaData) == false) {
            return new PEAR_Error(getGS('Cannot update file metadata on Campsite'));
        }
        // Update file metadata for the current object instance
        $this->m_metaData = $metaData;
        // Logging
        $logtext = getGS('The file "$1" has been modified (gunid = $2)',
            $metaData['dc:title']->getValue(), $this->m_gunId);
        Log::Message($logtext, null, 183);

        return $mtime;
    } // fn editMetadata


    /**
     * Updates metadata on the archive server
     *
     * @param array $p_metaData
     *      An array of Archive_FileMetadataEntry objects
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function update($p_metadata, $p_commit = true)
    {
    	if (!is_array($p_metadata)) {
    		return false;
    	}
    	foreach ($p_metadata as $element=>$value) {
    		if (count(explode(':', $v['element'])) < 2) {
    			return false;
    		}
    		if (!is_object($value)) {
    			return false;
    		}
    	}
    	$this->m_metaData = $p_metadata;
    	if ($p_commit) {
    		return $this->commit();
    	}
    	return true;
    }


    /**
     * Commit the data stored in memory to the archive.
     * This is useful if you make a bunch of setProperty() calls at once
     * and you dont want to update the database every time.  Instead you
     * can set all the variables without committing them, then call this function.
     *
     * @param array $p_ignoreColumns
     *      Specify column names to ignore when doing the commit.
     *
     * @return boolean
     *      Return TRUE if the database was updated, false otherwise.
     */
    public function commit()
    {
    	if (!$this->exists()) {
    		return false;
    	}
        $fileXMLMetadata = new Archive_FileXMLMetadata($this->m_gunId, $this->m_fileType);
        $fileDbMetadata = new Archive_FileDatabaseMetadata($this->m_gunId);
        if ($fileXMLMetadata->update($this->m_metaData)) {
        	$fileDbMetadata->update($this->m_metaData);
        	return true;
        }
        return false;
    }


    /**
     * This function should be called when a file is uploaded.
     * It will save the file to the temporary directory on
     * the disk before to be sent to the storage server.
     *
     * @param array $p_fileVar
     *      The file submited
     *
     * @return string|PEAR_Error
     *      The full pathname to the file or Error
     */
    public function onFileUpload($p_fileVar)
    {
        global $Campsite;

        if (!is_array($p_fileVar)) {
            return false;
        }

        $filesize = filesize($p_fileVar['tmp_name']);
        if ($filesize === false) {
            return new PEAR_Error("Archive_FileBase::OnFileUpload(): invalid parameters received.");
        }
        if (get_magic_quotes_gpc()) {
            $fileName = stripslashes($p_fileVar['name']);
        } else {
            $fileName = $p_fileVar['name'];
        }
        if ($this->isValidFileType($fileName) == FALSE) {
            return new PEAR_Error("Archive_FileBase::OnFileUpload(): invalid file type.");
        }
        $target = $Campsite['TMP_DIRECTORY'] . $fileName;
        if (!move_uploaded_file($p_fileVar['tmp_name'], $target)) {
            return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $target), CAMP_ERROR_CREATE_FILE);
        }
        chmod($target, 0644);

        return $target;
    } // fn onFileUpload


    /**
     * Validates an audioclip file by extension.
     *
     * @param $p_fileName
     *      The name of the audioclip file
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function isValidFileType($p_fileName)
    {
        $ext = strtolower(strrchr($p_fileName, '.'));
        if (array_key_exists($ext, $this->m_fileTypes)) {
            return true;
        }
        return false;
    } // fn isValidFileType


    /**
     * @param string
     *
     * @return mixed
     */
    public static function GetFileExtension($p_fileName)
    {
        $ext = strtolower(strrchr($p_fileName, '.'));
        if (empty($ext)) {
            return false;
        }
        return $ext;
    } // fn GetFileExtension


    /**
     * Output the raw values of this object so that it displays nice in HTML.
     * @return void
     */
    public function dumpToHtml()
    {
        echo "<pre>";
        echo $this->m_gunId . "\n";
        print_r($this->m_metaData);
        echo "</pre>";
    } // fn dumpToHtml


    /**
     * @return array
     */
    public static function GetBasicMetadata($p_fileInfo)
    {
        $metaData = array(
            'dc:title' => $p_fileInfo['name'],
            'dc:format' => $p_fileInfo['type'],
            'ls:filename' => $p_fileInfo['name'],
            'ls:filesize' => $p_fileInfo['size']
        );
        return $metaData;
    } // fn GetBasicMetadata


    /**
     * Retrieve a list of Audioclip objects based on the given constraints
     *
     * @param array $conditions
     *      array of struct with fields:
     *          cat: string - metadata category name
     *          op: string - operator, meaningful values:
     *              'full', 'partial', 'prefix',
     *              '=', '<', '<=', '>', '>='
     *          val: string - search value
     * @param string $operator
     *      type of conditions join (any condition matches /
     *      all conditions match), meaningful values: 'and', 'or', ''
     *      (may be empty or ommited only with less then 2 items in
     *      "conditions" field)
     * @param int $limit
     *      limit for result arrays (0 means unlimited)
     * @param int $offset
     *      starting point (0 means without offset)
     * @param string $orderby
     *      string - metadata category for sorting (optional) or array
     *      of strings for multicolumn orderby
     *      [default: dc:creator, dc:source, dc:title]
     * @param bool $desc
     *      boolean - flag for descending order (optional) or array of
     *      boolean for multicolumn orderby (it corresponds to elements
     *      of orderby field)
     *      [default: all ascending]
     *
     * @return array
     *      Array of Audioclip objects
     */
    public static function SearchFiles($p_criteria)
    {
        global $mdefs;

        $xrc = XR_CcClient::Factory($mdefs, true);
        if (PEAR::isError($xrc)) {
            return $xrc;
        }

        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        $result = $xrc->xr_searchMetadata($sessid, $p_criteria);
        if (PEAR::isError($result)) {
            return $result;
        }
        $files = array();
        foreach ($result['results'] as $fileMetaData) {
            $file = new Archive_FileBase($fileMetaData['gunid']);
            if ($file->exists()) {
                $files[] = $file;
            }
        }
        return array($result['cnt'], $files);
    } // fn SearchFiles


    /**
     * Retrieve a list of values of the give category that meet the given constraints
     *
     * @param string $p_category
     *
     * @param array $conditions
     *      array of struct with fields:
     *          cat: string - metadata category name
     *          op: string - operator, meaningful values:
     *              'full', 'partial', 'prefix',
     *              '=', '<', '<=', '>', '>='
     *          val: string - search value
     * @param string $operator
     *      type of conditions join (any condition matches /
     *      all conditions match), meaningful values: 'and', 'or', ''
     *      (may be empty or ommited only with less then 2 items in
     *      "conditions" field)
     * @param int $limit
     *      limit for result arrays (0 means unlimited)
     * @param int $offset
     *      starting point (0 means without offset)
     * @param string $orderby
     *      string - metadata category for sorting (optional) or array
     *      of strings for multicolumn orderby
     *      [default: dc:creator, dc:source, dc:title]
     * @param bool $desc
     *      boolean - flag for descending order (optional) or array of
     *      boolean for multicolumn orderby (it corresponds to elements
     *      of orderby field)
     *      [default: all ascending]
     *
     * @return array
     *      Array of Audioclip objects
     */
    public static function BrowseCategory($p_category, $offset = 0, $limit = 0,
                                          $conditions = array(),
                                          $operator = 'and',
                                          $orderby = 'dc:creator, dc:source, dc:title',
                                          $desc = false)
    {
        global $mdefs;

        $xrc = XR_CcClient::Factory($mdefs, true);
        if (PEAR::isError($xrc)) {
            return $xrc;
        }
        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        $criteria = array('filetype' => 'audioclip',
                          'operator' => $operator,
                          'limit' => $limit,
                          'offset' => $offset,
                          'orderby' => $orderby,
                          'desc' => $desc,
                          'conditions' => $conditions
                         );
        return $xrc->xr_browseCategory($sessid, $p_category, $criteria);
    } // fn BrowseCategory


    /**
     * Stores the file into the storage server.
     *
     * @param string $p_sessId
     *      The session Id
     * @param string $p_filePath
     *      The full path name to the file
     * @param array $p_formData
     *      Array of form data submitted
     *
     * @return int|PEAR_Error
     *      The gunid on success, PEAR Error on failure
     */
    public static function Store($p_sessId, $p_filePath,
                                 $p_metaData, $p_fileType)
    {
        if (file_exists($p_filePath) == false) {
            return new PEAR_Error(getGS('File $1 does not exist', $p_filePath));
        }
        $gunId = null;
        $checkSum = md5_file($p_filePath);
        $xmlString = self::CreateXMLTextFile($p_metaData, $p_fileType);
        $gunId = Archive_FileXMLMetadata::Upload($p_sessId, $p_filePath, $gunId,
            $xmlString, $checkSum, $p_fileType);
        if (PEAR::isError($gunId)) {
            return $gunId;
        }

        $fileXMLMetadata = new Archive_FileXMLMetadata($gunId, $p_fileType);
        $fileDbMetadata = new Archive_FileDatabaseMetadata();
        $fileDbMetadata->create($fileXMLMetadata->m_metaData);
        // Logging
        $logtext = getGS('The file "$1" has been added (gunid = $2)',
            $p_metaData['dc:title'], $gunId);
        Log::Message($logtext, null, 181);

        return $gunId;
    } // fn Store


    /**
     * Use getid3 to retrieve all the metatags for the given file.
     *
     * @param string $p_file
     *      The file to analyze
     *
     * @return array
     *      An array with all the id3 metatags
     */
    public static function AnalyzeFile($p_file)
    {
        require_once($GLOBALS['g_campsiteDir'].'/include/getid3/getid3.php');

        $getid3Obj = new getID3;
        return $getid3Obj->analyze($p_file);
    } // fn AnalyzeFile


    /**
     * This function should be called when a file has been successfully
     * sent to the Storage server. It deletes the temporary file on Local.
     *
     * @param string $p_fileName
     *      The temporary file to delete
     */
    public static function OnFileStore($p_fileName)
    {
        if (file_exists($p_fileName)) {
            @unlink($p_fileName);
        }
    } // fn OnFileStore


    /**
     * Alias of Archive_FileBase::OnFileStore()
     *
     * @param string $p_fileName
     */
    public static function DeleteTemporaryFile($p_fileName)
    {
        self::OnFileStore($p_fileName);
    } // fn DeleteTemporaryFile


    /**
     * Create a XML text file from submitted Archive_File metadata.
     *
     * @param array $p_formData
     *      The form data submited
     *
     * @return string $xmlTextFile
     *      The XML string
     */
    public static function CreateXMLTextFile($p_metaData, $p_fileType)
    {
        $xmlTextFile = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
            ."<$p_fileType>\n"
            ."\t<metadata\n"
            ."\t\txmlns=\"http://mdlf.org/campcaster/elements/1.0/\"\n"
            ."\t\txmlns:ls=\"http://mdlf.org/campcaster/elements/1.0/\"\n"
            ."\t\txmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n"
            ."\t\txmlns:dcterms=\"http://purl.org/dc/terms/\"\n"
            ."\t\txmlns:xml=\"http://www.w3.org/XML/1998/namespace\"\n"
            ."\t>\n";

        foreach($p_metaData as $key => $val) {
            $xmlTextFile .= "\t\t" . XML_Util::createTag($key, array(), $val) . "\n";
        }
        $xmlTextFile .= "\t</metadata>\n</$p_fileType>\n";
        return $xmlTextFile;
    } // fn CreateXMLTextFile


    /**
     * Generates the cache key for the object.
     *
     * @param array optional
     *    $p_recordSet The object data
     */
    public function getCacheKey($p_gunId = null)
    {
        if (empty($p_gunId)) {
        	$gunId = $p_gunId;
        } else {
        	$gunId = $this->m_gunId;
        }
        if (empty($gunId)) {
        	return null;
        }

        $cacheKey = $gunId . '_' . get_class($this);

        return $cacheKey.'_'.get_class($this);
    } // fn getCacheKey
    

    /**
     * Returns true if cache use was enabled
     *
     * @return bool
     */
    public function GetUseCache()
    {
        return self::$m_useCache;
    }


    /**
     * Sets cache enabled/disabled
     *
     * @param bool $p_useCache
     *
     * @return void
     */
    public function SetUseCache($p_useCache)
    {
        self::$m_useCache = $p_useCache;
    }


    /**
     * Initializes the current object from cache if it exists
     *
     * @param array $p_recordSet
     *
     * @return mixed
     *    object The cached object on success
     *    boolean FALSE if the object did not exist
     */
    public function readFromCache($p_gunId = null)
    {
        if (!self::GetUseCache()) {
            return false;
        }

        $cacheKey = '';
        if (empty($p_gunId) || !$this->keyValuesExist()) {
        	return false;
        }

        $cacheKey = $this->getCacheKey($p_gunId);
        $cacheObj = CampCache::singleton();
        $object = $cacheObj->fetch($cacheKey);

        if ($object === false) {
            return false;
        }

        $this->duplicateObject($object);

        return $this;
    }


    /**
     * Writes the object to cache.
     *
     * @return bool
     *    TRUE on success, FALSE on failure
     */
    public function writeCache()
    {
        if (!self::GetUseCache()) {
            return false;
        }

        $cacheKey = $this->getCacheKey();
        if ($cacheKey === false) {
            return false;
        }
        $cacheObj = CampCache::singleton();

        return $cacheObj->add($cacheKey, $this);
    } // fn writeCache
} // class Archive_FileBase

?>