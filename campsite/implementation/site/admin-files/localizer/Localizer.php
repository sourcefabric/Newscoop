<?php
/**
 * @package Campware
 * This file would normally be split into multiple files but since it must
 * be fast (it gets loaded for every hit to the admin screen), we put it
 * all in one file.
 */

/**
 * Includes
 */
require_once('File.php');
require_once('File/Find.php');
require_once('LocalizerConfig.php');
require_once('LocalizerLanguage.php');
require_once('LanguageMetadata.php');

/**
 * Translate the given string and print it.  This function accepts a variable
 * number of parameters and works something like printf().
 *
 * @param string $p_translateString
 *		The string to translate.
 *
 * @return void
 */
function putGS($p_translateString)
{
	$args = func_get_args();
	echo call_user_func_array('getGS', $args);
} // fn putGS


/**
 * Translate the given string and return it.  This function accepts a variable
 * number of parameters and works something like printf().
 *
 * @param string $p_translateString -
 *		The string to translate.
 *
 * @return string
 */
function getGS($p_translateString)
{
	global $g_translationStrings, $TOL_Language;
	$numFunctionArgs = func_num_args();
	if (!isset($g_translationStrings[$p_translateString]) || ($g_translationStrings[$p_translateString]=='')) {
		$translatedString = "$p_translateString (*)";
	}
	else {
		$translatedString = $g_translationStrings[$p_translateString];
	}
	if ($numFunctionArgs > 1) {
		for ($i = 1; $i < $numFunctionArgs;){
			$name = '$'.$i;
			$nameReversed = $i.'$';
			$parameter = func_get_arg($i);
			if (is_array($parameter)) {
				foreach ($parameter as $array_parameter) {
					$translatedString = str_replace($name, $array_parameter, $translatedString);
					$translatedString = str_replace($nameReversed, $array_parameter, $translatedString);
					$i++;
					$name = '$'.$i;
					$nameReversed = $i.'$';
				}
			} else {
				$translatedString = str_replace($name, $parameter, $translatedString);
				$translatedString = str_replace($nameReversed, $parameter, $translatedString);
			}
			$i++;
		}
	}
	return $translatedString;
} // fn getGS


/**
 * Register a string in the global translation file. (Legacy code for GS files)
 *
 * @param string $p_value
 * @param string $p_key
 * @return void
 */
function regGS($p_key, $p_value)
{
	global $g_translationStrings;
	if (isset($g_translationStrings[$p_key])) {
		if ($p_key!='') {
			print "The global string is already set in ".$_SERVER[PHP_SELF].": $p_key<BR>";
		}
	}
	else{
		if (substr($p_value, strlen($p_value)-3)==(":".camp_session_get('TOL_Language', 'en'))){
			$p_value = substr($p_value, 0, strlen($p_value)-3);
		}
		$g_translationStrings[$p_key] = $p_value;
	}
} // fn regGS


/**
 * The Localizer class handles groups of translation tables (LocalizerLanguages).
 * This class simply acts as a namespace for a group of static methods.
 * @package Campware
 */
class Localizer {

    /**
     * Return the type of files we are currently using, currently
     * either 'gs' or 'xml'.  If not set in the config file, we will
     * do our best to figure out the current mode.
     *
     * @return mixed
     *		Will return 'gs' or 'xml' on success, or NULL on failure.
     */
    function GetMode()
    {
        global $g_localizerConfig;
    	if ($g_localizerConfig['DEFAULT_FILE_TYPE'] != '') {
    		return $g_localizerConfig['DEFAULT_FILE_TYPE'];
    	}
	    $defaultLang = new LocalizerLanguage('globals',
	                                         $g_localizerConfig['DEFAULT_LANGUAGE']);
	    if ($defaultLang->loadGsFile()) {
	    	return 'gs';
	    }
	    elseif ($defaultLang->loadXmlFile()) {
	    	return 'xml';
	    }
	    else {
	    	return null;
	    }
    } // fn GetMode


