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
    const DEFAULT_TYPE = 'AUTHOR';
    const TABLE = 'Authors';

    public $m_dbTableName = self::TABLE;
    public $m_keyColumnNames = array('id');
    public $m_columnNames = array(
        'id',
        'first_name',
        'last_name',
        'email',
        'skype',
        'jabber',
        'aim',
        'image');
    public $m_keyIsAutoIncrement = TRUE;
    public $m_aliases = NULL;
    private $m_type = NULL;

    /**
     * Constructor.
     *
     * @param int|string $p_idOrName
     */
    public function __construct($p_idOrName = null, $p_type = null)
    {
        parent::__construct($this->m_columnNames);
        if (is_numeric($p_idOrName)) {
            $this->m_data['id'] = (int) $p_idOrName;
            $this->fetch();
        } elseif (!empty($p_idOrName)) {
            $name = self::ReadName((string) $p_idOrName);
            $this->m_keyColumnNames = array('first_name', 'last_name');
            $this->m_data['first_name'] = $name['first_name'];
            $this->m_data['last_name'] = $name['last_name'];
            $this->fetch();
            $this->m_keyColumnNames = array('id');
        }
        if ($this->exists()) {
            if (!is_null($p_type)) {
                $this->m_type = new AuthorType((int) $p_type);
            }
            $this->loadAliases();
        } else {
            $this->m_type = new AuthorType();
        }
    }

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

        // Save author data for logging purposes
        $tmpData = $this->m_data;
        // Delete row from Authors table.
        $result = parent::delete();

        return $result;
    }

    /**
     * Wrapper around DatabaseObject::setProperty
     * @see classes/DatabaseObject#setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql)
     * @param string $p_dbColumnName
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
        return (int) $this->m_data['id'];
    }

    /**
     * @return string
     */
    public function getName($p_format = '%_FIRST_NAME %_LAST_NAME')
    {
        $name = preg_replace(array('/%_FIRST_NAME/', '/%_LAST_NAME/'),
            array($this->getFirstName(), $this->getLastName()), $p_format);

        return trim($name);
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return (string) $this->m_data['first_name'];
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return (string) $this->m_data['last_name'];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        if (empty($this->m_aliases)) {
            $this->loadAliases();
        }

        return $this->m_aliases;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return (string) $this->m_data['email'];
    }

    /**
     * @return string
     */
    public function getSkype()
    {
        return (string) $this->m_data['skype'];
    }

    /**
     * @return string
     */
    public function getJabber()
    {
        return (string) $this->m_data['jabber'];
    }

    /**
     * @return string
     */
    public function getAim()
    {
        return (string) $this->m_data['aim'];
    }

    /**
     * @return int
     */
    public function getImage()
    {
        return (int) $this->m_data['image'];
    }

    public function getAuthorType()
    {
        return $this->m_type;
    }

    /**
     * TODO: return AuthorAssignedType::GetAuthorTypesByAuthor($this->getId());
     *
     * @return array
     */
    public function getType()
    {
        global $g_ado_db;

        $sql = 'SELECT fk_type_id
            FROM ' . AuthorAssignedType::TABLE . '
            WHERE fk_author_id = ' . $this->getId();

        return $g_ado_db->GetAll($sql);
    }

    /**
     * TODO: Will be replaced by self.getType()
     *
     * @return array
     */
    public function getTypeWithNames()
    {
        global $g_ado_db;

        $sql = 'SELECT fk_type_id, type
            FROM ' . AuthorAssignedType::TABLE . '
            JOIN AuthorTypes ON AuthorTypes.id = fk_type_id
            WHERE fk_author_id = ' . $this->getId();

        return $g_ado_db->GetAll($sql);
    }

    /**
     * @param  string $p_name
     * @return bool
     */
    public function setName($p_name)
    {
        $name = Author::ReadName($p_name);

        return $this->setLastName($name['last_name'])
            && $this->setFirstName($name['first_name']);
    }

    /**
     * @param  string $p_name
     * @return bool
     */
    public function setFirstName($p_name)
    {
        return $this->setProperty('first_name', $p_name);
    }

    /**
     * @param  string $p_name
     * @return bool
     */
    public function setLastName($p_name)
    {
        return $this->setProperty('last_name', $p_name);
    }

    /**
     * @param  string $p_value
     * @return bool
     */
    public function setEmail($p_value)
    {
        return $this->setProperty('email', $p_value);
    }

    /**
     * @param  int  $p_typeId
     * @return integer
     */
    public function setType($p_typeId = NULL)
    {
        if (is_null($p_typeId)) {
            $p_typeId = $this->__getDefaultType();
        }
        $assignedType = new AuthorAssignedType($this->getId(), (int) $p_typeId);
        if (!$assignedType->exists()) {
            $assignedType->create();
        }

        return (int) $assignedType->getAuthorTypeId();
    }

    private function __getDefaultType()
    {
        $types = AuthorType::GetAuthorTypes();
        foreach ((array) $types as $type) {
            if (strtoupper($type->getName()) === self::DEFAULT_TYPE) {
                return $type->getId();
            }
        }

        return (int) $types[0]->getId();
    }

    /**
     * @param  string $p_value
     * @return bool
     */
    public function setSkype($p_value)
    {
        return $this->setProperty('skype', $p_value);
    }

    /**
     * @param  string $p_value
     * @return bool
     */
    public function setJabber($p_value)
    {
        return $this->setProperty('jabber', $p_value);
    }

    /**
     * @param  string $p_value
     * @return bool
     */
    public function setAim($p_value)
    {
        return $this->setProperty('aim', $p_value);
    }

    /**
     * @param int $p_value
     *                     $return bool
     */
    public function setImage($p_value)
    {
        return $this->setProperty('image', (int) $p_value);
    }

    /**
     * @param  array $p_biography
     * @return false|null
     */
    public function setBiography(array $p_biography)
    {
        if (empty($p_biography) || !isset($p_biography['language'])) {
            return false;
        }

        $biographyObj = new AuthorBiography($this->getId(), $p_biography['language']);
        if (isset($p_biography['biography'])) {
            $biographyObj->setProperty('biography', $p_biography['biography'], false);
        }
        if (isset($p_biography['first_name']) && !empty($p_biography['first_name'])) {
            $biographyObj->setProperty('first_name', $p_biography['first_name'], false);
        }
        if (isset($p_biography['last_name']) && !empty($p_biography['last_name'])) {
            $biographyObj->setProperty('last_name', $p_biography['last_name'], false);
        }
        if ($biographyObj->exists()) {
            $biographyObj->commit();
        } else {
            $biographyObj->create();
        }
    }

    /**
     * @param array $p_aliases
     */
    public function setAliases(array $p_aliases)
    {
        if (empty($p_aliases)) {
            return false;
        }

        foreach ($p_aliases as $alias) {
            if (empty($alias)) {
                continue;
            }
            $aliasObj = new AuthorAlias($alias);
            if ($aliasObj->exists() === false) {
                $aliasObj->setAuthorId($this->getId(), false);
                $aliasObj->create();
            }
        }
    }

    /**
     * @return void
     */
    protected function loadAliases()
    {
        $this->m_aliases = AuthorAlias::GetAuthorAliases(null, $this->getId());
    }

    /**
     * @return array
     */
    public static function GetAllExistingNames()
    {
        global $g_ado_db;

        $queryStr = 'SELECT DISTINCT Name FROM ('
            . '  SELECT TRIM(CONCAT(Name, \' \', last_name)) AS Name FROM liveuser_users'
            . '  UNION'
            . '  SELECT TRIM(CONCAT(first_name, \' \', last_name)) AS Name'
            . '    FROM Authors'
            . ') AS names ORDER BY Name ASC';
        $authors = $g_ado_db->GetAll($queryStr);
        $convertArray = create_function('&$value, $key',
                                        '$value = $value["Name"];');
        array_walk($authors, $convertArray);

        return $authors;
    }

    /**
     * @return array
     */
    public static function GetAuthors()
    {
        $tmpAuthor = new Author();
        $columns = implode(',', $tmpAuthor->getColumnNames(true));
        $queryStr = "SELECT $columns
            FROM " . self::TABLE . '
            ORDER BY first_name';
        $authors = DbObjectArray::Create('Author', $queryStr);

        return $authors;
    }

    /**
     * @param  string $p_name
     * @return array
     */
    public static function ReadName($p_name)
    {
        $p_name = trim($p_name);
        $firstName = NULL;
        $lastName = NULL;
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

        return array('first_name' => $firstName, 'last_name' => $lastName);
    }
}
