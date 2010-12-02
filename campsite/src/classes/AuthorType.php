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
    var $m_dbTableName = 'AuthorTypes';
    var $m_keyColumnNames = array('id');
    var $m_keyIsAutoIncrement = true;
    var $m_columnNames = array('id', 'type');
    var $m_exists = false;


    /**
     * Constructor
     *
     * @param string
     *    $p_authorTypeId (optional) The author type identifier
     *
     * @return void
     */
    public function __construct($p_authorTypeId = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        if (is_numeric($p_authorTypeId) && $p_authorTypeId > 0) {
            $this->m_data['id'] = $p_authorTypeId;
            if ($this->keyValuesExist()) {
                $this->fetch();
            }
        }
    } // fn constructor


    /**
     * @param string
     *    $p_name
     *
     * @return boolean
     *    TRUE on success, FALSE on failure
     */
    public function create($p_name)
    {
        if (empty($p_name)) {
            return false;
        }
        $columns['type'] = $p_name;
        $result = parent::create($columns);
        if ($result) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('Author type "$1" created.', $p_name);
            Log::Message($logtext, null, 175);
        }
        return $result;
    } // fn create


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
        if ($result) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('Article type "$1" deleted.', $authorType);
            Log::Message($logtext, null, 176);
        }
        return $result;
    } // fn delete


    /**
     * Get the id of the author type.
     *
     * @return int
     */
    public function getId()
    {
        return $this->m_data['id'];
    } // fn getId


    /**
     * Get the name of the author type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->m_data['type'];
    } // fn getName


    /**
     * Get the name of the author type.
     *
     * @return string
     */
    public function setName($p_value)
    {
        if (!empty($p_value)) {
            return $this->setProperty('type', $p_value);
        }
    } // fn setName


    /**
     * Get all the author types.
     *
     * @return array
     *    An array of AuthorType objects.
     */
    public static function GetAuthorTypes()
    {
        global $g_ado_db;

        $queryStr = 'SELECT id FROM AuthorTypes ORDER BY id';
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
    } // fn GetAuthorTypes

} // class AuthorType

?>