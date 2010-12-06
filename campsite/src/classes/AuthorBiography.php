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
class AuthorBiography extends DatabaseObject
{
    var $m_dbTableName = 'AuthorBiographies';
    var $m_keyColumnNames = array('fk_author_id', 'fk_language_id');
    var $m_columnNames = array('fk_author_id', 'fk_language_id',
                               'biography', 'first_name', 'last_name');

    /**
     * Constructor.
     *
     * @param int $p_authorId
     * @param int $p_languageId
     */
    public function __construct($p_authorId, $p_languageId)
    {
        if (is_numeric($p_authorId)) {
            $this->m_data['fk_author_id'] = $p_authorId;
        }
        if (is_numeric($p_languageId)) {
            $this->m_data['fk_language_id'] = $p_languageId;
        }
        if (!is_null($p_authorId) && !is_null($p_languageId)) {
            $this->fetch();
        }
    } // fn constructor


    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->m_data['fk_author_id'];
    } // fn getAuthorId


    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->m_data['fk_language_id'];
    } // fn getLanguageId


    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->m_data['biography'];
    } // fn getBiography


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
     * Get the author biography.
     * Biography is returned in the given language if exists.
     * If not any specific language is given it returns all the available translations.
     *
     * @param int $p_authorId
     * @param string $p_languageId
     * @return array
     */
    public static function GetBiographies($p_authorId, $p_languageId = null)
    {
        $constraints = array();
        if (!is_null($p_authorId)) {
            $constraints[] = array("fk_author_id", $p_authorId);
        }
        if (!is_null($p_languageId)) {
            $constraints[] = array("fk_language_id", $p_languageId);
        }
        return DatabaseObject::Search('AuthorBiographies', $constraints);
    } // fn GetBiographies


    /**
     * Remove author biography pointers for the given author.
     *
     * @param int $p_authorId
     * @return void
     */
    public static function OnAuthorDelete($p_authorId)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM AuthorBiographies WHERE fk_author_id = $p_authorId";
        $g_ado_db->Execute($queryStr);
    } // fn OnAuthorDelete

} // class AuthorBiography

?>