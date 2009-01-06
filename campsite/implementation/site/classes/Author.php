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
class Author extends DatabaseObject {
	var $m_dbTableName = 'Authors';
	var $m_keyColumnNames = array('id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('id', 'first_name', 'last_name', 'email');

	/**
	 * Constructor.
	 * @param int $p_id
	 */
	public function __construct($p_idOrName = null)
	{
		global $g_ado_db;

		parent::DatabaseObject($this->m_columnNames);
		if (is_numeric($p_idOrName)) {
    		$this->m_data['id'] = $p_idOrName;
			$this->fetch();
		} elseif (!empty($p_idOrName)) {
			$names = Author::ReadName($p_idOrName);
			$this->m_keyColumnNames = array('first_name', 'last_name');
			$this->m_data['first_name'] = $names['first_name'];
            $this->m_data['last_name'] = $names['last_name'];
            $this->fetch();
		}
	} // constructor


	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->m_data['id'];
	} // fn getId


	/**
	 * @return string
	 */
	public function getName($p_format = '%_FIRST_NAME %_LAST_NAME')
	{
		return preg_replace(array('/%_FIRST_NAME/', '/%_LAST_NAME/'),
		                    array($this->getFirstName(), $this->getLastName()),
		                    $p_format);
	} // fn getName


    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->m_data['first_name'];
    } // fn getFirstName


    /**
     * @return string
     */
    public function getLastName()
    {
    	return $this->m_data['last_name'];
    } // fn getLastName

    
    public static function ReadName($p_name)
    {
    	$firstName = null;
    	$lastName = null;
        preg_match('/([^,]+),([^,]+)/', $p_name, $matches);
        if (count($matches) > 0) {
            $lastName = trim($matches[1]);
            $firstName = isset($matches[2]) ? trim($matches[2]) : '';
        } else {
        	preg_match_all('/[^\s]+/', $p_name, $matches);
        	if (isset($matches[0])) {
        		$matches = $matches[0];
        	}
        	if (count($matches) > 0) {
        		$lastName = array_pop($matches);
        		$firstName = implode(' ', $matches);
        	}
        }
        return array('first_name'=>$firstName, 'last_name'=>$lastName);
    }

    /**
	 *
	 */
	public function setName($p_name)
	{
		$names = Author::ReadName($p_name);
		return $this->setLastName($names['last_name'])
		&& $this->setFirstName($names['first_name']);
	} // fn setName


    /**
     *
     */
    public function setFirstName($p_name)
    {
        return $this->setProperty('first_name', $p_name);
    } // fn setFirstName


    /**
     *
     */
    public function setLastName($p_name)
    {
        return $this->setProperty('last_name', $p_name);
    } // fn setLastName


    /**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->m_data['email'];
	} // fn getEmail


	/**
	 * @param string $p_value
	 * @return boolean
	 */
	public function setEmail($p_value)
	{
		return $this->setProperty('email', $p_value);
	} // fn setEmail

} // class Author

?>