<?php
require_once('PEAR.php');
require_once('DB.php');
require_once('File.php');
require_once('File/Find.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/XML_Serializer/Serializer.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/XML_Serializer/Unserializer.php');

require_once('display.inc.php');
require_once('helpfunctions.php');

require_once('LocalizerConfig.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once('LocalizerLanguage.php');

class Localizer {

    var $m_languageDefs = null;
    
    /**
     * The localizer handles string translation.  This file manipulates
     * groups of translation tables (LocalizerLanguage).
     *
     */
    function Localizer() { }
    
    /**
     * Return a singleton.
     * @return Localizer
     */
    function &getInstance() {
        static $instance;
        if (!$instance) {
            $instance =& new Localizer();
        }
        return $instance;
    } // fn getInstance

    
    /**
     * Return the type of files we are currently using, currently
     * either 'php' or 'xml'.
     *
     * @return mixed
     *		Will return 'php' or 'xml' on success, or NULL on failure.
     */
    function GetMode() {
	    $defaultLang =& new LocalizerLanguage(LOCALIZER_PREFIX, '', LOCALIZER_DEFAULT_LANG);
	    if ($defaultLang->loadGsFile()) {
	    	return 'php';
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
     * @param string p_path
     * @param string p_base
     * @return void
     */
	function LoadLanguageFiles($p_path, $p_base) {
	    global $g_translationStrings;
	    if (!isset($_REQUEST['TOL_Language'])){
	        $_REQUEST['TOL_Language'] = LOCALIZER_DEFAULT_LANG;
	    }
	    
	    $defaultLang =& new LocalizerLanguage($p_base, $p_path, LOCALIZER_DEFAULT_LANG);
	    $userLang =& new LocalizerLanguage($p_base, $p_path, $_REQUEST['TOL_Language']);
	    
	    // Try to load the GS files first
	    if (!$defaultLang->loadGsFile()) {
		    $defaultLang->loadXmlFile();	    	
		    $userLang->loadXmlFile();	    	
	    }
	    else {
	    	$userLang->loadGsFile();
	    }
	    
	    $defaultLangStrings = $defaultLang->getTranslationTable();		    
	    $userLangStrings = $userLang->getTranslationTable();
	    // Prefer the user strings to the default ones.
	    $g_translationStrings = array_merge($g_translationStrings, $defaultLangStrings, $userLangStrings);
	} // fn LoadLanguageFiles

	
	/**
	 * To remove admin install path on calls from other admin scripts using asolute path
	 */
	function LoadLanguageFilesAbs($p_path, $p_base) {
	    $relativePath = str_replace(realpath(LOCALIZER_BASE_DIR.LOCALIZER_ADMIN_DIR), '', realpath($p_path));
	    Localizer::LoadLanguageFiles($relativePath, $p_base);
	}
	

	/**
	 * Get the full path to the translation file.
	 *
	 * @param array p_file
	 * @param string p_languageId
	 * @param string p_type 
	 *		Either 'php' or 'xml'.
     * @return string
     */
    function GetTranslationFilePath($p_file, $p_languageId, $p_type='xml') {
        return LOCALIZER_BASE_DIR . LOCALIZER_ADMIN_DIR
        	. $p_file['dir'].'/'.$p_file['base'].'.'.$p_languageId.'.'.$p_type;
    }

    
    /**
     * Return the keys that already exist in the local and global file.
     *
     * @param string p_directory
     *		The directory in which to look for the language files.
     *
     * @param array p_data
     *		A set of keys.
     *
     * @param boolean p_findExistingKeys
     *		Set this to true to return the set of keys (of the keys given) that already exist,
     *		set this to false to return the set of keys (of the keys given) that do not exist.
     *
     * @return array
     */
    function CompareKeys($p_directory, $p_data, $p_findExistingKeys = true) {
        $testGS = Localizer::ConvertStringArray($p_data, 1, 0, LOCALIZER_DENY_HTML);
        
		$localData =& new LocalizerLanguage(LOCALIZER_PREFIX, $p_directory, LOCALIZER_DEFAULT_LANG);
		$localData->loadXmlFile();
        $globaldata =& new LocalizerLanguage(LOCALIZER_PREFIX_GLOBAL, '/', LOCALIZER_DEFAULT_LANG);
        $globaldata->loadXmlFile();

        $returnValue = array();
        foreach ($testGS as $key) {
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
     *
     *
     */
    function ConvertStringArray($p_input, $p_rmSlash, $p_chQuot, $p_html) {
        if (is_array($p_input)) {
            foreach ($p_input as $key => $val) {
                if (is_array($val)) {
                    $arr[$key] = Localizer::ConvertStringArray($val, $p_rmSlash, $p_chQuot, $p_html);
                } else {
                    $arr[$key] = Display::ToWebString($val, $p_rmSlash, $p_chQuot, $p_html);
                }
            }
            return ($arr);
        }

        return Display::ToWebString($p_input, $p_rmSlash, $p_chQuot, $p_html);
    }


    /**
     * Return an array of localizer languages codes, discovered by looking at the file
     * name in the given directory.
     *
     * @return array
     */
    function _FindLangFilesIds($p_base, $p_directory) {
        $files = File_Find::mapTreeMultiple(LOCALIZER_BASE_DIR.LOCALIZER_ADMIN_DIR.$p_directory, 1);

        foreach ($files as $key => $filename) {
            if (preg_match("/$p_base\.[a-z]{2}_[^.]*\.xml/", $filename)) {
                list($lost, $code, $lost, $lost) = explode('.', $filename);
                $langIds[] = $code;
            }
        }

        return $langIds;
    }

    
    /**
     * Get a list of all files matching the pattern given.
     * Return an array of strings, each the full path name of a file.
     * @return array
     */
    function SearchFilesRecursive($startdir, $pattern, $sep) {
        $structure = File_Find::mapTreeMultiple($startdir);

        // Transform it into a flat structure.
        foreach ($structure as $dir => $file) {
        	// it's a directory
            if (is_array($file)) {
                $filelist .= Localizer::SearchFilesRecursive($startdir.'/'.$dir, $pattern, $sep);
            } 
            else {
            	// it's a file
                if (preg_match($pattern, $file)) {
                    $filelist .= $sep.$startdir.'/'.$file;
                }
            }
        }
        return $filelist;
    } // fn SearchFilesRecursive

    
    /**
     * Go through subdirectorys and create language files for given Id.
     */
    function CreateLanguageFilesRecursive($p_languageCode) {
        $search = '/('.LOCALIZER_PREFIX.'|'.LOCALIZER_PREFIX_GLOBAL.').'.LOCALIZER_DEFAULT_LANG.'.xml/';     
        $sep = '|';
        $files = Localizer::SearchFilesRecursive(LOCALIZER_START_DIR, $search, $sep);
        $files = explode($sep, $files);

        foreach ($files as $pathname) {
            if ($pathname) {
                $base = explode('.', basename($pathname));
                $file = array('base' => $base[0],
                              'dir'  => dirname($pathname));
                // read the default file
                $defaultLang =& new LocalizerLanguage($file['base'], $file['dir'], LOCALIZER_DEFAULT_LANG);
                $defaultLang->loadXmlFile();
                $defaultLang->clearValues();
                $defaultLang->setLanguageCode($p_languageCode);
                // if file already exists -> skip
                if (!file_exists($defaultLang->getFilePath())) {
	                $defaultLang->saveAsXml();
                }
            }
        }
    } // fn CreateLanguageFilesRecursive

    
    /**
     * Get all the languages that the interface supports.
     * @param string p_mode
     * @return array
     *		An array of array("Id", "Name", "NativeName", "Code").
     */
    function GetLanguages($p_mode = null) {
    	if (is_null($this->m_languageDefs)) {
    		if (is_null($p_mode)) {
    			$p_mode = Localizer::GetMode();
    		}
	        switch ($p_mode) {
	        case 'xml':
		    	if (file_exists('./languages.xml')) {
            		$xml = File::readAll($path);
            		File::rewind($path, FILE_MODE_READ);                
            		$handle =& new XML_Unserializer($this->unserializeoptions);
		        	$handle->unserialize($xml);
		        	$arr = $handle->getUnserializedData();
	                $languages = $arr['language'];
	            }
	            else {
	            	// Detect files directly
	            	$languageCodes = Localizer::_FindLangFilesIds('locals', '/');
	            	$languages = array();
	            	if (is_array($languageCodes)) {
		            	foreach ($languageCodes as $code) {
		            		$parts = explode('_', $code);
		            		$language = array();
		            		$language['Id'] = $code;
		            		$language['Name'] = $parts[1];
		            		$language['NativeName'] = $parts[1];
		            		$language['Code'] = $parts[0];
		            		$languages[] = $language;
		            	}
	            	}
	            }
	            break;
	
	        case 'php':
		    	global $Campsite;
		        $query = 'SELECT  Name, OrigName AS NativeName, Code, CONCAT(Code, "_", Name) AS Id
		                    FROM Languages
		                    ORDER BY Id';
		        $languages = $Campsite['db']->getAll($query);
	            if (!$languages) {
	            	return getGS('cannot read $1', 'campsite.Languages').'<br>';
	            }
	            break;
	        } // switch
	        $this->m_languageDefs =& $languages;
	    	return $languages;
    	}
    } // fn getLanguages


    /**
     * Search through PHP files and find all the strings that need to be translated.
     * @param string p_directory
     * @return array
     */
    function FindTranslationStrings($p_directory) {
        // All .php files
        $filePattern = '/(.*).php/';                                               
        // but do not scan the language files
        $fileDisquPattern = '/('.LOCALIZER_PREFIX.'|'.LOCALIZER_PREFIX_GLOBAL.').(.*).(xml|php)/';      
        // like getGS('edit "$1"', ...);  '
        $functPattern1 = '/(put|get)gs( )*\(( )*\'([^\']*)\'/iU';                  
        // like getGS("edit '$1'", ...);
        $functPattern2 = '/(put|get)gs( )*\(( )*"([^"]*)"/iU';                     

        // Get all files in this directory and the one below it
        $files = File_Find::mapTreeMultiple(LOCALIZER_BASE_DIR.LOCALIZER_ADMIN_DIR.$p_directory, 1);

        // Get all the PHP files
        foreach ($files as $name) {
            if (preg_match($filePattern, $name) && !preg_match($fileDisquPattern, $name)) {
            	// list of .php-scripts in this folder
                $filelist[] = $name;                                                    
            }
        }
        
		// Read in all the PHP files.
        foreach ($filelist as $name) {                                                  
            $data = array_merge($data, file(LOCALIZER_BASE_DIR.LOCALIZER_ADMIN_DIR.$p_directory.'/'.$name));
        }

       	// Collect all matches
        foreach ($data as $line) {
            if (preg_match_all($functPattern1, $line, $m)) {                            
                foreach ($m[4] as $match) {
                    $matches[$match] = $match;
                }
            }

            if (preg_match_all($functPattern2, $line, $m)) {
                foreach ($m[4] as $match) {
                    $matches[$match] = $match;
                }
            }
        }
        asort($matches);
        return $matches;
    } // fn FindTranslationStrings
    
    
    /**
     * Return the set of strings in the code that are not in the translation files.
     * @param string p_directory
     * @return array
     */
    function FindMissingStrings($p_directory) {
	    $newKeys =& Localizer::FindTranslationStrings($p_directory);
	    $missingKeys =& Localizer::CompareKeys($p_directory, $newKeys, false);
	    $missingKeys = array_unique($missingKeys);	    
	    return $missingKeys;
    } // fn FindMissingStrings
    
    
    /**
     * Return the set of strings in the translation files that are not used in the code.
     * @param string p_directory
     * @return array
     */
    function FindUnusedStrings($p_directory) {
	    $existingKeys =& Localizer::FindTranslationStrings($p_directory);	    
		$localData =& new LocalizerLanguage(LOCALIZER_PREFIX, $p_directory, LOCALIZER_DEFAULT_LANG);
		$localData->loadXmlFile();
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
     * @param string p_prefix
     * @param string p_directory
     * @param string p_languageCode
     * @param array p_data
     *
     * @return void
     */
    function ModifyStrings($p_prefix, $p_directory, $p_languageCode, $p_data) {
    	// Do some cleanup
        $p_data = Localizer::ConvertStringArray($p_data, true, false, LOCALIZER_DENY_HTML);
        
      	// If we change a string in the default language,
      	// then all the language files must be updated with the new key.
        if ($p_languageCode == LOCALIZER_DEFAULT_LANG) {
	        $languageIds = Localizer::_FindLangFilesIds($p_prefix, $p_directory);
	        foreach ($languageIds as $languageId) {
	        	
	        	// Load the language file
	        	$source =& new LocalizerLanguage($p_prefix, $p_directory, $languageId);
	        	$source->loadXmlFile();
	        	
	        	// For the default language, we set the key & value to be the same.
	        	if ($p_languageCode == $languageId) {
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
				$source->saveAsXml();
	        }
        }
      	// We only need to change the values in one file.
        else {
        	// Load the language file
        	$source =& new LocalizerLanguage($p_prefix, $p_directory, $p_languageCode);
        	$source->loadXmlFile();
    		foreach ($p_data as $pair) {
    			$source->updateString($pair['key'], $pair['key'], $pair['value']);
    		}
        	// Save the file
			$source->saveAsXml();        	
        }
    } // fn ModifyStrings

    
    /**
     * Synchronize the positions of the strings to the default language file order.
     */
    function FixPositions($p_prefix, $p_directory) {
        $defaultLanguage =& new LocalizerLanguage($p_prefix, $p_directory, LOCALIZER_DEFAULT_LANG);
        $defaultLanguage->loadXmlFile();
        $defaultTranslationTable = $defaultLanguage->getTranslationTable();
        $languageIds = Localizer::GetLanguages();
        foreach ($languageIds as $languageId) {
        	
        	// Load the language file
        	$source =& new LocalizerLanguage($p_prefix, $p_directory, $languageId);
        	$source->loadXmlFile();

        	$count = 0;
        	foreach ($defaultTranslationTable as $key => $value) {
        		$source->moveString($key, $count);
        		$count++;
        	}
        	
        	// Save the file
			$source->saveAsXml();
        }    	
    } // fn FixPositions
    
    
    /**
     * Go through all files matching $p_base in $p_directory and add entry(s).
     *
     * @param string p_base
     * @param string p_directory
     * @param int p_position
     * @param array p_newKey
     *
     * @return void
     */
    function AddStringAtPosition($p_base, $p_directory, $p_position, $p_newKey) {
    	// do some cleanup
        $p_newKey = Localizer::ConvertStringArray($p_newKey, 1, 0, LOCALIZER_DENY_HTML);     
        $languageIds = Localizer::_FindLangFilesIds($p_base, $p_directory);
        foreach ($languageIds as $Id) {
        	$source =& new LocalizerLanguage($p_base, $p_directory, $Id);
        	$source->loadXmlFile();
        	if (is_array($p_newKey)) {
        		foreach ($p_newKey as $key) {
        			if ($Id == LOCALIZER_DEFAULT_LANG) {
        				$source->addString($key, $key, $p_position);
        			}
        			else {
        				$source->addString($key, '', $p_position);
        			}
        		}
        	}
        	else {
       			if ($Id == LOCALIZER_DEFAULT_LANG) {
	        		$source->addString($p_newKey, $p_newKey, $p_position);
       			}
       			else {
	        		$source->addString($p_newKey, '', $p_position);       				
       			}
        	}
			$source->saveAsXml();
        }
    } // fn AddStringAtPosition


    /**
     * Go through all files matching $p_base in $p_directory and remove selected entry.
     * @param string p_base
     * @param string p_directory
     * @param mixed p_key
     *		Can be a string or an array of strings.
     * @return void
     */
    function RemoveString($p_base, $p_directory, $p_key) {
        $languageIds = Localizer::_FindLangFilesIds($p_base, $p_directory);

        foreach ($languageIds as $languageId) {
        	$target =& new LocalizerLanguage($p_base, $p_directory, $languageId);
        	$target->loadXmlFile();
        	if (is_array($p_key)) {
        		foreach ($p_key as $key) {
        			$target->deleteString($key);
        		}
        	}
        	else {
        		$target->deleteString($p_key);
        	}
			$target->saveAsXml();
        }
    } // fn RemoveString

    
    /**
     * Go through all files matching $file[base] in $file[dir] and swap selected entrys.
     * @return void
     */
    function MoveString($p_prefix, $p_directory, $p_pos1, $p_pos2) {
        $Ids = Localizer::_FindLangFilesIds($p_prefix, $p_directory);
        foreach ($Ids as $Id) {
			$target =& new LocalizerLanguage($p_prefix, $p_directory, $Id);
			$target->loadXmlFile();
			$success = $target->moveString($p_pos1, $p_pos2);
			$target->saveAsXml();
        }
    } // fn MoveString

    
} // class Data
?>