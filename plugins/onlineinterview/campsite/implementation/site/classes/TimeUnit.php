<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, because $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class TimeUnit extends DatabaseObject {
	var $m_dbTableName = 'TimeUnits';
	var $m_keyColumnNames = array('Unit', 'IdLanguage');
	var $m_keyIsAutoIncrement = false;
	var $m_columnNames = array('Unit', 'IdLanguage', 'Name');

	/**
	 * @param string $p_unit
	 * @param int $p_languageId
	 */
	function TimeUnit($p_unit = null, $p_languageId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Unit'] = $p_unit;
		$this->m_data['IdLanguage'] = 1;
		if (!is_null($p_languageId)) {
			$this->m_data['IdLanguage'] = $p_languageId;
		}
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	function SetTimeUnit($p_unit, $p_languageId, $p_name)
	{
		$timeUnit =& new TimeUnit($p_unit, $p_languageId);
		if ($timeUnit->exists()) {
			$timeUnit->setProperty('Name', $p_name);
		} else {
			$timeUnit->create(array('Name' => $p_name));
		}
	} // fn SetTimeUnit


	/**
	 * @return string
	 */
	function getUnit()
	{
		return $this->m_data['Unit'];
	} // fn getUnit


	/**
	 * @return string
	 */
	function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * @return int
	 */
	function getLanguageId()
	{
		return $this->m_data['IdLanguage'];
	} // fn getLanguageId


	/**
	 * Return an array of all time units.
	 * @return array
	 */
	function GetTimeUnits($p_languageCode)
	{
		$queryStr = "SELECT TimeUnits.Unit, TimeUnits.Name "
					." FROM TimeUnits, Languages "
					." WHERE TimeUnits.IdLanguage = Languages.Id "
					." AND Languages.Code = '$p_languageCode'"
					." ORDER BY TimeUnits.Unit ASC";
		$timeUnits = DbObjectArray::Create('TimeUnit', $queryStr);
		if (count($timeUnits) == 0) {
			$queryStr = "SELECT TimeUnits.Unit, TimeUnits.Name "
						." FROM TimeUnits, Languages "
						." WHERE TimeUnits.IdLanguage = Languages.Id "
						." AND Languages.Code = 'en'"
						." ORDER BY TimeUnits.Unit ASC";
			$timeUnits = DbObjectArray::Create('TimeUnit', $queryStr);
		}
		return $timeUnits;
	} // fn GetTimeUnits


} // class TimeUnit

?>