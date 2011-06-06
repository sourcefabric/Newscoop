<?PHP
/**
 * @package Campware
 */

/**
 * Abstract interface for the localizer to access data from different sources.
 * @package Campware
 * @abstract
 */
class LocalizerFileFormat {
	function load(&$p_localizerLanguage) { }
	function save(&$p_localizerLanguage) { }
	function getFilePath($p_localizerLanguage) { }
} // class LocalizerFileFormat


/**
 * @package Campware
 */
class LocalizerFileFormat_GS extends LocalizerFileFormat {
    /**
     * Load the translation table from a PHP-GS file.
     *
     * @param LocalizerLanguage $p_localizerLanguage
     *		LocalizerLanguage object.
     *
     * @return mixed
     * 		TRUE on success, PEAR_Error on failure.
     */
	function load(&$p_localizerLanguage)
	{
	    global $g_localizerConfig;
    	$p_localizerLanguage->setMode('gs');
        $filePath = LocalizerFileFormat_GS::GetFilePath($p_localizerLanguage);
        //echo "Loading $filePath<br>";
        if (file_exists($filePath) && is_readable($filePath)) {
	        $lines = file($filePath);
	        foreach ($lines as $line) {
	        	if (strstr($line, "regGS")) {
			        $line = preg_replace('/regGS/', '$p_localizerLanguage->registerString', $line);
	        		$success = eval($line);
	        		if ($success === FALSE) {
	        			return new PEAR_Error("Error evaluating: ".htmlspecialchars($line));
	        		}
	        	}
	        }
	        return true;
        } else {
        	return new PEAR_Error(CAMP_ERROR_READ_FILE, CAMP_ERROR_READ_FILE);
        }
	} // fn load


    /**
     * Save the translation table to a PHP-GS file.
     *
     * @param LocalizerLanguage $p_localizerLanguage
     *		LocalizerLanguage object.
     *
     * @return int
     *		CAMP_SUCCESS
     * 		CAMP_ERROR_MKDIR
     * 		CAMP_ERROR_WRITE_FILE
     */
	function save(&$p_localizerLanguage)
	{
	    global $g_localizerConfig;
    	$data = "<?php \n";
    	$translationTable = $p_localizerLanguage->getTranslationTable();
    	foreach ($translationTable as $key => $value) {
    	    // Escape quote characters.
    	    $key = str_replace('"', '\"', $key);
    	    $value = str_replace('"', '\"', $value);
    	    // do not insert $key and $value variables in between double quotes
    	    // escape sequences may be interpreted and this will modify the string
    	    $data .= "regGS(\"" . $key . "\", \"" . $value . "\");\n";
    	}
    	$data .= "?>";
        $filePath = LocalizerFileFormat_GS::GetFilePath($p_localizerLanguage);
        $p_localizerLanguage->_setSourceFile($filePath);

        // Create the language directory if it doesnt exist.
        $country = $p_localizerLanguage->getCountryCode() ? '_' : null;
        if (substr($p_localizerLanguage->m_prefix, 0, 7) == 'plugin_') {
            // use the plugin storage location
            $pluginName = str_replace('plugin_', '', $p_localizerLanguage->m_prefix);
	        $dirName = CS_PATH_PLUGINS.DIR_SEP.$pluginName.DIR_SEP.'admin-files'.DIR_SEP.'lang'.DIR_SEP
	        .$p_localizerLanguage->getLanguageCode().$country.$p_localizerLanguage->getCountryCode();
        } else {
            // use the default storage location
            $dirName = $g_localizerConfig['TRANSLATION_DIR'].'/'.$p_localizerLanguage->getLanguageCode()
            .$country.$p_localizerLanguage->getCountryCode();
        }

        if (!file_exists($dirName)) {
        	if (is_writable(dirname($dirName))) {
            	mkdir($dirName);
        	} else {
        		return new PEAR_Error(camp_get_error_message(CAMP_ERROR_MKDIR, $dirName), CAMP_ERROR_MKDIR);
        	}
        }

        // Write data to the file
        if (!file_exists($filePath)) {
        	if (is_writable($dirName)) {
                file_put_contents($filePath, $data);
	        } else {
	        	return new PEAR_Error(camp_get_error_message(CAMP_ERROR_WRITE_FILE, $filePath), CAMP_ERROR_WRITE_FILE);
	        }
        } else {
	        if (is_writable($filePath)) {
                file_put_contents($filePath, $data);
	        } else {
	        	return new PEAR_Error(camp_get_error_message(CAMP_ERROR_WRITE_FILE, $filePath), CAMP_ERROR_WRITE_FILE);
	        }
        }
        return CAMP_SUCCESS;
    } // fn save


