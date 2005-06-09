<?PHP

global $g_localizerLoadedFiles;
class LocalizerFileFormat {
	function load(&$p_localizerLanguage) { }	
	function save(&$p_localizerLanguage) { }
	function getFilePath($p_localizerLanguage) { }
	function getFilePattern($p_languageId = null) { }
	function getLanguagesInDirectory($p_prefix, $p_directory) { }	
} // class LocalizerFileFormat


class LocalizerFileFormat_GS extends LocalizerFileFormat {
    /**
     * Load the translation table from a PHP-GS file.
     *
     * @param LocalizerLanguage p_localizerLanguage
     *		LocalizerLanguage object.
     *
     * @return boolean
     */
	function load(&$p_localizerLanguage) 
	{
    	$p_localizerLanguage->setMode('gs');
        $filePath = LocalizerFileFormat_GS::GetFilePath($p_localizerLanguage);     
        if (file_exists($filePath)) {
	        $lines = file($filePath);
	        foreach ($lines as $line) {
	        	if (strstr($line, "regGS")) {
			        $line = preg_replace('/regGS/', '$p_localizerLanguage->registerString', $line);			    
	        		$success = eval($line);
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
	} // fn load
	    
	
    /**
     * Save the translation table to a PHP-GS file.
     *
     * @param LocalizerLanguage p_localizerLanguage
     *		LocalizerLanguage object.
     *
     * @return string
     *		File contents
     */
	function save(&$p_localizerLanguage) 
	{
    	$data = "<?php\n";
    	$translationTable = $p_localizerLanguage->getTranslationTable();
    	foreach ($translationTable as $key => $value) {
    	    // Escape quote characters.
    	    $key = str_replace('"', '\"', $key);
    	    $value = str_replace('"', '\"', $value);
    		$data .= "regGS(\"$key\", \"$value\");\n";
    	}
    	$data .= "?>";
        $filePath = LocalizerFileFormat_GS::GetFilePath($p_localizerLanguage);
        $p_localizerLanguage->_setSourceFile($filePath);
        // write data to file        
        if (PEAR::isError(File::write($filePath, $data, FILE_MODE_WRITE))) {
        	echo "<br>error writing file<br>";
            return FALSE;
        }
        File::close($filePath, FILE_MODE_WRITE);
        return $data;    	
    } // fn save
    
	
    /**
     * Get a regular expression that will match the name of the file.
     * @param string p_languageId
     *      (optional) If specified, give the pattern that will match this language ID.
     * @return string
     */
	function getFilePattern($p_languageId = null) 
	{
	    global $g_localizerConfig;
	    if (is_null($p_languageId)) {
    	    return '^('.$g_localizerConfig['FILENAME_PREFIX']
    	           .'|'.$g_localizerConfig['FILENAME_PREFIX_GLOBAL'].')\.[a-z]{2,2}\.php$';
	    }
	    else {
    	    return '^('.$g_localizerConfig['FILENAME_PREFIX']
    	           .'|'.$g_localizerConfig['FILENAME_PREFIX_GLOBAL'].')\.'.$p_languageId.'\.php$';	        
	    }
	} // fn getFilePattern
	

	/**
	 * Get the full path to the translation file.
	 * @param LocalizerLanguage p_localizerLanguage
	 * @return string
	 */
	function getFilePath($p_localizerLanguage) 
	{
	    global $g_localizerConfig;
       	return $g_localizerConfig['BASE_DIR'].$p_localizerLanguage->getDirectory()
       	    .'/'.$p_localizerLanguage->getPrefix()
       	    .'.'.$p_localizerLanguage->getLanguageCode().'.php';	    
	} // fn getFilePath
	
	
	/**
	 * Get all supported languages as an array of LanguageMetadata objects.
	 * @return array
	 *     An array of LanguageMetadata
	 */
	function getLanguages() 
	{
    	global $Campsite;
        $query = 'SELECT  Name, OrigName AS NativeName, Code as LanguageCode, Code AS Id
                    FROM Languages
                    ORDER BY Name';
        $languages = $Campsite['db']->getAll($query);
        if (!$languages) {
        	//echo 'Cannot read database campsite.Languages<br>';
        	return array();
        }
        $metadata = array();
        foreach ($languages as $language) {
            $tmpMetadata =& new LanguageMetadata();
            $tmpMetadata->m_englishName = $language['Name'];
            $tmpMetadata->m_nativeName = $language['NativeName'];
            $tmpMetadata->m_languageCode = $language['LanguageCode'];
            $tmpMetadata->m_countryCode = '';
            $tmpMetadata->m_languageId = $language['LanguageCode'];
            $metadata[] = $tmpMetadata;
        }
        return $metadata;
	} // fn getLanguages
	
	/**
	 * @return array
	 */
	function getLanguagesInDirectory($p_prefix, $p_directory) 
	{
	    global $g_localizerConfig;
    	// Detect files directly
        $files = File_Find::mapTreeMultiple($g_localizerConfig['BASE_DIR'].$p_directory, 1);
        $languageDefs = array();
        foreach ($files as $key => $filename) {
            if (preg_match("/$p_prefix\.[a-z]{2}\.php/", $filename)) {
                list($lost, $id, $lost) = explode('.', $filename);
        		$languageDef =& new LanguageMetadata();
        		$languageDef->m_languageId = $id;
        		$languageDef->m_languageCode = $id;
                $languageDefs[] = $languageDef;
            }
        }
        return $languageDefs;
	}
} // class LocalizerFileFormat_GS


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
     * @param LocalizerLanguage p_localizerLanguage
     * @return boolean
     */
	function load(&$p_localizerLanguage) 
	{
    	$p_localizerLanguage->setMode('xml');
        $filePath = LocalizerFileFormat_XML::GetFilePath($p_localizerLanguage);
        if (file_exists($filePath)) {
            $xml = File::readAll($filePath);
            File::close($filePath, FILE_MODE_READ);
	        $unserializer =& new XML_Unserializer($this->m_unserializeOptions);
	        $unserializer->unserialize($xml);
	        $translationArray = $unserializer->getUnserializedData();
	        $p_localizerLanguage->clearValues();
	        if (isset($translationArray['item'])) {
		        foreach ($translationArray['item'] as $translationPair) {
		        	$p_localizerLanguage->registerString($translationPair['key'], $translationPair['value']);
		        }
	        }
	        return true;
        }    	
        else {
        	return false;
        }
	} // fn load
	
	
    /**
     * Write a XML-format translation file.
     * @param LocalizerLanguage p_localizerLanguage
     * @return mixed
     *      The XML that was written on success,
     *      FALSE on error.
     */
	function save(&$p_localizerLanguage) 
	{
    	$saveData = array();
    	$saveData["Id"] = $p_localizerLanguage->getLanguageId();
    	$origTranslationTable =& $p_localizerLanguage->getTranslationTable();
		$saveTranslationTable = array();
		foreach ($origTranslationTable as $key => $value) {
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
        
        $filePath = LocalizerFileFormat_XML::GetFilePath($p_localizerLanguage);
        //echo "Saving as ".$this->m_filePath."<Br>";
        // write data to file        
        if (PEAR::isError(File::write($filePath, $xml, FILE_MODE_WRITE))) {
        	echo "<br>error writing file<br>";
            return FALSE;
        }        
        
        File::close($filePath, FILE_MODE_WRITE);
        
        return $xml;		
	} // fn save
	
	
	/**
	 * Get the full path to the translation file.
	 * @param LocalizerLanguage p_localizerLanguage
	 * @return string
	 */
	function getFilePath($p_localizerLanguage) 
	{
	    global $g_localizerConfig;
       	return $g_localizerConfig['BASE_DIR'].$p_localizerLanguage->getDirectory()
       	    .'/'.$p_localizerLanguage->getPrefix()
       	    .'.'.$p_localizerLanguage->getLanguageCode()
       	    .'_'.$p_localizerLanguage->getCountryCode().'.php';
	} // fn getFilePath

	
	/**
     * Get a regular expression that will match the name of the file.
     * @param string p_languageId
     *      (optional) If specified, give the pattern that will match this language ID.
	 * @return string
	 */
	function getFilePattern($p_languageId = null) 
	{
	    global $g_localizerConfig;
	    if (is_null($p_languageId)) {
    	    return '^('.$g_localizerConfig['FILENAME_PREFIX']
    	           .'|'.$g_localizerConfig['FILENAME_PREFIX_GLOBAL'].')\.[a-z]{2,2}_[a-z]{2,2}\.xml$';
	    }
	    else {
    	    return '^('.$g_localizerConfig['FILENAME_PREFIX']
    	           .'|'.$g_localizerConfig['FILENAME_PREFIX_GLOBAL'].')\.'.$p_languageId.'\.xml$';	        
	    }
	} // fn getFilePattern
	


	/**
	 * Get all supported languages as an array of LanguageMetadata objects.
	 * @return array
	 */
	function getLanguages() 
	{
	    global $g_localizerConfig;
	    $fileName = $g_localizerConfig['BASE_DIR']
	               .$g_localizerConfig['LANGUAGE_METADATA_FILENAME'];
    	if (file_exists($fileName)) {
    		$xml = File::readAll($path);
    		File::rewind($path, FILE_MODE_READ);                
    		$handle =& new XML_Unserializer($this->m_unserializeOptions);
        	$handle->unserialize($xml);
        	$arr = $handle->getUnserializedData();
            $languages = $arr['language'];
            foreach ($languages as $language) {
                $languageDef =& new LanguageMetadata();
                $languageDef->m_languageId = $language['Code'];
                $languageDef->m_languageCode = '';
                $languageDef->m_countryCode = '';
                $languageDef->m_englishName = '';
                $languageDef->m_nativeName = '';
            }
        }
        else {
            return LocalizerFileFormat_XML::getLanguagesInDirectory('locals', '/');
        }
	} // fn getLanguages
	
	
	/**
	 * @return array
	 */
	function getLanguagesInDirectory($p_prefix, $p_directory) 
	{
	    global $g_localizerConfig;
    	// Detect files directly
        $files = File_Find::mapTreeMultiple($g_localizerConfig['BASE_DIR'].$p_directory, 1);
        $languageDefs = array();
        foreach ($files as $key => $filename) {
            if (preg_match("/$p_prefix\.[a-z]{2}_[^.]*\.xml/", $filename)) {
                list($lost, $id, $lost, $lost) = explode('.', $filename);
        		list($languageCode, $countryCode) = explode('_', $id);
        		$languageDef =& new LanguageMetadata();
        		$languageDef->m_languageId = $id;
        		$languageDef->m_languageCode = $languageCode;
        		$languageDef->m_countryCode = $countryCode;
                $languageDefs[] = $code;
            }
        }
        return $languageDefs;
	}
	
} // class LocalizerFileFormat_XML
?>