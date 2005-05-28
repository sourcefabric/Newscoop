<?PHP
require_once('LocalizerConfig.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');

class LocalizerLanguage {
	var $m_translationTable = array();
	var $m_twoLetterLanguageCode = '';
	var $m_localizerLanguageCode = '';
	var $m_mode = 'php';
	var $m_prefix = '';
	var $m_directory = '';
	var $m_status = false;
	var $m_filePath = '';
    var $m_unserializeOptions = array();
    var $m_serializeOptions = array(
     						// indent with tabs
                           	"indent"           => "\t",       
                           	// root tag
                           	"rootName"         => "language",  
                           	// tag for values with numeric keys 
                           	"defaultTagName"   => "item", 
                           	#"typeHints"        => true,
                           	"keyAttribute"     => "position",
                           	"addDecl"          => true,
                           	"encoding"         => LOCALIZER_ENCODING,
                           	"indentAttributes" => true,
                           	"mode"             => 'simplexml'
            				);
	
	/**
	 * A LocalizerLanguage is basically a translation table.
	 * It can load and save different types of translation files.
	 *
	 * @param string p_languageCode
	 */
	function LocalizerLanguage($p_prefix, $p_directory, $p_languageCode = null) {
		if (!is_null($p_languageCode)) {
			$this->setLanguageCode($p_languageCode);
		}
		$this->m_prefix = $p_prefix;
		$this->m_directory = $p_directory;
	} // constructor
	

	/**
	 * This will return 'php' or 'xml'
	 * @return string
	 */
	function getMode() {
		return $this->m_mode;
	} // fn getMode
	
	
	/**
	 * Set the mode to be 'xml' or 'php'.
	 * @param string p_value
	 * @return void
	 */
	function setMode($p_value) {
		$p_value = strtolower($p_value);
		if (($p_value == 'xml') || ($p_value == 'php')) {
			$this->m_mode = $p_value;
		}
	} // fn setMode
	
	
	/**
	 * Set the language code - this can take either the two-letter language code
	 * or the extended version and it will figure out its counterpart.
	 *
	 * @param string p_languageId
	 * @return void
	 */
	function setLanguageCode($p_languageId) {
		if (strlen($p_languageId) > 2) {
			$this->m_twoLetterLanguageCode = substr($p_languageId, 0, 2);
			$this->m_localizerLanguageCode = $p_languageId;
		}
		else {
			$this->m_twoLetterLanguageCode = $p_languageId;
			if ($this->m_twoLetterLanguageCode == 'en') {
				$this->m_localizerLanguageCode = 'en_English';
			}
			else {
				$this->m_localizerLanguageCode = LocalizerLanguage::_GetLocalizerLanguageNameFromDb($p_languageId);
				if (empty($this->m_localizerLanguageCode)) {
					$this->m_localizerLanguageCode = $p_languageId;
				}
			}
		}		
	} // fn setLanguageCode
	
	
	/**
	 * Given the two-letter language code, get the localizer language code using the database.
	 *
	 * @access private
	 * @param string p_twoLetterLanguageCode
	 * @return string
	 */
    function _GetLocalizerLanguageNameFromDb($p_twoLetterLanguageCode) {
    	global $Campsite;
        $query = 'SELECT CONCAT(Code, "_", Name) AS Id WHERE Code="'.$p_twoLetterLanguageCode.'"';
        return $Campsite['db']->getOne($query);
    } // fn _GetLanguageName

    
    /** 
     * Register a string in the translation table.
     * @param string p_key
     * @param string p_value
     * @param string p_languageId
     * @return void
     */
    function registerString($p_key, $p_value) {
        if (substr($p_value, strlen($p_value)-3) == ":en"){
            $p_value = substr($p_value, 0, strlen($p_value)-3);
        }
        $this->m_translationTable[$p_key] = $p_value;
    } // fn registerString


    /**
     * Return the total number of strings in the translation table.
     * @return int
     */
    function getNumStrings() {
    	return count($this->m_translationTable);
    } // fn getNumStrings
    
    
    /**
     * Get the language code that is in the form <two_letter_language_code>_<english_name_of_language>.
     *
     * @return string
     */
	function getLocalizerLanguageCode() {
		return $this->m_localizerLanguageCode;
	} // fn getLocalizerLanguageCode
	
	
	/**
	 * Get the two-letter language code for the translation table.
	 * @return string
	 */
	function getTwoLetterLanguageCode() {
		return $this->m_twoLetterLanguageCode;
	} // fn getTwoLetterLanguageCode
	

