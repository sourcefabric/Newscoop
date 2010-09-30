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
class Author extends DatabaseObject {
	var $m_dbTableName = 'Authors';
	var $m_keyColumnNames = array('id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('id', 'first_name', 'last_name', 'email','type','skype','jabber','aim','biography','image');
    var $m_aliases = null;

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
            $this->m_keyColumnNames = array('id');
		}
        if ($this->getId()>0)
        {
            $this->loadAliases();
        }
	} // constructor


	/**
     * Wrapper around DatabaseObject::setProperty
     * @see classes/DatabaseObject#setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql)
     */
    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        if ($p_dbColumnName == 'first_name' || $p_dbColumnName == 'last_name') {
            $this->m_keyColumnNames = array('first_name', 'last_name');
            $this->resetCache();
            $this->m_keyColumnNames = array('id');
        }
        return parent::setProperty($p_dbColumnName, $p_value);
    }


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
		$name = preg_replace(array('/%_FIRST_NAME/', '/%_LAST_NAME/'),
		                     array($this->getFirstName(), $this->getLastName()),
		                     $p_format);
        return trim($name);
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

    public function getType()
    {
        return $this->m_data['type'];
    }
    
    public function getSkype()
    {
        return $this->m_data['skype'];
    }
    
    public function getJabber()
    {
        return $this->m_data['jabber'];
    }
    public function getAim()
    {
        return $this->m_data['aim'];
    }
    
    
    public function getImage()
    {
        return $this->m_data['image'];
    }

    public static function ReadName($p_name)
    {
    	$p_name = trim($p_name);
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
        	if (count($matches) > 1) {
        		$lastName = array_pop($matches);
        		$firstName = implode(' ', $matches);
        	}
        	if (count($matches) == 1) {
        		$firstName = $matches[0];
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

    public function setType($p_value)
    {
        return $this->setProperty('type', $p_value);
    }
    
    public function setSkype($p_value)
    {
        return $this->setProperty('skype', $p_value);
    }
    
    public function setJabber($p_value)
    {
        return $this->setProperty('jabber', $p_value);
    }
    public function setAim($p_value)
    {
        return $this->setProperty('aim', $p_value);
    }
    
    public function setImage($p_value)
    {
        return $this->setProperty('image',$p_value);
    }
    
    protected function loadAliases()
    {
        global $g_ado_db;
        $sql = "SELECT alias FROM authorsaliases WHERE IdAuthor=" . $this->getId() . " order by id  ";
        $this->m_aliases = $g_ado_db->GetAll($sql); 
    }
    
    public function getAliases()
    {
        $this->loadAliases();
        return $this->m_aliases;
    }
    
    public function setBiography($p_value, $IdLanguage=1, $first_name="", $last_name="")
    {
        global $g_ado_db;
        $bio = $this->getBiography($IdLanguage);
        if (empty($bio))
        {
            $sql = "INSERT INTO authorbiography (IdAuthor,IdLanguage,biography,first_name,last_name) VALUES('%d','%d','%s','%s','%s')";
            $sql = sprintf($sql, $this->getId(), $IdLanguage, $p_value,$first_name,$last_name);
            $g_ado_db->Execute($sql);
        }
        else 
        {
            $sql = "UPDATE authorbiography SET biography='%s', first_name='%s', last_name='%s' WHERE IdAuthor='%d' AND IdLanguage='%d'";
            $sql = sprintf($sql, $p_value, $first_name, $last_name, $this->getId(), $IdLanguage);
            $g_ado_db->Execute($sql);
        }
        
    }
    public function getBiography($IdLanguage=1)
    {
        global $g_ado_db;
        $sql = "SELECT IdLanguage, biography, first_name, last_name FROM authorbiography WHERE IdAuthor=%d AND IdLanguage=%d";
        $sql = sprintf($sql, $this->getId(), $IdLanguage);
        return $g_ado_db->GetAll($sql);
    }
    
    public function getBiographies()
    {
        global $g_ado_db;
        $sql = "SELECT IdLanguage, biography, first_name, last_name FROM authorbiography WHERE IdAuthor=%d";
        $sql = sprintf($sql, $this->getId());
        return $g_ado_db->GetAll($sql);
    }

    public function setAliases($aliases)
    {
        global $g_ado_db;
        $sql = "DELETE FROM authorsaliases WHERE IdAuthor=" . $this->getId();
        $g_ado_db->Execute($sql);
        foreach ($aliases as $alias)
        {
            if (strlen($alias)>0)
            {
                $sql = "INSERT INTO authorsaliases(IdAuthor,alias) VALUES('%s','%s')";
                $sql = sprintf($sql, $this->getId(), $alias);
                $g_ado_db->Execute($sql);
            }
        }
    }
    
	public static function GetAllExistingNames()
	{
		global $g_ado_db;

		$sql = "SELECT DISTINCT Name FROM (\n"
             . "  SELECT Name FROM liveuser_users\n"
             . "  UNION\n"
             . "  SELECT TRIM(CONCAT(first_name, ' ', last_name)) AS Name\n"
             . "    FROM Authors\n"
             . ") AS names ORDER BY Name ASC";
        $authors = $g_ado_db->GetAll($sql);
        $convertArray = create_function('&$value, $key',
                                        '$value = $value["Name"];');
        array_walk($authors, $convertArray);
        return $authors;
	} // fn GetAllExistingNames


    public static function GetAuthors()
    {
        $tmpAuthor = new Author();
        $columns = implode(',', $tmpAuthor->getColumnNames(true));
        $queryStr = "SELECT $columns FROM Authors ORDER BY first_name";
        $authors = DbObjectArray::Create('Author', $queryStr);
        return $authors;
    } // fn GetAuthors

} // class Author

?>