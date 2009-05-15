<?php
/**
 * @package Campsite
 */

if (!isset($g_campsiteDir)) {
	$g_campsiteDir = dirname(dirname(__FILE__));
}

require_once($g_campsiteDir.'/db_connect.php');
require_once($g_campsiteDir.'/classes/DatabaseObject.php');
require_once($g_campsiteDir.'/classes/Log.php');
require_once($g_campsiteDir.'/template_engine/classes/CampSession.php');

/**
 * @package Campsite
 */

/**
 * The global system preferences.
 */
class SystemPref {
    const SESSION_KEY_CACHE_ENGINE = 'campsite_cache_engine';

	const CACHE_KEY_SYSTEM_PREF = 'campsite_system_preferences';
	
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
		global $Campsite;
		global $g_ado_db;

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
			CampSession::singleton()->setData(self::SESSION_KEY_CACHE_ENGINE,
			$Campsite['system_preferences']['CacheEngine']);
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
			if ($p_varName == 'CacheEngine') {
				return CampSession::singleton()->getData(self::SESSION_KEY_CACHE_ENGINE);
			}

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
			}
	    } else {
				$sql = "INSERT INTO SystemPreferences 
				        (varname, value) VALUES ('".mysql_real_escape_string($p_varName)."', '".mysql_real_escape_string($p_value)."')";
				$g_ado_db->Execute($sql);
				$Campsite['system_preferences'][$p_varName] = $p_value;   
	    }
	    self::DeleteSystemPrefsFromCache();
	} // fn Set


    private static function FetchSystemPrefsFromCache()
    {
    	global $Campsite;
        if (CampCache::IsEnabled()) {
            $systemPref = CampCache::singleton()->fetch(self::CACHE_KEY_SYSTEM_PREF);
            if (is_array($systemPref)) {
                $Campsite['system_preferences'] = $systemPref;
                return true;
            }
        }
        return false;
    }


    private static function StoreSystemPrefsInCache()
    {
        global $Campsite;
    	if (CampCache::IsEnabled()) {
            return CampCache::singleton()->store(self::CACHE_KEY_SYSTEM_PREF,
            $Campsite['system_preferences']);
        }
        return false;
    }


    public static function DeleteSystemPrefsFromCache()
    {
        CampSession::singleton()->setData(SystemPref::SESSION_KEY_CACHE_ENGINE, null, 'default', true);
    	if (CampCache::IsEnabled()) {
            return CampCache::singleton()->delete(self::CACHE_KEY_SYSTEM_PREF);
        }
        return false;
    }
} // class SystemPref

?>