	/**
	 * Return the file path for the last file loaded.
	 * @return string
	 */
	function getSourceFile() {
		return $this->m_filePath;
	} // fn getSourceFile
	
	
	/**
	 * Return true if this LocalizerLanguage has the exact same
	 * translation strings as the given LocalizerLanguage.
	 *
	 * @param LocalizerLanguage p_localizerLanguage
	 * @return boolean
	 */
	function equal($p_localizerLanguage) {
		if (count($this->m_translationTable) != count($p_localizerLanguage->m_translationTable)) {
			return false;
		}
		foreach ($this->m_translationTable as $key => $value) {
			if (!array_key_exists($key, $p_localizerLanguage->m_translationTable)) {
				//echo "missing translation string: '$key'<br>";
				return false;
			}
			if ($p_localizerLanguage->m_translationTable[$key] != $value) {
				//echo "Non-matching values: '".$p_localizerLanguage->m_translationTable[$key]."' != '".$value."'<br>";
				return false;
			}
		}
		return true;
	} // fn equal
	

	/**
	 * Return a table indexed by the english language name, with the value being the 
	 * target language equivalent.
	 *
	 * @return array
	 */
	function getTranslationTable() {
		return $this->m_translationTable;
	}
	
	
	/**
	 * Get the full path to the translation file.
	 *
	 * @param string p_mode
	 *		Either 'php' or 'xml'.
     * @return string
     */
    function getFilePath($p_mode = null) {
    	if (is_null($p_mode)) {
    		$p_mode = $this->m_mode;
    	}    	
    	if ($p_mode == 'xml') {
        	$relativePath = $this->m_directory.'/'.$this->m_prefix.'.'.$this->m_localizerLanguageCode.'.xml';
    	}
    	else {
    		$relativePath = $this->m_directory.'/'.$this->m_prefix.'.'.$this->m_twoLetterLanguageCode.'.php';
    	}
    	return LOCALIZER_BASE_DIR.LOCALIZER_ADMIN_DIR.$relativePath;
    } // fn getFilePath

    
    /**
     * Return TRUE if the given string exists in the translation table.
     * @return boolean
     */
    function keyExists($p_string) {
    	return (isset($this->m_translationTable[$p_string]));
    } // fn stringExists
    
    
    /**
     * Add a string to the translation table.
     *
     * @param string p_key
     *		The english translation of the string.
     *
     * @param string p_value
     *		Optional.  If not specified, the value will be set to the same
     *		value as the key.
     *
     * @param int p_position
     *		Optional.  By default the string will be added to the end of the 
     *		translation table.
     *
     * @return boolean
     */
    function addString($p_key, $p_value = null, $p_position = null) {
    	if (!is_null($p_position) 
    		&& (!is_numeric($p_position) || ($p_position < 0) 
    			|| ($p_position > count($this->m_translationTable)))) {
    		return false;
    	}
    	if (!is_string($p_key) || !is_string($p_value)) {
    		return false;
    	}
    	if (is_null($p_position)) {
    		// Position is not specified - add the string at the end
    		if (is_null($p_value)) {
    			$this->m_translationTable[$p_key] = $p_key;
    		}
    		else {
    			$this->m_translationTable[$p_key] = $p_value;
    		}
    		return true;
    	}
		else {
			// The position is specified
			$begin = array_slice($this->m_translationTable, 0, $p_position);
			$end = array_slice($this->m_translationTable, $p_position);
			if (is_null($p_value)) {
				$newStr = array($p_key => $p_key);
			}
			else {
				$newStr = array($p_key => $p_value);
			}
			$this->m_translationTable = array_merge($begin, $newStr, $end);
			return true;
		}
    } // fn addString
    
    
    /**
     * Get the position of a key or a value.
     * @param string p_key
     * @param string p_value
     * @return mixed
     *		The position of the key/value in the array, FALSE if not found.
     */
    function getPosition($p_key = null, $p_value = null) {
    	$position = 0;
    	if (!is_null($p_key)) {
	    	foreach ($this->m_translationTable as $key => $value) {
	    		if ($p_key == $key) {
	    			return $position;
	    		}
	    		$position++;
	    	}
    	}
    	elseif (!is_null($p_value)) {
	    	foreach ($this->m_translationTable as $value) {
	    		if ($p_value == $value) {
	    			return $position;
	    		}
	    		$position++;
	    	}    		
    	}
    	return false;
    } // fn getPosition
    

