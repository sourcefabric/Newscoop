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
	 */
	public function Alias($p_idOrName = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		if (is_numeric($p_idOrName)) {
    		$this->m_data['Id'] = $p_idOrName;
			$this->fetch();
		} elseif (!empty($p_idOrName)) {
			$this->m_keyColumnNames = array('Name');
			$this->m_data['Name'] = $p_idOrName;
			$this->fetch();
			$this->m_keyColumnNames = array('Id');
		}
	} // constructor


	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->m_data['Id'];
	} // fn getId


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * Wrapper around DatabaseObject::setProperty
	 *
	 * @see classes/DatabaseObject#setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql)
	 * @param string $p_dbColumnName
	 */
    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        if ($p_dbColumnName == 'Name') {
            $this->m_keyColumnNames = array('Name');
            $this->resetCache();
            $this->m_keyColumnNames = array('Id');
        }
        return parent::setProperty($p_dbColumnName, $p_value);
    } // fn setProperty


	/**
	 *
	 */
	public function setName($p_name)
	{
	    return $this->setProperty('Name', $p_name);
	} // fn setName


	/**
	 * @return int
	 */
	public function getPublicationId()
	{
		return $this->m_data['IdPublication'];
	} // fn getPublicationId


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	public function setPublicationId($p_value)
	{
		return $this->setProperty('IdPublication', $p_value);
	} // fn setPublicationId


	/**
	 * Get all the aliases that match the given criteria.
	 *
	 * @param int $p_id
	 * @param int $p_publicationId
	 * @param string $p_name
	 * @return array
	 */
	public static function GetAliases($p_id = null, $p_publicationId = null, $p_name = null)
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

} // class Alias

?>