    /**
     * Load the translation strings into a global variable.
     *
     * @param string $p_prefix -
     *      Beginning of the file name, before the ".php" extension.
     * @param  string $p_languageCode -
     * 		The two-letter language code of the language you want to load.
     * @return void
     */
	function LoadLanguageFiles($p_prefix, $p_languageCode = null)
	{
	    global $g_translationStrings;
	    global $g_localizerConfig;
	    if (is_null($p_languageCode)){
	        $p_languageCode = $g_localizerConfig['DEFAULT_LANGUAGE'];
	    }

	    if (!isset($g_translationStrings)) {
    		$g_translationStrings = array();
        }

        $key = $p_prefix."_".$g_localizerConfig['DEFAULT_LANGUAGE'];
	    if (!isset($g_localizerConfig['LOADED_FILES'][$key])) {
	        $defaultLang = new LocalizerLanguage($p_prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);
    	    $defaultLang->loadFile(Localizer::GetMode());
    	    $defaultLangStrings = $defaultLang->getTranslationTable();
    	    // Merge default language strings into the translation array.
    	    $g_translationStrings = array_merge($g_translationStrings, $defaultLangStrings);
	        $g_localizerConfig['LOADED_FILES'][$key] = true;
	    }
	    $key = $p_prefix."_".$p_languageCode;
	    if (!isset($g_localizerConfig['LOADED_FILES'][$key])) {
    	    $userLang = new LocalizerLanguage($p_prefix, $p_languageCode);
    	    $userLang->loadFile(Localizer::GetMode());
    	    $userLangStrings = $userLang->getTranslationTable();
    	    // Merge user strings into translation array.
    	    $g_translationStrings = array_merge($g_translationStrings, $userLangStrings);
	        $g_localizerConfig['LOADED_FILES'][$key] = true;
	    }
	} // fn LoadLanguageFiles


    /**
     * Compare a particular language's keys with the default language set.
     *
     * @param string $p_prefix -
     *		The prefix of the language files.
     *
     * @param array $p_data -
     *		A set of keys.
     *
     * @param boolean $p_findExistingKeys -
     *		Set this to true to return the set of keys (of the keys given) that already exist,
     *		set this to false to return the set of keys (of the keys given) that do not exist.
     *
     * @return array
     */
    function CompareKeys($p_prefix, $p_data, $p_findExistingKeys = true)
    {
        global $g_localizerConfig;
		$localData = new LocalizerLanguage($p_prefix,
		                                    $g_localizerConfig['DEFAULT_LANGUAGE']);
		$localData->loadFile(Localizer::GetMode());
        $globaldata = new LocalizerLanguage($g_localizerConfig['FILENAME_PREFIX_GLOBAL'],
                                             $g_localizerConfig['DEFAULT_LANGUAGE']);
        $globaldata->loadFile(Localizer::GetMode());

        $returnValue = array();
        foreach ($p_data as $key) {
        	$globalKeyExists = $globaldata->keyExists($key);
        	$localKeyExists = $localData->keyExists($key);
        	if ($p_findExistingKeys && ($globalKeyExists || $localKeyExists)) {
                $returnValue[$key] = $key;
            }
            elseif (!$p_findExistingKeys && !$globalKeyExists && !$localKeyExists) {
            	$returnValue[$key] = $key;
            }
        }

        return $returnValue;
    } // fn CompareKeys


    /**
     * Return an array of localizer languages codes, discovered by looking at the directory
     * names in the /lang directory.
     *
     * @return array
     */
    function GetLanguages()
    {
        global $g_localizerConfig;
        $languages = array();
        if (is_dir($g_localizerConfig['TRANSLATION_DIR'])) {
            if ($dirHandle = opendir($g_localizerConfig['TRANSLATION_DIR'])) {
                while (($file = readdir($dirHandle)) !== false) {
                    $filepath = $g_localizerConfig['TRANSLATION_DIR'].'/'.$file;
                    if ( ($file != '.') && ($file != '..') && ($file[0] != '.')
                         && (filetype($filepath) == "dir")) {
        		        $languageDef = new LanguageMetadata();
        		        $languageDef->m_languageId = $file;
        		        $languageDef->m_languageCode = $file;
                        $languages[] = $languageDef;
                    }
                }
                closedir($dirHandle);
            }
        }
        return $languages;
    } // fn GetLanguages


