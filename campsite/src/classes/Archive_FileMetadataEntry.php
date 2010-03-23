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
class Archive_FileMetadataEntry extends DatabaseObject
{
    var $m_keyColumnNames = array('id');
    var $m_keyIsAutoIncrement = true;
    var $m_dbTableName = 'Archive_FileMetadata';
    var $m_columnNames = array('id',
                               'gunid',
                               'predicate_ns',
                               'predicate',
                               'object');

    /**
     * Constructor
     *
     * @param int|array $p_data
     *      The file metadata entry id
     *      An array of metadata values
     */
    public function __construct($p_data = null)
    {
        if (is_null($p_data)) {
            return;
        }
        if (is_numeric($p_data)) {
            $this->m_data['id'] = $p_data;
            $this->fetch();
        }
        if (is_array($p_data)) {
            $this->fetch($p_data);
        }
    } // constructor


    /**
     * Retrieves the record for the file metadata entry
     *
     * @param array $p_recordSet
     *
     * @return boolean
     *      TRUE if the record exists, FALSE otherwise
     */
    public function fetch($p_recordSet = null)
    {
        global $g_ado_db;

        if (!is_null($p_recordSet) && is_array($p_recordSet)) {
            $this->m_data = $p_recordSet;
        }
        if (isset($this->m_data['id'])) {
            return parent::fetch();
        }
        if (!isset($this->m_data['gunid'])
	        || !isset($this->m_data['predicate_ns'])
                || !isset($this->m_data['predicate'])
	        || !isset($this->m_data['object'])) {
            return false;
        }
        $sql = 'SELECT * FROM `'.$g_ado_db->escape($this->m_dbTableName)."`"
	    ." WHERE gunid = '".$g_ado_db->escape($this->m_data['gunid'])."'"
	    ." AND predicate = '".$g_ado_db->escape($this->m_data['predicate'])."'";
        $resultSet = $g_ado_db->GetRow($sql);
        if ($resultSet) {
            $this->m_data['id'] = $resultSet['id'];
            $this->m_exists = true;
        } else {
            $this->m_exists = false;
        }
        return $this->m_exists;
    } // fn fetch


    /**
     * Deletes the file metadata entry
     *
     * @return boolean
     */
    public function delete()
    {
        if (!$this->exists()) {
            return false;
        }
        return parent::delete();
    } // fn delete


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
    public function getGunId()
    {
        return $this->m_data['gunid'];
    } // fn getGunId


    /**
     * @return string
     */
    public function getMetatag()
    {
        return $this->getMetatagNs().':'.$this->getMetatagName();
    } // fn getMetatag


    /**
     * @return string
     */
    public function getMetatagName()
    {
        return strtolower($this->m_data['predicate']);
    } // fn getMetatagName


    /**
     * @return string
     */
    public function getMetatagNs()
    {
        return strtolower($this->m_data['predicate_ns']);
    } // fn getMetatagNs


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->m_data['object'];
    } // fn getValue


    /**
     * @return boolean
     */
    public static function IsValidNamespace($p_metatag)
    {
        $metatag = strtolower($p_metatag);
        $namespace = strtok($metatag, ':');
        return in_array($namespace, array('dc','ls','dcterms'));
    } // fn IsValidNamespace


    /**
     * @return string
     */
    public static function GetTagNS($p_tag)
    {
        if (!Archive_FileMetadataEntry::IsValidNamespace($p_tag)) {
            return null;
        }
        return strtok(strtolower($p_tag), ':');
    } // fn GetTagNS


    /**
     * @return string
     */
    public static function GetTagName($p_tag)
    {
        $tok = strtok(strtolower($p_tag), ':');
        if ($tok !== false) {
            $tok = strtok(':');
        }
        return $tok;
    } // fn GetTagName

} // class Archive_FileMetadataEntry

?>