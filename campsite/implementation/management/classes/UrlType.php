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
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class UrlType extends DatabaseObject {
	var $m_dbTableName = 'URLTypes';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'Description');

	/**
	 * Constructor.
	 * @param int $p_id
	 */
	function UrlType($p_id = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		if (!is_null($p_id)) {
    		$this->m_data['Id'] = $p_id;
			$this->fetch();
		}
	} // constructor


	/**
	 * Return an array of all URL types.
	 * @return array
	 */
	function GetUrlTypes()
	{
		$queryStr = 'SELECT * FROM URLTypes';
		$urlTypes = DbObjectArray::Create('UrlType', $queryStr);
		return $urlTypes;
	} // fn GetUrlTypes


	/**
	 * The unique ID of the URLType.
	 * @return int
	 */
	function getId()
	{
		return $this->m_data['Id'];
	} // fn getId


	/**
	 * Return the name of this URLType.
	 * @return string
	 */
	function getName()
	{
		$name = $this->m_data['Name'];
		switch ($name) {
			case "short names":
				return getGS("short names");
			case "template path":
				return getGS("template path");
			default:
				return "";
		}
	} // fn getName


	/**
	 * Return the description of the URL Type.
	 * @return string
	 */
	function getDescription()
	{
		return $this->m_data['Description'];
	} // fn getDescription


	function GetByName($p_name)
	{
		global $g_ado_db;
		$sql = "SELECT * FROM URLTypes WHERE Name='".mysql_real_escape_string($p_name)."'";
		$row = $g_ado_db->GetRow($sql);
		if ($row && is_array($row)) {
			$urlType =& new UrlType();
			$urlType->fetch($row);
			return $urlType;
		} else {
			return null;
		}
	} // fn GetByName

} // class UrlType

?>