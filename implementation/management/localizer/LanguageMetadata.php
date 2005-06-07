<?PHP
require_once('LocalizerConfig.php');
require_once('Localizer.php');
require_once('LocalizerLanguage.php');

class LanguageMetadata {
	var $m_languageDefs = null;
	var $m_languageId = '';
	var $m_englishName = '';
	var $m_nativeName = '';
	var $m_languageCode = '';
	var $m_countryCode = '';
	
	function LanguageMetadata() {
	} // constructor
	
	
	/**
	 * The unique ID of the language in the form <Two Letter Language Code>_<Two Letter Country Code>.
	 * For example, english is "en_US".
	 * @return string
	 */
	function getLanguageId() {
		return $this->m_languageId;
	} // fn getLanguageId
	
	
	/**
	 * Return the english name of this language.
	 * @return string
	 */
	function getEnglishName() {
		return $this->m_englishName;
	} // fn getEnglishName
	
	
	/**
	 * Return the name of the language as written in the language itself.
	 * @return string
	 */
	function getNativeName() {
		return $this->m_nativeName;
	} // fn getNativeName
	
	
	/**
	 * Get the two-letter code for this language.
	 * @return string
	 */
	function getLanguageCode() {
		return $this->m_languageCode;
	} // fn getLanguageCode

	
	/**
	 * Get the two-letter code for the country.
	 * @return string
	 */
	function getCountryCode() {
		return $this->m_countryCode;
	} // fn getCountryCode

	
	/**
     * Get all the languages that the interface supports.
     *
     * When in PHP mode, it will get the list from the database.
     * When in XML mode, it will first try to look in the languages.xml file located
     * in the current directory, and if it doesnt find that, it will look at the file names
     * in the top directory and deduce the languages from that.
     *
     * @param string p_mode
     * @return array
     *		An array of array("Id", "Name", "NativeName", "LanguageCode").
     */
//    function GetAllLanguages($p_mode = null) {
//    	if (is_null($this->m_languageDefs)) {
//    		if (is_null($p_mode)) {
//    			$p_mode = Localizer::GetMode();
//    		}
//    		$className = "LocalizerFileFormat_".strtoupper($p_mode);
//    		if (class_exists($className)) {
//    		    $object =& new $className();
//    		    if (method_exists($object, "getLanguages")) {
//    		        $languages = $object->getLanguages();
//    		    }
//    		}
//	        switch ($p_mode) {
//	        case 'xml':
//		    	if (file_exists(LOCALIZER_BASE_DIR.LOCALIZER_LANGUAGE_METADATA_FILENAME)) {
//            		$xml = File::readAll($path);
//            		File::rewind($path, FILE_MODE_READ);                
//            		$handle =& new XML_Unserializer($this->unserializeoptions);
//		        	$handle->unserialize($xml);
//		        	$arr = $handle->getUnserializedData();
//	                $languages = $arr['language'];
//	            }
//	            else {
//	            	// Detect files directly
//	            	$languageIds = Localizer::_GetLanguageIdsInDirectory('locals', '/');
//	            	$languages = array();
//	            	if (is_array($languageIds)) {
//		            	foreach ($languageIds as $languageId) {
//		            		$language = array();
//		            		$language['Id'] = $languageId;
//		            		$language['Name'] = '';
//		            		$language['NativeName'] = '';
//		            		$parts = explode('_', $languageId);
//		            		$language['LanguageCode'] = $parts[0];
//		            		$language['CountryCode'] = $parts[1];
//		            		$languages[] = $language;
//		            	}
//	            	}
//	            }
//	            break;
	
//	        case 'gs':
//		    	global $Campsite;
//		        $query = 'SELECT  Name, OrigName AS NativeName, Code as LanguageCode, Code AS Id
//		                    FROM Languages
//		                    ORDER BY NativeName';
//		        $languages = $Campsite['db']->getAll($query);
//	            if (!$languages) {
//	            	return getGS('cannot read $1', 'campsite.Languages').'<br>';
//	            }
//	            break;
//	        } // switch
//	        $this->m_languageDefs =& $languages;
//	    	return $languages;
//    	}
//    } // fn GetLanguages	
	
} // class
?>