<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/Log.php');

/**
 * @package Campsite
 */

/**
 * The global system preferences.
 */
class SystemPref {
	var $m_config = array();
	var $m_columnNames = array(
		'id',
		'fk_user_id',
		'varname',
		'value',
		'last_modified');


	/**
	 * Load the system settings from the database.
	 * @return void
	 */
	function __LoadConfig() {
		global $Campsite;
		global $g_ado_db;
		if (!isset($Campsite['system_preferences'])) {
			$Campsite['system_preferences'] = array();
		}
		// Fetch the user's permissions.
		$queryStr = 'SELECT varname, value FROM UserConfig '
					.' WHERE fk_user_id=0';
		$config = $g_ado_db->GetAll($queryStr);
		if ($config) {
			// Make m_config an associative array.
			foreach ($config as $value) {
				$Campsite["system_preferences"][$value['varname']] = $value['value'];
			}
		}
	} // fn __LoadConfig


	/**
	 * Return the value of the given variable name.
	 * If the variable name does not exist, return null.
	 *
	 * @param string $p_varName
	 * @return mixed
	 */
	function Get($p_varName)
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
	 *
	 * @param string $p_varName
	 * @param mixed $p_value
	 *
	 * @return void
	 */
	function Set($p_varName, $p_value)
	{
		global $Campsite;
		global $g_ado_db;
		if (empty($p_varName) || !is_string($p_varName)) {
			return;
		}
		if (!isset($Campsite['system_preferences'])) {
			SystemPref::__LoadConfig();
		}

		if (isset($Campsite['system_preferences'][$p_varName])) {
			if ($Campsite['system_preferences'][$p_varName] != $p_value) {
				$sql = "UPDATE UserConfig SET value='".mysql_real_escape_string($p_value)."'"
					   ." WHERE fk_user_id=0"
					   ." AND varname='".mysql_real_escape_string($p_varName)."'";
				$g_ado_db->Execute($sql);
				$Campsite['system_preferences'][$p_varName] = $p_value;
			}
		}
	} // fn Set

} // class SystemPref

?>