    /**
     * Search through PHP files and find all the strings that need to be translated.
     * @param string $p_directory -
     * @return array
     */
    function FindTranslationStrings($p_directory)
    {
        global $g_localizerConfig;
        // All .php files
        $filePattern = '/(.*).php/';
        $patterns = array();

        // like get GS('edit "$1"', ...);  '
        $functPattern1 = '/(put|get)gs( )*\(( )*\'([^\']*)\'/iU';
        // like get GS("edit '$1'", ...);
        $functPattern2 = '/(put|get)gs( )*\(( )*"([^"]*)"/iU';

        // Get all files in this directory
        $files = File_Find::mapTreeMultiple($g_localizerConfig['BASE_DIR'].$p_directory, 1);

        // Get all the PHP files
        $filelist = array();
        foreach ($files as $name) {
            if (preg_match($filePattern, $name)) {
            	// list of .php-scripts in this folder
                $filelist[] = $name;
            }
        }

		// Read in all the PHP files.
		$data = array();
        foreach ($filelist as $name) {
            $data = array_merge($data, file($g_localizerConfig['BASE_DIR'].$p_directory.'/'.$name));
        }

       	// Collect all matches
       	$matches = array();
        foreach ($data as $line) {
            if (preg_match_all($functPattern1, $line, $m)) {
                foreach ($m[4] as $match) {
                    $match = str_replace("\\\\", "\\", $match);
                    $matches[$match] = $match;
                }
            }

            if (preg_match_all($functPattern2, $line, $m)) {
                foreach ($m[4] as $match) {
                    $match = str_replace("\\\\", "\\", $match);
                    $matches[$match] = $match;
                }
            }
        }
        asort($matches);
        return $matches;
    } // fn FindTranslationStrings


    /**
     * Return the set of strings in the code that are not in the translation files.
     * @param string $p_prefix
     * @return array
     */
    function FindMissingStrings($p_prefix)
    {
        global $g_localizerConfig;
        if (empty($p_prefix)) {
            return array();
        }
        $dir = $g_localizerConfig["MAP_PREFIX_TO_DIR"][$p_prefix];
	    $newKeys = Localizer::FindTranslationStrings($dir);
	    $missingKeys = Localizer::CompareKeys($p_prefix, $newKeys, false);
	    $missingKeys = array_unique($missingKeys);
	    return $missingKeys;
    } // fn FindMissingStrings


    /**
     * Return the set of strings in the translation files that are not used in the code.
     * @param string $p_prefix
     * @return array
     */
    function FindUnusedStrings($p_prefix)
    {
        global $g_localizerConfig;
        if (empty($p_prefix)) {
            return array();
        }
        $dir = $g_localizerConfig["MAP_PREFIX_TO_DIR"][$p_prefix];
	    $existingKeys = Localizer::FindTranslationStrings($dir);
		$localData = new LocalizerLanguage($p_prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);
		$localData->loadFile(Localizer::GetMode());
		$localTable = $localData->getTranslationTable();
		$unusedKeys = array();
		foreach ($localTable as $key => $value) {
			if (!in_array($key, $existingKeys)) {
				$unusedKeys[$key] = $key;
			}
		}
	    $unusedKeys = array_unique($unusedKeys);
	    return $unusedKeys;
    } // fn FindUnusedStrings


    /**
     * Update a set of strings in a language file.
     * @param string $p_prefix
     * @param string $p_languageCode
     * @param array $p_data
     *
     * @return mixed
     * 		Return TRUE on success, or an array of PEAR_Errors on failure.
     */
    function ModifyStrings($p_prefix, $p_languageId, $p_data)
    {
        global $g_localizerConfig;
      	// If we change a string in the default language,
      	// then all the language files must be updated with the new key.
        if ($p_languageId == $g_localizerConfig['DEFAULT_LANGUAGE']) {
	        $languages = Localizer::GetLanguages();
	        $saveResults = true;
	        foreach ($languages as $language) {

	        	// Load the language file
	        	$source = new LocalizerLanguage($p_prefix, $language->getLanguageId());
	        	$tmpResult = $source->loadFile(Localizer::GetMode());

	        	// If we cant load the file, record the error and move on to the next file.
				if (PEAR::isError($tmpResult)) {
					$saveResults[] = $tmpResult;
					continue;
				}

	        	// For the default language, we set the key & value to be the same.
	        	if ($p_languageId == $language->getLanguageId()) {
	        		foreach ($p_data as $pair) {
	        			$source->updateString($pair['key'], $pair['value'], $pair['value']);
	        		}
	        	}
	        	// For all other languages, we just change the key and keep the old value.
	        	else {
	        		foreach ($p_data as $pair) {
	        			$source->updateString($pair['key'], $pair['value']);
	        		}
	        	}

	        	// Save the file
				$tmpResult = $source->saveFile(Localizer::GetMode());
				if (PEAR::isError($tmpResult)) {
					$saveResults[] = $tmpResult;
				}
	        }
	        return $saveResults;
        }
      	// We only need to change the values in one file.
        else {
        	// Load the language file
        	$source = new LocalizerLanguage($p_prefix, $p_languageId);
        	$result = $source->loadFile(Localizer::GetMode());
        	if (PEAR::isError($result)) {
        		return array($result);
        	}
    		foreach ($p_data as $pair) {
    			$source->updateString($pair['key'], $pair['key'], $pair['value']);
    		}
        	// Save the file
			$result = $source->saveFile(Localizer::GetMode());
			if (PEAR::isError($result)) {
				return array($result);
			} else {
				return true;
			}
        }
    } // fn ModifyStrings


