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
class AuthorAlias extends DatabaseObject
{
    var $m_dbTableName = 'AuthorAliases';
    var $m_keyColumnNames = array('id');
    var $m_keyIsAutoIncrement = true;
    var $m_columnNames = array('id', 'fk_author_id', 'alias');

    /**
     * Constructor.
     *
     * @param int $p_idOrName
     */
    public function __construct($p_idOrName = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        if (is_numeric($p_idOrName)) {
            $this->m_data['id'] = $p_idOrName;
            $this->fetch();
        } elseif (!empty($p_idOrName)) {
            $this->m_keyColumnNames = array('alias');
            $this->m_data['alias'] = $p_idOrName;
            $this->fetch();
            $this->m_keyColumnNames = array('id');
        }
    } // fn constructor


    /**
     * Wrapper around DatabaseObject::setProperty
     *
     * @see classes/DatabaseObject#setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql)
     */
    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        if ($p_dbColumnName == 'alias') {
            $this->m_keyColumnNames = array('alias');
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
     * @return int
     */
    public function getAuthorId()
    {
        return $this->m_data['fk_author_id'];
    } // fn getAuthorId


    /**
     * @return string
     */
    public function getName()
    {
        return $this->m_data['alias'];
    } // fn getName


    /**
     * @param int $p_value
     * @return boolean
     */
    public function setAuthorId($p_value, $p_commit = true)
    {
        return parent::setProperty('fk_author_id', $p_value, $p_commit);
    } // fn setAuthorId


    /**
     *
     */
    public function setName($p_name)
    {
        return $this->setProperty('alias', $p_name);
    } // fn setName


    /**
     * Get all the author aliases that match the given criteria.
     *
     * @param int $p_id
     * @param int $p_authorId
     * @param string $p_name
     * @return array
     */
    public static function GetAuthorAliases($p_id = null, $p_authorId = null, $p_name = null)
    {
        $constraints = array();
        if (!is_null($p_authorId)) {
            $constraints[] = array("fk_author_id", $p_authorId);
        }
        if (!is_null($p_name)) {
            $constraints[] = array("alias", $p_name);
        }
        if (!is_null($p_id)) {
            $constraints[] = array("id", $p_id);
        }
        return DatabaseObject::Search('AuthorAlias', $constraints);
    } // fn GetAuthorAliases


    /**
     * Remove alias pointers for the given author.
     *
     * @param int $p_authorId
     * @return void
     */
    public static function OnAuthorDelete($p_authorId)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM AuthorAliases WHERE fk_author_id = $p_authorId";
        $g_ado_db->Execute($queryStr);
    } // fn OnAuthorDelete

    public static function BuildAuthorIdsQuery(array $p_aliases) {
        $authors_query = false;
        $author_aliases = array();

        foreach ($p_aliases as $one_alias) {
            $one_alias = str_replace('"', '""', trim($one_alias));
            if (0 < strlen($one_alias)) {
                $author_aliases[] = $one_alias;
            }
        }

        if (0 < count($author_aliases)) {
            $aliases_str = '"' . implode('", "', $author_aliases) . '"';
            $authors_query = "SELECT DISTINCT id FROM AuthorAliases WHERE trim(alias) IN ($aliases_str)";

        }

        return $authors_query;
    } // fn BuildAuthorIdsQuery

} // class AuthorAlias

?>