    /**
     * Get the string at the given position.
     *
     * @return array
     * 		An array of two elements, the first is the key, the second is the value.
     *		They are indexed by 'key' and 'value'.
     */
    function getStringAtPosition($p_position) {
    	if (is_null($p_position) || !is_numeric($p_position) || ($p_position < 0) 
    			|| ($p_position > count($this->m_translationTable))) {
    		return false;
    	}
    	$returnValue = array_splice($this->m_translationTable, $p_position, 0);
    	$keys = array_keys($returnValue);
    	$key = array_pop($keys);
    	$value = array_pop($returnValue);
    	return array('key' => $key, 'value' => $value);
    } // fn getStringAtPosition
    
    
    /**
     * Change the key and optionally the value of the 
     * translation string.  If the value isnt specified,
     * it is not changed.  If the key does not exist,
     * it will be added.  In this case, you can use p_position
     * to specify where to add the string.
     *
     * @param string p_oldKey
     * @param string p_newKey
     * @param string p_value
     * @param int p_position
     * @return boolean
     */
    function updateString($p_oldKey, $p_newKey, $p_value = null, $p_position = null) {
    	if (!is_string($p_oldKey) || !is_string($p_newKey)) {
    		return false;
    	}
    	// Does the old string exist?
    	if (!isset($this->m_translationTable[$p_oldKey])) {
    		return $this->addString($p_newKey, $p_value, $p_position);
    	}
    	if ($p_oldKey == $p_newKey) {
	    	// Just updating the value
    		if (!is_null($p_value) && ($p_value != $this->m_translationTable[$p_oldKey])) {
    			$this->m_translationTable[$p_oldKey] = $p_value;
    			return true;
    		}
    		// No changes
    		else {
    			return true;
    		}
    	}
    	
    	// Updating the key (and possibly the value)
    	if (is_null($p_value)) {
    		$p_value = $this->m_translationTable[$p_oldKey];
    	}
    	$position = $this->getPosition($p_oldKey);
    	$success = $this->deleteString($p_oldKey);
    	$success &= $this->addString($p_newKey, $p_value, $position);
    	return $success;
    } // fn updateString
    
    
    /**
     * Move a string to a different position in the translation array.
     * This allows similiar strings to be grouped together.
     *
     * @param int p_startPositionOrKey
     * @param int p_endPosition
     *
     * @return boolean
     *		TRUE on success, FALSE on failure.
     */
    function moveString($p_startPositionOrKey, $p_endPosition) {
    	// Check parameters
    	if (is_numeric($p_startPositionOrKey) && (($p_startPositionOrKey < 0) 
    		|| ($p_startPositionOrKey > count($this->m_translationTable)))) {
    		return false;
    	}
    	if (!is_numeric($p_endPosition) || ($p_endPosition < 0)
    		|| ($p_endPosition > count($this->m_translationTable))) {
    		return false;
    	}
    	$startPosition = null;
    	if (is_numeric($p_startPositionOrKey)) {
			$startPosition = $p_startPositionOrKey;
    	}
    	elseif (is_string($p_startPositionOrKey)) {
    		if (!isset($this->m_translationTable[$p_startPositionOrKey])) {
    			return false;
    		}
    		$startPosition = $this->getPosition($p_startPositionOrKey);
    	}
    	else {
    		return false;
    	}
    	
    	// Success if we dont have to move the string anywhere
		if ($startPosition == $p_endPosition) {
			return true;
		} 	
    	// Delete the string in the old position
    	$result = $this->deleteStringAtPosition($startPosition);
    	if (!$result) {
    		return false;
    	}
    	$key = $result['key'];
    	$value = $result['value'];
    	
    	// Add the string in the new position
    	$result = $this->addString($key, $value, $p_endPosition);
    	if (!$result) {
    		return false;
    	}
    	return true;
    } // fn moveString
    
    
    /**
     * Delete the string given by $p_key.
     * @param string p_key
     * @return mixed
     *		The deleted string as array('key' => $key, 'value' => $value) on success,
     *		FALSE if it didnt exist.
     */
    function deleteString($p_key) {
    	if (isset($this->m_translationTable[$p_key])) {
    		$value = $this->m_translationTable[$p_key];
    		unset($this->m_translationTable[$p_key]);
    		return array('key'=>$p_key, 'value'=>$value);
    	}
    	return false;
    } // fn deleteString
    
    
    /**
     * Delete a string at a specific position in the array.
     * @param int p_position
     * @return mixed
     *		The deleted string as array($key, $value) on success, FALSE on failure.
     */
    function deleteStringAtPosition($p_position) {
    	if (!is_numeric($p_position) || ($p_position < 0) 
    		|| ($p_position > count($this->m_translationTable))) {
    		return false;
    	}
    	$returnValue = array_splice($this->m_translationTable, $p_position, 1);
    	$keys = array_keys($returnValue);
    	$key = array_pop($keys);
    	$value = array_pop($returnValue);
    	return array('key' => $key, 'value' => $value);
    } // fn deleteStringAtPosition
    
    
    /**
     * Synchronize the positions of the strings in the translation table
     * with the positions of the string in the default language translation table.
     */
    function fixPositions() {
        $defaultLanguage =& new LocalizerLanguage($this->m_prefix, $this->m_directory, LOCALIZER_DEFAULT_LANG);
        $defaultLanguage->loadXmlFile();
        $defaultTranslationTable = $defaultLanguage->getTranslationTable();
    	$count = 0;
    	$modified = false;
    	foreach ($defaultTranslationTable as $key => $value) {
    		if ($this->getPosition($key) != $count) {
    			$this->moveString($key, $count);
    			$modified = true;
    		}
    		$count++;
    	}
    	return $modified;
    } // fn fixPositions
    
    
    /**
     * Sync with the default language file.  This means
     * adding any missing strings and fixing the positions of the strings to 
     * be the same as the default language file.
     */
    function syncToDefault() {
        $defaultLanguage =& new LocalizerLanguage($this->m_prefix, $this->m_directory, LOCALIZER_DEFAULT_LANG);
        $defaultLanguage->loadXmlFile();
        $defaultTranslationTable = $defaultLanguage->getTranslationTable();
    	$count = 0;
    	$modified = false;
    	foreach ($defaultTranslationTable as $key => $value) {
    		if (!isset($this->m_translationTable[$key])) {
    			$this->addString($key, '', $count);
    			$modified = true;
    		}
    		$count++;
    	}
    	return ($this->fixPositions() || $modified);
    } // fn syncToDefault
    
    
    /**
     * Find the keys/values that match the given keyword.
     *
     * @param string p_keyword
     *
     * @return array
     */
    function search($p_keyword) {
    	$matches = array();
    	foreach ($this->m_translationTable as $key => $value) {
    		if (empty($p_keyword) || stristr($key, $p_keyword) || stristr($value, $p_keyword)) {
    			$matches[$key] = $value;
    		}
    	}
    	return $matches;
    } // fn search
    
    
    /**
     * Load a language file of the given type.
     *
     * @param string p_type
     *		If not specified, it will use the current mode.
     *
     * @return boolean
     */
    function loadFile($p_type = null) {
    	if ($p_type == 'xml') {
    		return $this->loadXmlFile();
    	}
    	elseif ($p_type == 'php') {
    		return $this->loadGsFile();
    	}
    	elseif (!empty($this->m_mode)) {
    		return $this->loadFile($this->m_mode);
    	}
    	else {
    		$mode = Localizer::GetMode();
    		if (!is_null($mode)) {
    			return $this->loadFile($mode);
    		}    		
    	}
    	return false;
    } // fn loadFile
    
    
    /**
     * Save the translation table as the given type.
     *
     * @param string p_type
     *		If not specified, it will use the current mode.
     *
     * @return boolean
     */
    function saveFile($p_type = null) {
    	if ($p_type == 'xml') {
    		return $this->saveAsXml();
    	}
    	elseif ($p_type == 'php') {
    		return $this->saveAsGs();
    	}
    	elseif (!empty($this->m_mode)) {
    		return $this->saveFile($this->m_mode);
    	}
    	else {
    		$mode = Localizer::GetMode();
    		if (!is_null($mode)) {
    			return $this->saveFile($mode);
    		}
    	}
    	return false;    	
    } // fn saveFile
    
    
    /**
     * Read old-style translation file into our translation table.
     * @param string p_file
     * @param string p_langCode
     *		The two-letter language code of the file.
     * @return void
     */
    function loadGsFile() {
    	$this->m_mode = 'php';
        $this->m_filePath = $this->getFilePath('php');     
        //echo $this->m_filePath."<BR>";    
        if (file_exists($this->m_filePath)) {
	        $lines = file($this->m_filePath);
	        foreach ($lines as $line) {
	        	if (strstr($line, "regGS")) {
			        $line = preg_replace('/regGS/', '$this->registerString', $line);
	        		$success = @eval($line);
	        		if ($success === FALSE) {
	        			echo "Error evaluating: ".htmlspecialchars($line)."<br>";
	        		}
	        	}
	        }
	        return true;
        }
        else {
        	return false;
        }
    } // fn loadGsFile

    
    /**
     * Save the translation table to a GS file.
     *
     * @param string p_prefix
     *		File name prefix
     *
     * @param string p_directory
     *		The directory to save in, relative to SERVER_ROOT.
     *
     * @return string
     *		File contents
     */
    function saveAsGs() {
    	$data = "<?php\n";
    	foreach ($this->m_translationTable as $key => $value) {
    		$data .= "regGS('$key', '$value');\n";
    	}
    	$data .= "?>";
        $this->m_filePath = $this->getFilePath('php');
        // write data to file        
        if (PEAR::isError(File::write($this->m_filePath, $data, FILE_MODE_WRITE))) {
        	echo "<br>error writing file<br>";
            return FALSE;
        }
        File::close($this->m_filePath, FILE_MODE_WRITE);
        return $data;    	
    } // fn saveAsGs
    