    /**
     * Synchronize the positions of the strings to the default language file order.
     * @param string $p_prefix
     * @return void
     */
    function FixPositions($p_prefix)
    {
        global $g_localizerConfig;
        $defaultLanguage = new LocalizerLanguage($p_prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);
        $defaultLanguage->loadFile(Localizer::GetMode());
        $defaultTranslationTable = $defaultLanguage->getTranslationTable();
        $languageIds = Localizer::GetAllLanguages();
        foreach ($languageIds as $languageId) {

        	// Load the language file
        	$source = new LocalizerLanguage($p_prefix, $languageId);
        	$source->loadFile(Localizer::GetMode());

        	$count = 0;
        	foreach ($defaultTranslationTable as $key => $value) {
        		$source->moveString($key, $count);
        		$count++;
        	}

        	// Save the file
			$source->saveFile(Localizer::GetMode());
        }
    } // fn FixPositions


    /**
     * Go through all files matching $p_prefix in $p_directory and add entry(s).
     *
     * @param string $p_prefix
     * @param int $p_position
     * @param array $p_newKey
     *
     * @return mixed
     * 		Return true on success, PEAR_Error on failure.
     */
    function AddStringAtPosition($p_prefix, $p_position, $p_newKey)
    {
        global $g_localizerConfig;
        $languages = Localizer::GetLanguages();
        foreach ($languages as $language) {
        	$source = new LocalizerLanguage($p_prefix, $language->getLanguageId());
        	$success = $source->loadFile(Localizer::GetMode());
        	if (!$success) {
        		$result = $source->saveFile(Localizer::GetMode());
        		if (PEAR::isError($result)) {
        			return $result;
        		}
        	}
        	if (is_array($p_newKey)) {
        		foreach ($p_newKey as $key) {
        			if ($language->getLanguageId() == $g_localizerConfig['DEFAULT_LANGUAGE']) {
        				$source->addString($key, $key, $p_position);
        			} else {
        				$source->addString($key, '', $p_position);
        			}
        		}
        	} else {
       			if ($Id == $g_localizerConfig['DEFAULT_LANGUAGE']) {
	        		$source->addString($p_newKey, $p_newKey, $p_position);
       			} else {
	        		$source->addString($p_newKey, '', $p_position);
       			}
        	}
			$result = $source->saveFile(Localizer::GetMode());
			if (PEAR::isError($result)) {
				return $result;
			}
        }
        return true;
    } // fn AddStringAtPosition


    /**
     * Go through all files matching $p_prefix remove selected entry.
     * @param string $p_prefix
     * @param mixed $p_key -
     *		Can be a string or an array of strings.
     * @return void
     */
    function RemoveString($p_prefix, $p_key)
    {
        $languages = Localizer::GetLanguages();

        foreach ($languages as $language) {
        	$target = new LocalizerLanguage($p_prefix, $language->getLanguageId());
        	$target->loadFile(Localizer::GetMode());
        	if (is_array($p_key)) {
        		foreach ($p_key as $key) {
        			$target->deleteString($key);
        		}
        	} else {
        		$target->deleteString($p_key);
        	}
			$target->saveFile(Localizer::GetMode());
        }
    } // fn RemoveString


    /**
     * Go through all files matching $p_prefix and swap selected entrys.
     *
     * @param string $p_prefix
     * @param int $p_pos1
     * @param int $p_pos2
     *
     * @return void
     */
    function RepositionString($p_prefix, $p_pos1, $p_pos2)
    {
        $languages = Localizer::GetLanguages();
        foreach ($languages as $language) {
			$target = new LocalizerLanguage($p_prefix, $language->getLanguageId());
			$target->loadFile(Localizer::GetMode());
			$target->moveString($p_pos1, $p_pos2);
			$target->saveFile(Localizer::GetMode());
        }
    } // fn RepositionString


