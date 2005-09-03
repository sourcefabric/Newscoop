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
		$translatedString = "$p_translateString (not translated)";
	}
	else {
		$translatedString = $g_translationStrings[$p_translateString];
	}
	if ($numFunctionArgs > 1) {
		for ($i = 1; $i < $numFunctionArgs; $i++){
			$name = '$'.$i;
			$val = func_get_arg($i);
			$translatedString = str_replace($name, $val, $translatedString);
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
			print "The global string is already set in ".$_SERVER[PHP_SELF].": $key<BR>";
		}
	}
	else{
		if (substr($p_value, strlen($p_value)-3)==(":".$_REQUEST["TOL_Language"])){
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
	    $defaultLang =& new LocalizerLanguage($g_localizerConfig['FILENAME_PREFIX'], 
	                                          '', 
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
     * @param string $p_path -
     *      Path to directory where the translation files are located
     *
     * @param string $p_prefix -
     *      Beginning of the file name, either 'locals' or 'globals'.
     *
     * @return void
     */
	function LoadLanguageFiles($p_path, $p_prefix) 
	{
	    global $g_translationStrings;
	    global $g_localizerConfig;
	    if (!isset($_REQUEST['TOL_Language'])){
	        $_REQUEST['TOL_Language'] = $g_localizerConfig['DEFAULT_LANGUAGE'];
	    }
	    
	    $defaultLang =& new LocalizerLanguage($p_prefix, $p_path, $g_localizerConfig['DEFAULT_LANGUAGE']);
	    $userLang =& new LocalizerLanguage($p_prefix, $p_path, $_REQUEST['TOL_Language']);
	    
	    // Load language files
	    $defaultLang->loadFile(Localizer::GetMode());
	    $userLang->loadFile(Localizer::GetMode());	
	    
	    $defaultLangStrings = $defaultLang->getTranslationTable();		    
	    $userLangStrings = $userLang->getTranslationTable();
	    // Prefer the user strings to the default ones.
	    $g_translationStrings = array_merge($g_translationStrings, $defaultLangStrings, $userLangStrings);
	} // fn LoadLanguageFiles

	
	/**
	 * To remove admin install path on calls from other admin scripts using asolute path.
	 *
     * @param string $p_path -
     *      Path to directory where the translation files are located
     *
     * @param string $p_prefix -
     *      Beginning of the file name, either 'locals' or 'globals'.
     *
	 * @return void
	 */
	function LoadLanguageFilesAbs($p_path, $p_prefix) 
	{
	    global $g_localizerConfig;
		//echo $p_path."<br>";
	    $relativePath = str_replace(realpath($g_localizerConfig['BASE_DIR']), '', realpath($p_path));
	    //echo $relativePath."<br>";
	    Localizer::LoadLanguageFiles($relativePath, $p_prefix);
	} // fn LoadLanguageFileAbs
	

    /**
     * Compare a particular language's keys with the default language set.
     *
     * @param string $p_directory -
     *		The directory in which to look for the language files.
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
    function CompareKeys($p_directory, $p_data, $p_findExistingKeys = true) 
    {
        global $g_localizerConfig;
		$localData =& new LocalizerLanguage($g_localizerConfig['FILENAME_PREFIX'], 
		                                    $p_directory, 
		                                    $g_localizerConfig['DEFAULT_LANGUAGE']);
		$localData->loadFile(Localizer::GetMode());
        $globaldata =& new LocalizerLanguage($g_localizerConfig['FILENAME_PREFIX_GLOBAL'], 
                                             '/', 
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
     * Return an array of localizer languages codes, discovered by looking at the file
     * name in the given directory.
     *
     * @return array
     */
    function GetLanguagesInDirectory($p_prefix, $p_directory) 
    {
        global $g_localizerConfig;
        $files = File_Find::mapTreeMultiple($g_localizerConfig['BASE_DIR'].$p_directory, 1);
        $className = "LocalizerFileFormat_".strtoupper(Localizer::GetMode());
        $fileFormat = new $className();
        $languages = $fileFormat->getLanguagesInDirectory($p_prefix, $p_directory);
        return $languages;
    } // fn GetLanguagesInDirectory

    
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
        // but do not scan the language files
        $fileExcludePattern = '/(';
        $patterns = array();
        foreach ($g_localizerConfig['FILE_TYPES'] as $type) {
            $className = 'LocalizerFileFormat_'.strtoupper($type);
            $object =& new $className;
            $patterns[] = '('.$object->getFilePattern().')';
        }
        $fileExcludePattern .= implode('|', $patterns).')/';

        // like getGS('edit "$1"', ...);  '
        $functPattern1 = '/(put|get)gs( )*\(( )*\'([^\']*)\'/iU';                  
        // like getGS("edit '$1'", ...);
        $functPattern2 = '/(put|get)gs( )*\(( )*"([^"]*)"/iU';                     

        // Get all files in this directory
        $files = File_Find::mapTreeMultiple($g_localizerConfig['BASE_DIR'].$p_directory, 1);

        // Get all the PHP files
        foreach ($files as $name) {
            if (preg_match($filePattern, $name) && !preg_match($fileExcludePattern, $name)) {
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
     * @param string $p_directory -
     * @return array
     */
    function FindMissingStrings($p_directory) 
    {
	    $newKeys =& Localizer::FindTranslationStrings($p_directory);
	    $missingKeys =& Localizer::CompareKeys($p_directory, $newKeys, false);
	    $missingKeys = array_unique($missingKeys);	    
	    return $missingKeys;
    } // fn FindMissingStrings
    
    
    /**
     * Return the set of strings in the translation files that are not used in the code.
     * @param string $p_directory -
     * @return array
     */
    function FindUnusedStrings($p_directory) 
    {
        global $g_localizerConfig;
	    $existingKeys =& Localizer::FindTranslationStrings($p_directory);	    
		$localData =& new LocalizerLanguage($g_localizerConfig['FILENAME_PREFIX'], 
		                                    $p_directory, 
		                                    $g_localizerConfig['DEFAULT_LANGUAGE']);
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
     * @param string $p_directory
     * @param string $p_languageCode
     * @param array $p_data
     *
     * @return void
     */
    function ModifyStrings($p_prefix, $p_directory, $p_languageId, $p_data) 
    {
        global $g_localizerConfig;
      	// If we change a string in the default language,
      	// then all the language files must be updated with the new key.
        if ($p_languageId == $g_localizerConfig['DEFAULT_LANGUAGE']) {
	        $languages = Localizer::GetLanguagesInDirectory($p_prefix, $p_directory);
	        foreach ($languages as $language) {
	        	
	        	// Load the language file
	        	$source =& new LocalizerLanguage($p_prefix, $p_directory, $language->getLanguageId());
	        	$source->loadFile(Localizer::GetMode());
	        	
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
				$source->saveFile(Localizer::GetMode());
	        }
        }
      	// We only need to change the values in one file.
        else {
        	// Load the language file
        	$source =& new LocalizerLanguage($p_prefix, $p_directory, $p_languageId);
        	$source->loadFile(Localizer::GetMode());
    		foreach ($p_data as $pair) {
    			$source->updateString($pair['key'], $pair['key'], $pair['value']);
    		}
        	// Save the file
			$source->saveFile(Localizer::GetMode());        	
        }
    } // fn ModifyStrings

    
    /**
     * Synchronize the positions of the strings to the default language file order.
     * @param string $p_prefix
     * @param string $p_directory
     * @return void
     */
    function FixPositions($p_prefix, $p_directory) 
    {
        global $g_localizerConfig;
        $defaultLanguage =& new LocalizerLanguage($p_prefix, $p_directory, $g_localizerConfig['DEFAULT_LANGUAGE']);
        $defaultLanguage->loadFile(Localizer::GetMode());
        $defaultTranslationTable = $defaultLanguage->getTranslationTable();
        $languageIds = Localizer::GetAllLanguages();
        foreach ($languageIds as $languageId) {
        	
        	// Load the language file
        	$source =& new LocalizerLanguage($p_prefix, $p_directory, $languageId);
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
     * @param string $p_directory
     * @param int $p_position
     * @param array $p_newKey
     *
     * @return void
     */
    function AddStringAtPosition($p_prefix, $p_directory, $p_position, $p_newKey) 
    {
        global $g_localizerConfig;
        $languages = Localizer::GetLanguagesInDirectory($p_prefix, $p_directory);
        foreach ($languages as $language) {
        	$source =& new LocalizerLanguage($p_prefix, $p_directory, $language->getLanguageId());
        	$source->loadFile(Localizer::GetMode());
        	if (is_array($p_newKey)) {
        		foreach ($p_newKey as $key) {
        			if ($language->getLanguageId() == $g_localizerConfig['DEFAULT_LANGUAGE']) {
        				$source->addString($key, $key, $p_position);
        			}
        			else {
        				$source->addString($key, '', $p_position);
        			}
        		}
        	}
        	else {
       			if ($Id == $g_localizerConfig['DEFAULT_LANGUAGE']) {
	        		$source->addString($p_newKey, $p_newKey, $p_position);
       			}
       			else {
	        		$source->addString($p_newKey, '', $p_position);       				
       			}
        	}
			$source->saveFile(Localizer::GetMode());
        }
    } // fn AddStringAtPosition


    /**
     * Go through all files matching $p_prefix in $p_directory and remove selected entry.
     * @param string $p_prefix
     * @param string $p_directory
     * @param mixed $p_key -
     *		Can be a string or an array of strings.
     * @return void
     */
    function RemoveString($p_prefix, $p_directory, $p_key) 
    {
        $languages = Localizer::GetLanguagesInDirectory($p_prefix, $p_directory);

        foreach ($languages as $language) {
        	$target =& new LocalizerLanguage($p_prefix, $p_directory, $language->getLanguageId());
        	$target->loadFile(Localizer::GetMode());
        	if (is_array($p_key)) {
        		foreach ($p_key as $key) {
        			$target->deleteString($key);
        		}
        	}
        	else {
        		$target->deleteString($p_key);
        	}
			$target->saveFile(Localizer::GetMode());
        }
    } // fn RemoveString

    
    /**
     * Go through all files matching $p_prefix in $p_directory and swap selected entrys.
     *
     * @param string $p_prefix
     * @param string $p_directory
     * @param int $p_pos1
     * @param int $p_pos2
     *
     * @return void
     */
    function MoveString($p_prefix, $p_directory, $p_pos1, $p_pos2) 
    {
        $languages = Localizer::GetLanguagesInDirectory($p_prefix, $p_directory);
        foreach ($languages as $language) {
			$target =& new LocalizerLanguage($p_prefix, $p_directory, $language->getLanguageId());
			$target->loadFile(Localizer::GetMode());
			$success = $target->moveString($p_pos1, $p_pos2);
			$target->saveFile(Localizer::GetMode());
        }
    } // fn MoveString

    
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
		    $object =& new $className();
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
            } 
            else {
            	// it's a file
                if (preg_match($p_pattern, $file)) {
                    $filelist[] = $p_startdir.'/'.$file;
                }
            }
        }
        return $filelist;
    } // fn SearchFilesRecursive


	/**
     * Go through subdirectorys and create language files for given Id.
     * @param string $p_languageId
     * @return void
     */
    function CreateLanguageFilesRecursive($p_languageId) 
    {
        global $g_localizerConfig;
        $className = "LocalizerFileFormat_".strtoupper(Localizer::GetMode());
        $fileFormat = new $className();        
        $searchPattern = '/'.$fileFormat->getFilePattern($g_localizerConfig['DEFAULT_LANGUAGE']).'/';
        $files = Localizer::SearchFilesRecursive($g_localizerConfig['BASE_DIR'], $searchPattern);
        foreach ($files as $pathname) {
            if ($pathname) {
                $fileNameParts = explode('.', basename($pathname));
                $base = $fileNameParts[0];
                $dir = str_replace($g_localizerConfig['BASE_DIR'], '', dirname($pathname));
                // read the default file
                $defaultLang =& new LocalizerLanguage($base, $dir, $g_localizerConfig['DEFAULT_LANGUAGE']);
                $defaultLang->loadFile(Localizer::GetMode());
                $defaultLang->clearValues();
                $defaultLang->setLanguageId($p_languageId);
                // if file already exists -> skip
                if (!file_exists($defaultLang->getFilePath())) {
	                $defaultLang->saveFile(Localizer::GetMode());
                }
            }
        }
    } // fn CreateLanguageFilesRecursive
	
    
	/**
     * Go through subdirectorys and delete language files for given Id.
     * @param string $p_languageId
     * @return void
     */
    function DeleteLanguageFilesRecursive($p_languageId) 
    {
        global $g_localizerConfig;
        $className = "LocalizerFileFormat_".strtoupper(Localizer::GetMode());
        $fileFormat = new $className();        
        $searchPattern = '/'.$fileFormat->getFilePattern($g_localizerConfig['DEFAULT_LANGUAGE']).'/';
        $files = Localizer::SearchFilesRecursive($g_localizerConfig['BASE_DIR'], $searchPattern);
        //echo "<pre>";print_r($files);echo "</pre>";
        foreach ($files as $pathname) {
            if ($pathname) {
                $fileNameParts = explode('.', basename($pathname));
                $base = $fileNameParts[0];
                $dir = str_replace($g_localizerConfig['BASE_DIR'], '', dirname($pathname));
                $languageFile =& new LocalizerLanguage($base, $dir, $p_languageId);
                if (file_exists($languageFile->getFilePath())) {
                    //echo 'deleteing '.$languageFile->getFilePath().'<br>';
	                unlink($languageFile->getFilePath());
                }
            }
        }
    } // fn CreateLanguageFilesRecursive
    
} // class Localizer
?>