    /**
     * Read an XML-format translation file into the translation table.
     *
     * @param string p_prefix
     * @param string p_directory
     *
     * @return boolean
     */
    function loadXmlFile() {
    	$this->m_mode = 'xml';
        $this->m_filePath = $this->getFilePath('xml');
        if (file_exists($this->m_filePath)) {
            $xml = File::readAll($this->m_filePath);
            File::close($this->m_filePath, FILE_MODE_READ);
	        $unserializer =& new XML_Unserializer($this->m_unserializeOptions);
	        $unserializer->unserialize($xml);
	        $translationArray = $unserializer->getUnserializedData();
	        $this->m_translationTable = array();
	        if (isset($translationArray['item'])) {
		        foreach ($translationArray['item'] as $translationPair) {
		        	$this->m_translationTable[$translationPair['key']] = $translationPair['value'];
		        }
	        }
	        return true;
        }    	
        else {
        	return false;
        }
    } // fn loadXmlFile
    

    /**
     * Save the translation table to an XML file, 
     * specified by the parameter.
     *
     * @param array p_file
     *
     * @return boolean
     *		TRUE on success, FALSE on failure.
     */
    function saveAsXml() {
    	$saveData = array();
    	$saveData["Id"] = $this->getLocalizerLanguageCode();
		$saveTranslationTable = array();
		foreach ($this->m_translationTable as $key => $value) {
			$saveTranslationTable[] = array('key' => $key, 'value' => $value);
		}
    	$saveData = array_merge($saveData, $saveTranslationTable);
    	
        $serializer =& new XML_Serializer($this->m_serializeOptions);
        $serializer->serialize($saveData);
        $xml = $serializer->getSerializedData();
        if (PEAR::isError($xml)) {
        	echo "<br>error serializing data<br>";
            return FALSE;
        }
        
        $this->m_filePath = $this->getFilePath('xml');
        //echo "Saving as ".$this->m_filePath."<Br>";
        // write data to file        
        if (PEAR::isError(File::write($this->m_filePath, $xml, FILE_MODE_WRITE))) {
        	echo "<br>error writing file<br>";
            return FALSE;
        }        
        
        File::close($this->m_filePath, FILE_MODE_WRITE);
        
        return $xml;
    } // fn saveAsXml

    
    /**
     * Erase all the values in the translation table, but 
     * keep the keys.
     * @return void
     */
    function clearValues() {
    	foreach ($this->m_translationTable as $key => $value) {
    		$this->m_translationTable[$key] = '';
    	}
    } // fn clearValues
    
    
    /**
     * For debugging purposes, displays the the translation table 
     * in an HTML table.
     */
    function dumpToHtml() {
    	echo "<pre>";
    	if (!empty($this->m_filePath)) {
    		echo "<b>File: ".$this->m_filePath."</b><br>";
    	}
    	echo "<b>Language Code: ".$this->m_localizerLanguageCode."</b><br>";
    	echo "<table>";
    	foreach ($this->m_translationTable as $key => $value) {
    		echo "<tr><td>'$key'</td><td>'$value'</td></tr>";
    	}
    	echo "</table>";
    	echo "</pre>";
    } // fn dumpToHtml
    
} // class LocalizerLanguage

?>