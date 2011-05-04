<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

/**
 * @package Campsite
 */
class Country extends DatabaseObject {
	var $m_dbTableName = 'Countries';
	var $m_keyColumnNames = array('Code', 'IdLanguage');
	var $m_keyIsAutoIncrement = false;
	var $m_columnNames = array('Code', 'IdLanguage', 'Name');

	/**
	 * Constructor.
	 * @param string $p_code
	 * @param int $p_languageId
	 */
	public function Country($p_code = null, $p_languageId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Code'] = $p_code;
		$this->m_data['IdLanguage'] = $p_languageId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	public function create($p_values = null)
	{
		$success = parent::create($p_values);
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Country $1 added', $this->m_data['Name']." (".$this->m_data['Code'].")");
			Log::Message($logtext, null, 131);
		}
		return $success;
	} // fn create


	public function delete()
	{
	        $tmpData = $this->m_data;
		$success = parent::delete();
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Country "$1" ($2) deleted', $tmpData['Name'], $tmpData['Code']);
			Log::Message($logtext, null, 134);
		}
		return $success;
	} // fn delete


	/**
	 * The unique ID of the language in the database.
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->m_data['IdLanguage'];
	} // fn getLanguageId


	/**
	 * Return the english name of this language.
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * Set the name of the country.
	 * @param string $p_value
	 * @return boolean
	 */
	public function setName($p_value)
	{
		$oldValue = $this->m_data['Name'];
		$success = $this->setProperty('Name', $p_value);
		if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Country name $1 changed', $this->m_data['Name']." (".$this->m_data['Code'].")");
			Log::Message($logtext, null, 133);
		}
		return $success;
	} // fn setName


	/**
	 * Get the two-letter code for this language.
	 * @return string
	 */
	public function getCode()
	{
		return $this->m_data['Code'];
	} // fn getCode


	/**
	 *
	 */
	public static function GetNumCountries($p_languageId = null, $p_code = null,
	                                       $p_name = null)
	{
		global $g_ado_db;
		$queryStr = "SELECT COUNT(*) FROM Countries";
		$constraints = array();
		if (!is_null($p_languageId)) {
			$constraints[] = array('IdLanguage', $p_languageId);
		}
		if (!is_null($p_code)) {
			$constraints[] = array('Code', $p_code);
		}
		if (!is_null($p_name)) {
			$constraints[] = array('Name', $p_name);
		}
		if (count($constraints) > 0) {
			$tmpArray = array();
			foreach ($constraints as $constraint) {
				$tmpArray[] = $constraint[0]."='".$constraint[1]."'";
			}
			$queryStr .= " WHERE ".implode(" AND ", $tmpArray);
		}
		$total = $g_ado_db->GetOne($queryStr);
		return $total;
	} // fn GetNumCountries


	/**
	 * @param int $p_languageId
	 * @param string $p_code
	 * @param string $p_name
	 * @param array $p_sqlOptions
	 * @return array
	 */
	public static function GetCountries($p_languageId = null, $p_code = null,
	                                    $p_name = null, $p_sqlOptions = null)
	{
		if (is_null($p_sqlOptions)) {
			$p_sqlOptions = array();
		}
		if (!isset($p_sqlOptions['ORDER BY'])) {
			$p_sqlOptions['ORDER BY'] = array('Name', 'Code', 'IdLanguage');
		}
		$constraints = array();
		if (!is_null($p_languageId)) {
			$constraints[] = array('IdLanguage', $p_languageId);
		}
		if (!is_null($p_code)) {
			$constraints[] = array('Code', $p_code);
		}
		if (!is_null($p_name)) {
			$constraints[] = array('Name', $p_name);
		}
		return DatabaseObject::Search('Country', $constraints, $p_sqlOptions);
	} // fn GetCountries

} // class Country

?>