	/**
	 * Get the full path to the translation file.
	 * @param LocalizerLanguage $p_localizerLanguage
	 * @return string
	 */
	function getFilePath($p_localizerLanguage)
	{
	    global $g_localizerConfig;
        $country = $p_localizerLanguage->getCountryCode() ? '_' : null;
	    if (substr($p_localizerLanguage->m_prefix, 0, 7) == 'plugin_') {
	        // use the plugin storage location
	        $pluginName = str_replace('plugin_', '', $p_localizerLanguage->m_prefix);
	        $path = CS_PATH_PLUGINS.DIR_SEP.$pluginName.DIR_SEP.'admin-files'.DIR_SEP.'lang'.DIR_SEP
	        .$p_localizerLanguage->getLanguageCode().$country.$p_localizerLanguage->getCountryCode()
	        .'/'.$p_localizerLanguage->getPrefix().'.php';
	       return $path;
	    } else {
	       // use the default storage location
       	    return $g_localizerConfig['TRANSLATION_DIR'].'/'.$p_localizerLanguage->getLanguageCode()
       	    . $country . $p_localizerLanguage->getCountryCode() .'/'.$p_localizerLanguage->getPrefix().'.php';
	    }
	} // fn getFilePath


	/**
	 * Get all supported languages as an array of LanguageMetadata objects.
	 * @return array
	 *     An array of LanguageMetadata
	 */
	function getLanguages()
	{
    	global $g_ado_db;
        $query = 'SELECT  Name, OrigName AS NativeName, Code as LanguageCode, Code AS Id
                    FROM Languages
                    ORDER BY Name';
        $languages = $g_ado_db->getAll($query);
        if (!$languages) {
        	//echo 'Cannot read database campsite.Languages<br>';
        	return array();
        }
        $metadata = array();
        foreach ($languages as $language) {
            $tmpMetadata = new LanguageMetadata();
            $tmpMetadata->m_englishName = $language['Name'];
            $tmpMetadata->m_nativeName = $language['NativeName'];
            $tmpMetadata->m_languageCode = $language['LanguageCode'];
            $tmpMetadata->m_countryCode = '';
            $tmpMetadata->m_languageId = $language['LanguageCode'];
            $metadata[] = $tmpMetadata;
        }
        return $metadata;
	} // fn getLanguages

} // class LocalizerFileFormat_GS


/**
 * @package Campware
 */
class LocalizerFileFormat_XML extends LocalizerFileFormat {
    var $m_serializeOptions = array();
    var $m_unserializeOptions = array();

    function LocalizerFileFormat_XML()
    {
        global $g_localizerConfig;
        $this->m_serializeOptions = array(
             						// indent with tabs
                                   	"indent"           => "\t",
                                   	// root tag
                                   	"rootName"         => "language",
                                   	// tag for values with numeric keys
                                   	"defaultTagName"   => "item",
                                   	"keyAttribute"     => "position",
                                   	"addDecl"          => true,
                                   	"encoding"         => $g_localizerConfig['FILE_ENCODING'],
                                   	"indentAttributes" => true,
                                   	"mode"             => 'simplexml'
                    				);
    }


