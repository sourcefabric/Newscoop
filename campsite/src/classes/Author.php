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
class Author extends DatabaseObject
{
    var $m_dbTableName = 'Authors';
    var $m_keyColumnNames = array('id');
    var $m_columnNames = array(
        'id',
        'first_name',
        'last_name',
        'email',
        'type',
        'skype',
        'jabber',
        'aim',
        'biography',
        'image');
    var $m_keyIsAutoIncrement = true;
    var $m_aliases = null;
    

	/**
	 * Constructor.
	 *
	 * @param int $p_idOrName
	 */
    public function __construct($p_idOrName = null)
    {
        global $g_ado_db;

        parent::DatabaseObject($this->m_columnNames);
        if (is_numeric($p_idOrName)) {
            $this->m_data['id'] = $p_idOrName;
            $this->fetch();
        } elseif (!empty($p_idOrName)) {
            $names = self::ReadName($p_idOrName);
            $this->m_keyColumnNames = array('first_name', 'last_name');
            $this->m_data['first_name'] = $names['first_name'];
            $this->m_data['last_name'] = $names['last_name'];
            $this->fetch();
            $this->m_keyColumnNames = array('id');
        }
        if ($this->exists()) {
            $this->loadAliases();
        }
    } // fn constructor


    /**
     * @return boolean
     */
    public function delete()
    {
        if (!$this->exists()) {
            return false;
        }

        // Unlink articles
        ArticleAuthor::OnAuthorDelete($this->getId());
        // Unlink aliases
        AuthorAlias::OnAuthorDelete($this->getId());
        // Unlink authors
        AuthorAssignedType::OnAuthorDelete($this->getId());
        // Unlink biographies
        AuthorBiography::OnAuthorDelete($this->getId());

        // Save author data temporarly so that it can still be used after
        // deleting for logging purposes
        $tmpData = $this->m_data;
        // Delete row from Authors table.
        $result = parent::delete();
        if ($result) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('Author #$1 "$2" deleted.',
                $tmpData['id'], $tmpData['first_name'] . ' ' . $tmpData['last_name']);
            Log::Message($logtext, null, 174);
        }

        return $result;
    } // fn delete


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
    } // fn setProperty


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
            array($this->getFirstName(), $this->getLastName()), $p_format);
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


    /**
     */
    public function getType()
    {
        //return AuthorAssignedTypes::GetAuthorTypesByAuthor($this->getId());
        global $g_ado_db;
        $sql = "SELECT fk_type_id FROM `AuthorAssignedTypes` WHERE fk_author_id=" . $this->getId();
        return $g_ado_db->GetAll($sql);
    }


    /**
     */
    public function getTypeWithNames()
    {
        global $g_ado_db;
        $sql = "SELECT fk_type_id, type FROM `AuthorAssignedTypes` JOIN `AuthorTypes` ON `AuthorTypes`.`id` = fk_type_id WHERE fk_author_id = " . $this->getId();
        return $g_ado_db->GetAll($sql);
    }


    public static function RemoveAuthorType($p_id)
    {
        global $g_ado_db;
        $sql = "DELETE FROM `AuthorsTypes` WHERE id =%d";
        $sql = sprintf($sql, $p_id);
        $sql2 = "DELETE FROM `AuthorsAuthorsTypes` WHERE fk_type_id=%d";
        $sql2 = sprintf($sql2, $p_id);
        $g_ado_db->Execute($sql);
        $g_ado_db->Execute($sql2);
    }


    /**
     * @return string
     */
    public function getSkype()
    {
        return $this->m_data['skype'];
    }


    /**
     * @return string
     */
    public function getJabber()
    {
        return $this->m_data['jabber'];
    }


    /**
     * @return string
     */
    public function getAim()
    {
        return $this->m_data['aim'];
    }


    public function getImage()
    {
        return $this->m_data['image'];
    }


    /**
     * @return array
     */
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


    public function setType($p_typeId)
    {
        $authorType = new AuthorAssignedType($this->getId(), $p_typeId);
        if (!$authorType->exists()) {
            $authorType->create();
        }

        //global $g_ado_db;
        //$types = $this->getType();
        //if (is_array($types) && in_array(array('fk_type_id'=>$p_value),$types)) return;
        //$sql = "INSERT INTO `AuthorsAuthorsTypes` (fk_author_id, fk_type_id) VALUES (%d,%d)";
        //$sql = sprintf($sql, $this->getId(), $p_value);
        //$g_ado_db->Execute($sql);
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
        $this->m_aliases = AuthorAlias::GetAuthorAliases(null, $this->getId());
    }


    public function getAliases()
    {
        if (empty($this->m_aliases)) {
            $this->loadAliases();
        }
        return $this->m_aliases;
    } // fn getAliases


    /**
     * @param array $p_authorBiography
     */
    public function setBiography(array $p_authorBiography)
    {
        if (empty($p_authorBiography) || !isset($p_authorBiography['language'])) {
            return false;
        }

        $authorBiographyObj = new AuthorBiography($this->getId(), $p_authorBiography['language']);
        $authorBiographyObj->setProperty('biography', $p_authorBiography['biography'], false);
        $authorBiographyObj->setProperty('first_name', $p_authorBiography['first_name'], false);
        $authorBiographyObj->setProperty('last_name', $p_authorBiography['last_name'], false);
        if ($authorBiographyObj->exists()) {
            $authorBiographyObj->commit();
        } else {
            $authorBiographyObj->create();
        }
    } // fn setBiography


    public function getBiographies()
    {
        global $g_ado_db;
        $sql = "SELECT IdLanguage, biography, first_name, last_name FROM Authorbiography WHERE IdAuthor=%d";
        $sql = sprintf($sql, $this->getId());
        return $g_ado_db->GetAll($sql);
    } // fn getBiographies


    /**
     * @param array $p_aliases
     */
    public function setAliases(array $p_aliases)
    {
        if (empty($p_aliases)) {
            return false;
        }

        foreach ($p_aliases as $alias) {
            // ignore empty entries
            if (empty($alias)) {
                continue;
            }
            $aliasObj = new AuthorAlias($alias);
            if ($aliasObj->exists() === false) {
                $aliasObj->setAuthorId($this->getId(), false);
                $aliasObj->create();
            }
        }
    } // fn setAliases


    /**
     * @return array
     */
    public static function GetAllExistingNames()
    {
        global $g_ado_db;

        $queryStr = 'SELECT DISTINCT Name FROM ('
            . '  SELECT Name FROM liveuser_users'
            . '  UNION'
            . '  SELECT TRIM(CONCAT(first_name, \' \', last_name)) AS Name'
            . '    FROM Authors'
            . ') AS names ORDER BY Name ASC';
        $authors = $g_ado_db->GetAll($queryStr);
        $convertArray = create_function('&$value, $key',
                                        '$value = $value["Name"];');
        array_walk($authors, $convertArray);
        return $authors;
    } // fn GetAllExistingNames


    /**
     * @return array
     */
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