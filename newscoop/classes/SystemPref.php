<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/../db_connect.php';
require_once dirname(__FILE__) . '/DatabaseObject.php';
require_once dirname(__FILE__) . '/Log.php';
require_once dirname(__FILE__) . '/../template_engine/classes/CampSession.php';

/**
 * @package Campsite
 */

/**
 * The global system preferences.
 */
class SystemPref {
    const SESSION_KEY_CACHE_ENGINE = 'campsite_cache_engine';
    const SESSION_KEY_CACHE_ENABLED = 'campsite_cache_enabled';

	const CACHE_KEY_SYSTEM_PREF = 'campsite_system_preferences';

	const CACHE_FILE_NAME = 'system_preferences.php';
	
	var $m_config = array();
	var $m_columnNames = array(
		'id',
		'varname',
		'value',
		'last_modified');


	/**
	 * Load the system settings from the database.
	 * @return void
	 */
	private static function __LoadConfig() {
		global $Campsite, $g_ado_db;

		if (isset($Campsite['system_preferences'])
		|| self::FetchSystemPrefsFromCache()) {
			return;
		}

		$Campsite['system_preferences'] = array();
		// Fetch the user's permissions.
		$queryStr = 'SELECT varname, value FROM SystemPreferences';
		$config = $g_ado_db->GetAll($queryStr);
		if (is_array($config)) {
			foreach ($config as $value) {
				$Campsite["system_preferences"][$value['varname']] = $value['value'];
			}
			self::StoreSystemPrefsInCache();
		}
	} // fn __LoadConfig


	/**
	 * Return the value of the given variable name.
	 * If the variable name does not exist, return null.
	 *
	 * @param string $p_varName
	 * @return mixed
	 */
	public static function Get($p_varName)
	{
		global $Campsite;

		if (!isset($Campsite['system_preferences'])) {
			SystemPref::__LoadConfig();
		}
		if (isset($Campsite['system_preferences'][$p_varName])) {
			return $Campsite['system_preferences'][$p_varName];
		} else {
			return null;
		}
	} // fn Get


	/**
	 * Set the system preferences to the given value.
	 * If the preference key was not already registered, 
	 * it will added to database.
	 *
	 * @param string $p_varName
	 * @param mixed $p_value
	 *
	 * @return void
	 */
	public static function Set($p_varName, $p_value)
	{
		global $Campsite;
		global $g_ado_db;
		if (empty($p_varName) || !is_string($p_varName)) {
			return;
		}
		if (!isset($Campsite['system_preferences'])) {
			SystemPref::__LoadConfig();
		}

	    if (array_key_exists($p_varName, $Campsite['system_preferences'])) {
			if ($Campsite['system_preferences'][$p_varName] != $p_value) {
				$sql = "UPDATE SystemPreferences SET value='".mysql_real_escape_string($p_value)."'"
					   ." WHERE varname='".mysql_real_escape_string($p_varName)."'";
				$g_ado_db->Execute($sql);
				$Campsite['system_preferences'][$p_varName] = $p_value;
				self::StoreSystemPrefsInCache();
			}
	    } else {
	    	$sql = "INSERT INTO SystemPreferences
				    (varname, value) VALUES ('".mysql_real_escape_string($p_varName)."', '".mysql_real_escape_string($p_value)."')";
	    	$g_ado_db->Execute($sql);
	    	$Campsite['system_preferences'][$p_varName] = $p_value;
	    	self::StoreSystemPrefsInCache();
	    }
	} // fn Set


    private static function FetchSystemPrefsFromCache()
    {
    	global $Campsite;

    	$systemPreferences = CampSession::singleton()->getData('system_preferences');
    	if (is_array($systemPreferences)) {
    		$Campsite['system_preferences'] = $systemPreferences;
    		return true;
    	}

    	if (file_exists($GLOBALS['g_campsiteDir'].'/'.self::CACHE_FILE_NAME)) {
    		require_once($GLOBALS['g_campsiteDir'].'/'.self::CACHE_FILE_NAME);
    		return isset($GLOBALS['Campsite']) && is_array($GLOBALS['Campsite'])
    		&& isset($GLOBALS['Campsite']['system_preferences'])
    		&& is_array($GLOBALS['Campsite']['system_preferences']);
    	}
    	return false;
    }


    private static function StoreSystemPrefsInCache()
    {
        global $Campsite;

        CampSession::singleton()->setData('system_preferences', $Campsite['system_preferences'], 'default', true);

        $cacheFileName = $GLOBALS['g_campsiteDir'].'/'.self::CACHE_FILE_NAME;
        $cacheFile = fopen($cacheFileName, 'w+');
        if (!$cacheFile) {
        	return false;
        }
        chmod($cacheFileName, 0600);

        $buffer = "<?php\n\$GLOBALS['Campsite']['system_preferences'] = array(\n";
        $preferences = array();
        foreach ($Campsite['system_preferences'] as $key=>$value) {
        	$preferences[] = "'$key' => '" . addslashes($value) . "'";
        }
        $buffer .= implode(",\n", $preferences) . ");\n?>";
        $result = fwrite($cacheFile, $buffer, strlen($buffer));
        fclose($cacheFile);
        return $result;
    }


    public static function DeleteSystemPrefsFromCache()
    {
    	CampSession::singleton()->setData('system_preferences', null, 'default', true);
    	if (file_exists($GLOBALS['g_campsiteDir'].'/'.self::CACHE_FILE_NAME)) {
    		unlink($GLOBALS['g_campsiteDir'].'/'.self::CACHE_FILE_NAME);
    	}
    	return true;
    }

    /**
     * Return whether statistics collecting was set on.
     *
     * @return bool
     */
    public static function CollectStatistics()
    {
        return (self::Get("CollectStatistics") == 'Y');
    } // fn CollectStatistics

} // class SystemPref

?>
