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
require_once($g_documentRoot.'/classes/ParserCom.php');

/**
 * @package Campsite
 */
class Alias extends DatabaseObject {
	var $m_dbTableName = 'Aliases';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'IdPublication');

	/**
	 * Constructor.
	 * @param int $p_id
	 */
	function Alias($p_id = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		if (!is_null($p_id)) {
    		$this->m_data['Id'] = $p_id;
			$this->fetch();
		}
	} // constructor


	/**
	 * @param array $p_values
	 * @return boolean
	 */
	function create($p_values = null)
	{
		$created = parent::create($p_values);
		if ($created && ($this->m_data["IdPublication"] > 0)) {
			ParserCom::SendMessage('publications', 'modify',
								   array("IdPublication" => $this->m_data['IdPublication']));
		}
		return $created;
	} // fn create


	/**
	 * @return boolean
	 */
	function delete()
	{
		$deleted = parent::delete();
		if ($deleted) {
			ParserCom::SendMessage('publications', 'modify',
								   array("IdPublication" => $this->m_data['IdPublication']));
		}
		return $deleted;
	} // fn delete


	/**
	 * @return int
	 */
	function getId()
	{
		return $this->m_data['Id'];
	} // fn getId


	/**
	 * @return string
	 */
	function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 *
	 */
	function setName($p_name)
	{
		$changed = $this->setProperty('Name', $p_name);
		if ($changed) {
			ParserCom::SendMessage('publications', 'modify',
								   array("IdPublication"=>$this->m_data['IdPublication']));
		}
		return $changed;
	} // fn setName


	/**
	 * @return int
	 */
	function getPublicationId()
	{
		return $this->m_data['IdPublication'];
	} // fn getPublicationId


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	function setPublicationId($p_value)
	{
		return $this->setProperty('IdPublication', $p_value);
	} // fn setPublicationId


	/**
	 * @param int $p_id
	 * @param int $p_publicationId
	 * @param string $p_name
	 * @return array
	 */
	function GetAliases($p_id = null, $p_publicationId = null, $p_name = null)
	{
		$contraints = array();
		if (!is_null($p_publicationId)) {
			$contraints[] = array("IdPublication", $p_publicationId);
		}
		if (!is_null($p_name)) {
			$contraints[] = array("Name", $p_name);
		}
		if (!is_null($p_id)) {
			$contraints[] = array("Id", $p_id);
		}
		return DatabaseObject::Search('Alias', $contraints);
	} // fn GetAliases

	/**
	 * @param string $p_alias_id
	 * @return boolean or ID
	 */
	function AliasExists($p_alias_id) 
	{
	    global $g_ado_db;
	    $queryStr = "SELECT Id, Name FROM Aliases WHERE Name='$p_alias_id'";
	    $res = $g_ado_db->GetRow($queryStr);
	    if ($res) return $res['Id'];
	    else return false;	    
	} // fn AliasExists
} // class Alias

?>