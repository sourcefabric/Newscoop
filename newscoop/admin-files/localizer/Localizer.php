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
require_once(dirname(__FILE__).'/LocalizerConfig.php');
require_once(dirname(__FILE__).'/LocalizerLanguage.php');
require_once(dirname(__FILE__).'/LanguageMetadata.php');


function isGS($p_translateString)
{
    global $g_translationStrings;
	return isset($g_translationStrings[$p_translateString]);
}


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
	global $g_translationStrings;
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
			print "The global string is already set in ".$_SERVER['PHP_SELF'].": $p_key<BR>";
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

    // annotations to be translated, separated with |
    const ANNOTATIONS = 'label|title';

    /**
     * Return the type of files we are currently using, currently
     * either 'gs' or 'xml'.  If not set in the config file, we will
     * do our best to figure out the current mode.
     *
     * @return mixed
     *		Will return 'gs' or 'xml' on success, or NULL on failure.
     */
    public static function GetMode()
    {
        global $g_localizerConfig;
    	if ($g_localizerConfig['DEFAULT_FILE_TYPE'] != '') {
    		return $g_localizerConfig['DEFAULT_FILE_TYPE'];
    	}
	    $defaultLang = new LocalizerLanguage('globals',
	                                         $g_localizerConfig['DEFAULT_LANGUAGE']);
	    if ($defaultLang->loadFile('GS')) {
	    	return 'gs';
	    }
	    elseif ($defaultLang->loadFile('XML')) {
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
	public static function LoadLanguageFiles($p_prefix, $p_languageCode = null)
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
    public static function CompareKeys($p_prefix, $p_data, $p_findExistingKeys = true)
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
    public static function GetLanguages()
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
    public static function FindTranslationStrings($p_directory)
    {
        global $g_localizerConfig;

        if (!is_array($p_directory)) {
            $p_directory = array($p_directory);
        }

        // scan for files
        $files = array();
        $root = rtrim($g_localizerConfig['BASE_DIR'], '/');
        foreach ($p_directory as $dir) {
            if (strpos($dir, '*') !== FALSE) { // loads subdirectories /*/*.php
                $files = array_merge($files, glob("$root/$dir/*.*"));
                $dir = rtrim($dir, '*');
            }

            $realpath = realpath("$root/$dir");
            if (!$realpath) { // not found
                continue;
            }

            if (!is_dir($realpath)) { // add file if specified
                $files[] = $realpath;
                continue;
            }

            $files = array_merge($files, glob("$realpath/*.*"));
        }

        // extensions filter
        $extensions = array('php', 'phtml');
        $filelist = array_filter($files, function($file) use ($extensions) {
            return in_array(pathinfo($file, PATHINFO_EXTENSION), $extensions);
        });

        // like get GS('edit "$1"', ...);  '
        $functPattern1 = '/(put|get)gs( )*\(( )*\'([^\']*)\'/iU';
        // like get GS("edit '$1'", ...);
        $functPattern2 = '/(put|get)gs( )*\(( )*"([^"]*)"/iU';

		// Read in all the PHP files.
		$data = array();
        foreach ($filelist as $file) {
            $data = array_merge($data, file($file));
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

            // translate annotations
            if (preg_match_all('/\* @(' . self::ANNOTATIONS . ') (.*)$/', $line, $m)) {
                foreach ($m[2] as $match) {
                    $match = trim($match);
                    if (!empty($match)) {
                        $matches[$match] = $match;
                    }
                }
            }
        }
        asort($matches);
        return $matches;
    } // fn FindTranslationStrings


    /**
     * The method creates an flat array of full paths
     * out of an deep array mapping directory structure,
     * and filter .php files.
     *
     * @param array $p_entries
     * @param string $p_subdir (for recursive calls by itself)
     * @return array() Flat list of files
     */
    private static function CompilePhpFileList(array $p_entries, $p_subdir=null)
    {
        // All .php files
        $filePattern = '/(.*).php$/';
        $filelist = array();

        foreach ($p_entries as $subdir => $entry) {
            if (is_array($entry)) {
                $subdir = isset($p_subdir) ? $p_subdir.DIR_SEP.$subdir : $subdir;
                $filelist = array_merge($filelist, self::CompilePhpFileList($entry, $subdir));
            } else {
                if (preg_match($filePattern, $entry)) {
                	// list of .php-scripts in this folder
                    $filelist[] = isset($p_subdir) ? $p_subdir.DIR_SEP.$entry : $entry;
                }
            }
        }
        return $filelist;
    }


    /**
     * Return the set of strings in the code that are not in the translation files.
     * @param string $p_prefix
     * @return array
     */
    public static function FindMissingStrings($p_prefix)
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
    public static function FindUnusedStrings($p_prefix)
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
    public static function ModifyStrings($p_prefix, $p_languageId, $p_data)
    {
        global $g_localizerConfig;
      	// If we change a string in the default language,
      	// then all the language files must be updated with the new key.
        if ($p_languageId == $g_localizerConfig['DEFAULT_LANGUAGE']) {
	        $languages = Localizer::GetLanguages();
	        $saveResults = array();
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
	        return count($saveResults) == 0 ? true : $saveResults;
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
    public static function FixPositions($p_prefix)
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
    public static function AddStringAtPosition($p_prefix, $p_position, $p_newKey)
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
    public static function RemoveString($p_prefix, $p_key)
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
    public static function RepositionString($p_prefix, $p_pos1, $p_pos2)
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
    public static function ChangeStringPrefix($p_oldPrefix, $p_newPrefix, $p_key)
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
    public static function GetAllLanguages($p_mode = null)
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
     * Create a new directory to store the language files.
     * @param string $p_languageCode
     * @return mixed
     * 		Return TRUE on success and PEAR_Error on failure.
     */
    public static function CreateLanguageFiles($p_languageCode)
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
    public static function DeleteLanguageFiles($p_languageCode)
    {
        global $g_localizerConfig;
        $langDir = $g_localizerConfig['TRANSLATION_DIR'].'/'.$p_languageCode;
        if (!file_exists($langDir)) {
            return true;
        }

        $iterator = new DirectoryIterator($langDir);
        foreach ($iterator as $file) {
            if ($file->isDot()) { // ignore dots
                continue;
            }

            if (!$file->isWritable()) {
                return new PEAR_Error(camp_get_error_message(CAMP_ERROR_DELETE_FILE, $file->getRealpath()), CAMP_ERROR_DELETE_FILE);
            }

            unlink($file->getRealpath());
        }

        @rmdir($langDir);
        return true;
    } // fn DeleteLanguageFiles

    /**
     * Return information about overall, translated and untralslated string count.
     *
     * @param string $prefix
     * @param string $target_lang
     * @return array
     */
    public static function GetTranslationStatus($prefix, $target_lang)
    {
        global $g_localizerConfig;

        $defaultLang = new LocalizerLanguage($prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);
        $targetLang = new LocalizerLanguage($prefix, $target_lang);
        $mode = Localizer::GetMode();
        $defaultLang->loadFile($mode);
        $targetLang->loadFile($mode);
        $sourceStrings = $defaultLang->getTranslationTable();
        $targetStrings = $targetLang->getTranslationTable();

        $translated = 0;
        $untranslated = 0;

        foreach ($sourceStrings as $k => $v) {
            if (strlen($targetStrings[$k])) {
                $translated++;
            } else {
                $untranslated++;
            }
        }
        return array('all' => count($sourceStrings), 'translated' => $translated, 'untranslated' => $untranslated);
    }

}
