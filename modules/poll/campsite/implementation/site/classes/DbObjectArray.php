<?php

/**
 * @package Campsite
 */
class DbObjectArray {

    /**
     * Create an array of DatabaseObjects.
     *
     * @param string $p_className
     *        The type of objects to create.  The class
     *        must be a decendant of DatabaseObject and
     *        have a constructor that can take no parameters.
     *
     * @param string $p_queryStr
     *        The database query string that will fetch the
     *        rows from the database.
     *
     * @return array
     */
    function Create($p_className, $p_queryStr)
    {
        global $g_ado_db;
        $retval = array();
        $rows = $g_ado_db->GetAll($p_queryStr);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $tmpObj =& new $p_className();
                $tmpObj->fetch($row);
                $retval[] = $tmpObj;
            }
        }
        return $retval;
    } // fn Create


    /**
     * Given an array of DatabaseObjects, return one column
     * of the data.
     *
     * @param array $p_array
     * @param string $p_columnName
     * @return array
     */
    function GetColumn($p_array, $p_columnName)
    {
        $column = array();
        if (is_array($p_array)) {
            foreach ($p_array as $dbObject) {
                $column[] = $dbObject->m_data[$p_columnName];
            }
        }
        return $column;
    } // fn GetColumn


    /**
     * Given an array of DatabaseObjects, return the raw data
     * table as a two dimensional array.
     *
     * @param array $p_array
     * @return array
     */
    function GetTable($p_array)
    {
        $table = array();
        if (is_array($p_array)) {
            foreach ($p_array as $dbObject) {
                $table[] = $dbObject->m_data;
            }
        }
        return $table;
    } // fn GetTable


} // class DbObjectArray

?>