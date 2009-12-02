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
     * Constructor
     *
     * @param string $p_gunId
     *      The file gunid
     */
    public function __construct($p_gunId = null)
    {
        if (!is_null($p_gunId)) {
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
        }
    } // constructor


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
     * Returns the unique identifier value for the file
     *
     * @return string
     *      The file global unique identifier
     */
    public function getGunId()
    {
    	return $this->m_gunId;
    } // fn getGunId


    public function getType()
    {
        $format = explode('/', $this->getMetatagValue('format'));
	return $format[0];
    }


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
     * @return boolean|PEAR_Error
     *      TRUE on success, PEAR Error on failure
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
                if ($p_formData['f_'.$key.'_'.$element_encode]) {
                    list($predicate_ns, $predicate) = explode(':', $v['element']);
                    $recordSet['gunid'] = $this->m_gunId;
                    $recordSet['predicate_ns'] = $predicate_ns;
                    $recordSet['predicate'] = $predicate;
                    $recordSet['object'] = $p_formData['f_'.$key.'_'.$element_encode];
                    $tmpMetadataObj = new Archive_FileMetadataEntry($recordSet);
                    $metaData[strtolower($v['element'])] = $tmpMetadataObj;
                }
            }
        }

        if (sizeof($metaData) == 0) return false;

        $fileXMLMetadataObj = new Archive_FileXMLMetadata($this->m_gunId);
        if ($fileXMLMetadataObj->update($metaData) == false) {
            return new PEAR_Error(getGS('Cannot update file metadata on storage server'));
        }
        $fileDbMetadataObj = new Archive_FileDatabaseMetadata($this->m_gunId);
        if ($fileDbMetadataObj->update($metaData) == false) {
            return new PEAR_Error(getGS('Cannot update file metadata on Campsite'));
        }
        return true;
    } // fn editMetadata


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
     * Validates an audioclip file by its extension.
     *
     * @param $p_fileName
     *      The name of the audioclip file
     *
     * @return boolean
     *      TRUE on success, FALSE on failure
     */
    public function isValidFileType($p_fileName)
    {
        foreach ($this->m_fileTypes as $t) {
            if (preg_match('/'.str_replace('/', '\/', $t).'$/i', $p_fileName))
                return true;
        }
        return false;
    } // fn isValidFileType


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

        $xrc = XR_CcClient::Factory($mdefs);
	if (PEAR::isError($xrc)) {
	    return $xrc;
	}

	// TODO: get the proper session id
        $sessid = camp_session_get('cc_sessid', '');
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

        $xrc = XR_CcClient::Factory($mdefs);
		if (PEAR::isError($xrc)) {
			return $xrc;
		}
        $sessid = camp_session_get('cc_sessid', '');
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
            return new PEAR_Error(getGS('File $1 does not exist', $p_fileName));
        }
        $gunId = null;
        $checkSum = md5_file($p_filePath);
        $xmlString = self::CreateXMLTextFile($p_metaData, $p_fileType);
        $gunId = Archive_FileXMLMetadata::Upload($p_sessId, $p_filePath, $gunId,
						 $xmlString, $checkSum, $p_fileType);
        if (PEAR::isError($gunId)) {
            return $gunId;
        }

        $fileXMLMetadata = new Archive_FileXMLMetadata($gunId);
        $fileDbMetadata = new Archive_FileDatabaseMetadata();
        $fileDbMetadata->create($fileXMLMetadata->m_metaData);
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
	// TODO: change audioClip tag to fit file type, if necessary
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

} // class Archive_FileBase

?>