<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

/**
 * @package Campsite
 */
class AuthorType extends DatabaseObject
{
    const TABLE = 'AuthorTypes';

    /**
     * @var string
     */
    public $m_dbTableName = self::TABLE;

    /**
     * @var array
     */
    public $m_keyColumnNames = array('id');

    /**
     * @var array
     */
    public $m_columnNames = array('id', 'type');

    /**
     * @var bool
     */
    public $m_keyIsAutoIncrement = true;

    /**
     * @var bool
     */
    public $m_exists = false;

    /**
     * Constructor
     *
     * @param int $p_authorTypeId
     *      (optional) The author type identifier
     * @return void
     */
    public function __construct($p_authorTypeId = null)
    {
        parent::__construct($this->m_columnNames);
        if (is_numeric($p_authorTypeId) && $p_authorTypeId > 0) {
            $this->m_data['id'] = $p_authorTypeId;
            if ($this->keyValuesExist()) {
                $this->fetch();
            }
        }
    }

    /**
     * @param string $p_name
     * @return bool
     */
    public function create($p_name = null)
    {
        if (empty($p_name)) {
            return false;
        }
        $columns['type'] = (string) $p_name;
        $result = parent::create($columns);
        return $result;
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
        ArticleAuthor::OnAuthorTypeDelete($this->getId());
        // Unlink authors
        AuthorAssignedType::OnAuthorTypeDelete($this->getId());
        // Delete this author type
        $authorType = $this->getName();
        $result = parent::delete();
        return $result;
    }

    /**
     * Get the id of the author type.
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->m_data['id'];
    }

    /**
     * Get the name of the author type.
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->m_data['type'];
    }

    /**
     * Get the name of the author type.
     *
     * @param string $p_value
     * @return boolean|null
     */
    public function setName($p_value)
    {
        if (!empty($p_value)) {
            return $this->setProperty('type', (string) $p_value);
        }
    }

    /**
     * Get all the author types.
     *
     * @return array
     *    An array of AuthorType objects.
     */
    public static function GetAuthorTypes()
    {
        global $g_ado_db;

        $queryStr = 'SELECT id
            FROM ' . self::TABLE . '
            ORDER BY id';
        $result = $g_ado_db->GetAll($queryStr);
        if (!$result) {
            return array();
        }

        $authorTypes = array();
        foreach ($result as $authorType) {
            $tmpAuthorType = new AuthorType($authorType['id']);
            $authorTypes[] = $tmpAuthorType;
        }
        return $authorTypes;
    }
}