    /**
     * Move a string from one file to another.
     *
     * @param string $p_oldPrefix
     * @param string $p_newPrefix
     * @param string $p_key
     */
    function ChangeStringPrefix($p_oldPrefix, $p_newPrefix, $p_key)
    {
        $languages = Localizer::GetLanguages();
        foreach ($languages as $language) {
			$source = new LocalizerLanguage($p_oldPrefix, $language->getLanguageId());
			$source->loadFile(Localizer::GetMode());
			$srcValue = $source->getValue($p_key);

			$target = new LocalizerLanguage($p_newPrefix, $language->getLanguageId());
			$target->loadFile(Localizer::GetMode());

			$target->addString($p_key, $srcValue);
			$source->deleteString($p_key);

			$target->saveFile(Localizer::GetMode());
			$source->saveFile(Localizer::GetMode());
        }
    } // fn ChangeStringPrefix


   	/**
     * Get all the languages that the interface supports.
     *
     * When in PHP mode, it will get the list from the database.
     * When in XML mode, it will first try to look in the languages.xml file located
     * in the current directory, and if it doesnt find that, it will look at the file names
     * in the top directory and deduce the languages from that.
     *
     * @param string $p_mode
     * @return array
     *		An array of LanguageMetadata objects.
     */
    function GetAllLanguages($p_mode = null)
    {
		if (is_null($p_mode)) {
			$p_mode = Localizer::GetMode();
		}
		$className = "LocalizerFileFormat_".strtoupper($p_mode);
		if (class_exists($className)) {
		    $object = new $className();
		    if (method_exists($object, "getLanguages")) {
		        $languages = $object->getLanguages();
		    }
		}
        //$this->m_languageDefs =& $languages;
    	return $languages;
    } // fn GetAllLanguages


    /**
     * Get a list of all files matching the pattern given.
     * Return an array of strings, each the full path name of a file.
     * @param string $p_startdir
     * @param string $p_pattern
     * @return array
     */
    function SearchFilesRecursive($p_startdir, $p_pattern)
    {
        $structure = File_Find::mapTreeMultiple($p_startdir);

        // Transform it into a flat structure.
        $filelist = array();
        foreach ($structure as $dir => $file) {
        	// it's a directory
            if (is_array($file)) {
                $filelist = array_merge($filelist,
                    Localizer::SearchFilesRecursive($p_startdir.'/'.$dir, $p_pattern));
            } else {
            	// it's a file
                if (preg_match($p_pattern, $file)) {
                    $filelist[] = $p_startdir.'/'.$file;
                }
            }
        }
        return $filelist;
    } // fn SearchFilesRecursive


	/**
     * Create a new directory to store the language files.
     * @param string $p_languageCode
     * @return mixed
     * 		Return TRUE on success and PEAR_Error on failure.
     */
    function CreateLanguageFiles($p_languageCode)
    {
        global $g_localizerConfig;

        if (!is_string($p_languageCode)) {
        	return new PEAR_Error("Localizer::CreateLanguageFiles: Invalid parameter");
        }

        // Make new directory
        $dirName = $g_localizerConfig['TRANSLATION_DIR']."/".$p_languageCode;
        if (!file_exists($dirName)) {
        	if (is_writable($g_localizerConfig['TRANSLATION_DIR'])) {
        		mkdir($dirName);
        	} else {
        		return new PEAR_Error(camp_get_error_message(CAMP_ERROR_MKDIR, $dirName), CAMP_ERROR_MKDIR);
        	}
        }
        return true;
    } // fn CreateLanguageFiles


    /**
     * Delete language files for the given language.
     * @param string $p_languageCode
     * @return mixed
     * 		Return TRUE on success, PEAR_Error on failure.
     */
    function DeleteLanguageFiles($p_languageCode)
    {
        global $g_localizerConfig;
        $langDir = $g_localizerConfig['TRANSLATION_DIR'].'/'.$p_languageCode;
        if (!file_exists($langDir)) {
            return true;
        }
        $files = File_Find::mapTreeMultiple($langDir, 1);
        foreach ($files as $pathname) {
            if (file_exists($pathname)) {
            	if (is_writable($pathname)) {
                	unlink($pathname);
            	} else {
            		return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $pathname), CAMP_ERROR_DELETE_FILE);
            	}
            }
        }
        @rmdir($langDir);
        return true;
    } // fn DeleteLanguageFiles

} // class Localizer
?>