    /**
     * Read an XML-format translation file into the translation table.
     * @param LocalizerLanguage $p_localizerLanguage
     * @return boolean
     */
	function load(&$p_localizerLanguage)
	{
    	$p_localizerLanguage->setMode('xml');
        $filePath = LocalizerFileFormat_XML::GetFilePath($p_localizerLanguage);
        if (file_exists($filePath)) {
            $xml = File::readAll($filePath);
            File::close($filePath, FILE_MODE_READ);
	        $unserializer = new XML_Unserializer($this->m_unserializeOptions);
	        $unserializer->unserialize($xml);
	        $translationArray = $unserializer->getUnserializedData();
	        $p_localizerLanguage->clearValues();
	        if (isset($translationArray['item'])) {
		        foreach ($translationArray['item'] as $translationPair) {
		        	$p_localizerLanguage->registerString($translationPair['key'], $translationPair['value']);
		        }
	        }
	        return true;
        } else {
        	return false;
        }
	} // fn load


    /**
     * Write a XML-format translation file.
     * @param LocalizerLanguage $p_localizerLanguage
     * @return mixed
     *      The XML that was written on success,
     *      FALSE on error.
     */
	function save(&$p_localizerLanguage)
	{
    	$saveData = array();
    	$saveData["Id"] = $p_localizerLanguage->getLanguageId();
    	$origTranslationTable = $p_localizerLanguage->getTranslationTable();
		$saveTranslationTable = array();
		foreach ($origTranslationTable as $key => $value) {
			$saveTranslationTable[] = array('key' => $key, 'value' => $value);
		}
    	$saveData = array_merge($saveData, $saveTranslationTable);

        $serializer = new XML_Serializer($this->m_serializeOptions);
        $serializer->serialize($saveData);
        $xml = $serializer->getSerializedData();
        if (PEAR::isError($xml)) {
            return CAMP_ERROR;
        }

        // Create the language directory if it doesnt exist.
        $country = $p_localizerLanguage->getCountryCode() ? '_' : null;
        $dirName = $g_localizerConfig['TRANSLATION_DIR'].'/'.$p_localizerLanguage->getLanguageCode()
            .$country.$p_localizerLanguage->getCountryCode();
        if (!file_exists($dirName)) {
        	if (is_writable($dirName)) {
            	mkdir($dirName);
        	} else {
        		return CAMP_ERROR_MKDIR;
        	}
        }

        $filePath = LocalizerFileFormat_XML::GetFilePath($p_localizerLanguage);
        // write data to file
        if (is_writable($filePath)) {
        	File::write($filePath, $xml, FILE_MODE_WRITE);
        } else {
        	return CAMP_ERROR_WRITE_FILE;
        }

        File::close($filePath, FILE_MODE_WRITE);
        return CAMP_SUCCESS;
	} // fn save


	/**
	 * Get the full path to the translation file.
	 * @param LocalizerLanguage $p_localizerLanguage
	 * @return string
	 */
	function getFilePath($p_localizerLanguage)
	{
	    global $g_localizerConfig;
	    $country = $p_localizerLanguage->getCountryCode() ? '_' : null;
       	return $g_localizerConfig['TRANSLATION_DIR'].'/'
       	    .$p_localizerLanguage->getLanguageCode()
       	    .$country.$p_localizerLanguage->getCountryCode()
       	    .'/'.$p_localizerLanguage->getPrefix().'.php';
	} // fn getFilePath


	/**
	 * Get all supported languages as an array of LanguageMetadata objects.
	 * @return array
	 */
	function getLanguages()
	{
	    global $g_localizerConfig;
	    $fileName = $g_localizerConfig['TRANSLATION_DIR']
	               .$g_localizerConfig['LANGUAGE_METADATA_FILENAME'];
    	if (file_exists($fileName)) {
    		$xml = File::readAll($path);
    		File::rewind($path, FILE_MODE_READ);
    		$handle = new XML_Unserializer($this->m_unserializeOptions);
        	$handle->unserialize($xml);
        	$arr = $handle->getUnserializedData();
            $languages = $arr['language'];
            foreach ($languages as $language) {
                $languageDef = new LanguageMetadata();
                $languageDef->m_languageId = $language['Code'];
                $languageDef->m_languageCode = '';
                $languageDef->m_countryCode = '';
                $languageDef->m_englishName = '';
                $languageDef->m_nativeName = '';
            }
        } else {
            return Localizer::GetLanguages();
        }
	} // fn getLanguages


} // class LocalizerFileFormat_XML
?>
