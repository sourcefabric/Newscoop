<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Translation.php');

/**
 * @package Campsite
 */
class ObjectType extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_keyIsAutoIncrement = true;
	var $m_dbTableName = 'ObjectTypes';
	var $m_columnNames = array('id',
							   'name');

	/**
	 * @param string $p_idOrName
	 */
	public function __construct($p_idOrName = null)
	{
        if (!is_null($p_idOrName)) {
            if (is_numeric($p_idOrName)) {
                $this->m_data['id'] = $p_idOrName;
		    } else {
		        $this->m_data['name'] = $p_idOrName;
		        $this->m_keyColumnNames = array('name');
		    }
            $this->fetch();
        }
	} // constructor


    /**
     * Wrapper around DatabaseObject::setProperty
     * @see classes/DatabaseObject#setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql)
     */
    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        if ($p_dbColumnName == 'name') {
            $this->m_keyColumnNames = array('name');
            $this->resetCache();
            $this->m_keyColumnNames = array('id');
        }
        return parent::setProperty($p_dbColumnName, $p_value);
    }


	public function delete()
	{
		if (!$this->exists()) {
			return false;
		}

		// Delete the description
		Translation::deletePhrase($this->m_data['fk_description_id']);

		// Delete the record in the database
		return parent::delete();
	} // fn delete


	/**
	 * @return int
	 */
	public function getObjectTypeId()
	{
		return $this->m_data['id'];
	} // fn getObjectTypeId


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['name'];
	} // fn getName


	/**
	 * Get the description ID which is an index into the Translations table.
	 *
	 * @return int
	 */
	function getDescriptionId()
	{
		return $this->m_data['fk_description_id'];
	} // fn getDescriptionId


	/**
	 * Get the description in the given language.
	 * This is a convenience function that wraps the Translation::GetPhrase() function.
	 *
	 * @param int $p_languageId
	 * @return string
	 */
	public function getDescription($p_languageId)
	{
		return Translation::GetPhrase($p_languageId, $this->m_data['fk_description_id']);
	} // fn getDescription


	/**
	 * Set the description in the given language.
	 *
	 * @param int $p_languageId
	 * @param string $p_text
	 */
	public function setDescription($p_languageId, $p_text)
	{
		Translation::SetPhrase($p_languageId, $this->m_data['fk_description_id'], $p_text);
	} // fn setDescription

} // class ObjectType

?>