<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once('PEAR.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/CampCache.php');
require_once($g_documentRoot.'/classes/Exceptions.php');


/**
 * @package Campsite
 */
class DatabaseObject {
    /**
     * The name of the database table.
     * Redefine this in the subclass.
     * @var string
     */
    var $m_dbTableName = '';

    /**
     * The names of the columns in the database table.
     * Redefine this in the subclass.
     * @var array
     */
    var $m_columnNames = array();

    /**
     * The column names used for the key.
     * Redefine this in the subclass.
     * @var array
     */
    var $m_keyColumnNames = array();

    /**
     * Whether or not the primary key is an auto-increment field.
     * @var boolean
     */
    var $m_keyIsAutoIncrement = false;

    /**
     * An array of (ColumnName => Value).
     * @var array
     */
    var $m_data = array();

    /**
     * TRUE if the object exists in the database, FALSE if not, NULL if unknown.
     * @var boolean
     */
    var $m_exists = null;

    /**
     * If the key values of an object are changed, we need to remember the old
     * values in order to change to the new values.  This is the array of the
     * old values.
     * @var array
     */
    var $m_oldKeyValues = array();

    /**
     * If true it will use the caching feature
     *
     * @var bool
     */
    private static $m_useCache = true;

    /**
     * DatabaseObject represents a row in a database table.
     * This class is meant to be subclassed in order to implement a
     * specific table in the database.
     *
     * @param array $p_columnNames
     *        The column names of this table.  These are optional.
     *
     */
    function DatabaseObject($p_columnNames = null)
    {
        if (!is_null($p_columnNames)) {
            $this->setColumnNames($p_columnNames);
        }
    } // constructor


    /**
     * Return the column names used for the primary key.
     * @return array
     */
    function getKeyColumnNames() { return $this->m_keyColumnNames; }


    /**
     * Return the column names of this table.
     *
     * @param boolean $p_withTablePrefix
     *        Set to true if you want to prefix the column names with the table name.
     *        Default is false.
     *
     * @return array
     */
    function getColumnNames($p_withTablePrefix = false)
    {
        if (!$p_withTablePrefix) {
            return $this->m_columnNames;
        } else {
            $prefixNames = array();
            foreach ($this->m_columnNames as $columnName) {
                $prefixNames[] = $this->m_dbTableName.'.'.$columnName;
            }
            return $prefixNames;
        }
    } // fn getColumnNames


    /**
     * Initialize the column names for this object.
     * All column values will be initialized to null.
     *
     * @param array $p_columnNames
     *         The column names in the database.
     *
     * @return void
     */
    function setColumnNames($p_columnNames)
    {
        foreach ($p_columnNames as $columnName) {
            $this->m_data[$columnName] = null;
        }
    } // fn setColumnNames


    /**
     * Return the row as an array indexed by the column names.
     * @return array
     */
    function getData() { return $this->m_data; }


    /**
     * Return the name of the database table.
     * @return string
     */
    function getDbTableName() { return $this->m_dbTableName; }


    /**
     * Return the key as an array indexed by column names.
     * @return array
     */
    function getKey()
    {
        $key = array();
        foreach ($this->m_keyColumnNames as $columnName) {
            $key[$columnName] = $this->m_data[$columnName];
        }
        return $key;
    } // fn getKey


    /**
     * This function has two modes of operation:
     * You can change the columns used for the key values,
     * or you can change the key values themselves.
     * Changing key values is tricky because you have to remember the old
     * values in order to set the new values.
     *
     * @param array $p_columnNames
     *     <pre>
     *        Can be either:
     *        [0] => 'column name 1', [1] => 'column name 2', ...
     *        or:
     *        ['column name 1'] => 'value', ['column name 2'] => 'value',...
     *     </pre>
     * @return void
     */
    function setKey($p_columnNames)
    {
        if (is_array($p_columnNames)) {
            if (isset($p_columnNames[0])) {
                $this->m_keyColumnNames = array_values($p_columnNames);
            } else {
                $this->m_keyColumnNames = array_keys($p_columnNames);
                foreach ($this->m_keyColumnNames as $columnName) {
                    $this->modifyKeyValue($columnName, $p_columnNames[$columnName]);
                }
            }
        } elseif (is_string($p_columnNames)) {
            $this->m_keyColumnNames = array($p_columnNames);
        }
    } // fn setKey


    /**
     * Remember the old value of the key.
     * @param string $p_columnName
     * @param string $p_value
     * @return void
     */
    function modifyKeyValue($p_columnName, $p_value)
    {
        if (!isset($this->m_oldKeyValues[$p_columnName])) {
            $this->m_oldKeyValues[$p_columnName] = $this->m_data[$p_columnName];
        }
        $this->m_data[$p_columnName] = $p_value;
    } // fn modifyKeyValue


    /**
     * Fetch a single record from the database for the given key.
     *
     * @param array $p_recordSet
     *        If the record has already been fetched and we just need to
     *         assign the data to the object's internal member variable.
     *
     * @return boolean
     *        TRUE on success, FALSE on failure
     */
    function fetch($p_recordSet = null)
    {
        global $g_ado_db;

        if (is_null($p_recordSet)) {
            $object = $this->readFromCache();
            if ($object !== false) {
                return true;
            }

            $queryStr = 'SELECT ';
            $tmpColumnNames = array();
            foreach ($this->getColumnNames() as $columnName) {
                $tmpColumnNames[] = '`'.$columnName.'`';
            }
            $queryStr .= implode(', ', $tmpColumnNames);
            $queryStr .= ' FROM ' . $this->m_dbTableName;
            $queryStr .= ' WHERE ' . $this->getKeyWhereClause();
//            $queryStr .= ' LIMIT 1';
            $resultSet = $g_ado_db->GetRow($queryStr);
            if ($resultSet) {
                foreach ($this->getColumnNames() as $dbColumnName) {
                    $this->m_data[$dbColumnName] = $resultSet[$dbColumnName];
                }
                $this->m_exists = true;
            } else {
                $this->m_exists = false;
                return false;
            }
        } else {
            $object = $this->readFromCache($p_recordSet);
            if ($object !== false) {
                $this->m_exists = true;
                return true;
            }

            // We were given a pre-fetched recordset.
            $this->m_data =& $p_recordSet;
            // Make sure all columns have a value even if they arent in the dataset.
            foreach ($this->getColumnNames() as $dbColumnName) {
                if (!isset($p_recordSet[$dbColumnName])) {
                    $this->m_data[$dbColumnName] = null;
                }
            }
            if ($this->keyValuesExist()) {
                $queryStr = 'SELECT * FROM ' . $this->m_dbTableName
                            . ' WHERE ' . $this->getKeyWhereClause();
                if ($g_ado_db->GetRow($queryStr)) {
                    $this->m_exists = true;
                }
            }
        }
        // Reset old key values - we are now synced with the database.
        $this->m_oldKeyValues = array();

        // Write the object to cache
        $this->writeCache();

        return true;
    } // fn fetch


    /**
     * Return true if the object exists in the database.
     *
     * @return boolean
     */
    function exists()
    {
        global $g_ado_db;

        if (!is_null($this->m_exists)) {
            return $this->m_exists;
        }

        $queryStr = 'SELECT `'.$this->m_keyColumnNames[0].'`';
        $queryStr .= ' FROM ' . $this->m_dbTableName;
        $queryStr .= ' WHERE ' . $this->getKeyWhereClause();
//        $queryStr .= ' LIMIT 1';
        $resultSet = $g_ado_db->GetRow($queryStr);
        return (count($resultSet) > 0);
    } // fn exists


    /**
     * Return a string for the primary key of the table.
     *
     * @return string
     */
    function getKeyWhereClause()
    {
        $whereParts = array();
        foreach ($this->m_keyColumnNames as $columnName) {
            if (isset($this->m_oldKeyValues[$columnName])) {
                $whereParts[] = '`' . $columnName . "`='".mysql_real_escape_string($this->m_oldKeyValues[$columnName]) ."'";
            } else {
                $whereParts[] = '`' . $columnName . "`='"
                    .mysql_real_escape_string($this->m_data[$columnName]) ."'";
            }
        }
        return implode(' AND ', $whereParts);
    } // fn getKeyWhereClause


    /**
     * Return true if the object has all the values required
     * to fetch a unique record from the table.
     *
     * @return boolean
     */
    function keyValuesExist()
    {
        foreach ($this->m_keyColumnNames as $columnName) {
            if (!isset($this->m_data[$columnName]) /* || empty($this->m_data[$columnName]) */) {
                return false;
            }
        }
        return true;
    } // fn keyValuesExist


    /**
     * Create the record in the database for this object.
     * This will use the currently set index values for the key.
     * No other values are set unless they are passed in through
     * the $p_values parameter.
     *
     * @param array $p_values
     *        Extra values to be set at create time, in the form of:
     *        (DB Column Name) => (value)
     *
     * @return boolean
     *        TRUE if the record was added, false if not.
     */
    function create($p_values = null)
    {
        global $g_ado_db;

        $queryStr = 'INSERT IGNORE INTO ' . $this->m_dbTableName;

        // Make sure we have the key required to create the row.
        // If auto-increment is set, the database will create the key for us.
        $columns = array();
        if ($this->keyValuesExist()) {
            // If the key values exist, use those.
            $columns = $this->getKey();
        } elseif (!$this->m_keyIsAutoIncrement) {
            // We dont have the key values and
            // the key is not an auto-increment value,
            // so we cant create the row.
            return false;
        }

        // Check if any columns values in the class are already set.
        // If so, automatically set these values when we create the row.
        foreach ($this->m_columnNames as $columnName) {
            if (!empty($this->m_data[$columnName])) {
                $columns[$columnName] = "'".mysql_real_escape_string($this->m_data[$columnName])."'";
            }
        }

        // Optionally set some values when we create the row.
        // These override values that are preset in the class.
        if (!is_null($p_values)) {
            $parts = array();
            foreach ($p_values as $columnName => $value) {
                // Construct value string for the SET clause.
                $columns[$columnName] = "'".mysql_real_escape_string($value)."'";
                $this->m_data[$columnName] = $value;
            }
        }

        if (count($columns) > 0) {
            $queryStr .= '(`' . implode('`,`', array_keys($columns)) . '`)';
        } else {
            $queryStr .= '(' . implode('`,`', array_keys($columns)) . ')';
        }
        $queryStr .= ' VALUES ('.implode(',', array_values($columns)) .')';

        // Create the row.
        $g_ado_db->Execute($queryStr);
        $success = ($g_ado_db->Affected_Rows() > 0);
        $this->m_exists = $success;

        // Fetch the row ID if it is auto-increment
        if ($this->m_keyIsAutoIncrement) {
            // There should only be one key column because
            // its an auto-increment key.
            $this->m_data[$this->m_keyColumnNames[0]] =
                $g_ado_db->Insert_ID();
        }
        return $success;
    } // fn create


    /**
     * Delete the row from the database.
     *
     * @return boolean
     *        TRUE if the record was deleted, false if not.
     */
    function delete()
    {
        global $g_ado_db;

        $queryStr = 'DELETE FROM ' . $this->m_dbTableName
                    .' WHERE ' . $this->getKeyWhereClause()
                    .' LIMIT 1';
        $g_ado_db->Execute($queryStr);
        $wasDeleted = ($g_ado_db->Affected_Rows() > 0);
        // Always set "exists" to false because if a row wasnt
        // deleted it means it probably didnt exist in the first place.
        $this->m_exists = false;
        $this->m_data = array();
        return $wasDeleted;
    } // fn delete


    /**
     * Return the data stored in the given column.
     *
     * @param string $p_dbColumnName
     *
     * @param boolean $p_forceFetchFromDatabase
     *        Get the data from the database instead of cached value
     *        that is stored in the object.
     *
     * @return mixed
     *        Return a string if the property exists,
     *        NULL if the value doesnt exist,
     *      or a PEAR_Error if $p_forceFetchFromDatabase is TRUE
     *      and there was a problem fetching the data.
     */
    function getProperty($p_dbColumnName, $p_forceFetchFromDatabase = false)
    {
        global $g_ado_db;

        if (!in_array($p_dbColumnName, $this->m_columnNames)
                && !array_key_exists($p_dbColumnName, $this->m_data)) {
            throw new InvalidPropertyException(get_class($this), $p_dbColumnName);
        }

        if (isset($this->m_data[$p_dbColumnName])) {
            if ($p_forceFetchFromDatabase) {
                if ($this->keyValuesExist() && in_array($p_dbColumnName, $this->m_columnNames)) {
                    $queryStr = 'SELECT '.$p_dbColumnName
                                .' FROM '.$this->m_dbTableName
                                .' WHERE '.$this->getKeyWhereClause();
                    $this->m_data[$p_dbColumnName] = $g_ado_db->GetOne($queryStr);
                    // Special case for key values
                    if (in_array($p_dbColumnName, $this->m_oldKeyValues)) {
                        // Key value is now synced with database.
                        unset($this->m_oldKeyValues[$p_dbColumnName]);
                    }
                    // Remember that this row exists.
                    if ($this->m_data[$p_dbColumnName] !== false) {
                        $this->m_exists = true;
                    }
                } else {
                    if (!$this->keyValuesExist()) {
                        return new PEAR_Error('Key values do not exist - cannot fetch row.');
                    } else {
                        return new PEAR_Error('Column name does not exist - cannot fetch row.');
                    }
                }
            }
            return $this->m_data[$p_dbColumnName];
        }
        return null;
    } // fn getProperty


    /**
     * Set the given column name to the given value.
     * The object's internal variable will also be updated.
     * If the value hasnt changed, the database will not be updated.
     * Note: You cannot set $p_commit to FALSE and $p_isSql to TRUE
     * at the same time.
     *
     * @param string $p_dbColumnName
     *        The name of the column that is to be updated.
     *
     * @param string $p_value
     *        The value to set.
     *
     * @param boolean $p_commit
     *        If set to true, the value will be written to the database immediately.
     *        If set to false, the value will not be written to the database.
     *        Default is true.
     *
     * @param boolean $p_isSql
     *        Set this to TRUE if p_value consists of SQL commands.
     *        There is no way to know what the result of the command is,
     *        so we will need to refetch the value from the database in
     *        order to update the internal variable's value.
     *
     * @return boolean
     *        TRUE on success, FALSE on error.
     */
    function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        global $g_ado_db;

        // If we are not committing, the value cannot be SQL.
        if (!$p_commit && $p_isSql) {
            return false;
        }
        // Check that the value is a valid column name.
        if (!array_key_exists($p_dbColumnName, $this->m_data)) {
            if (in_array($p_dbColumnName, $this->m_columnNames)) {
                $this->m_data[$p_dbColumnName] = null;
            } else {
                return false;
            }
        }
        // If the value hasnt changed, dont update it.
        if ($p_value == $this->m_data[$p_dbColumnName]) {
            return true;
        }
        // If we dont have the key to this row, we cant update it.
        if ($p_commit && !$this->keyValuesExist()) {
            return false;
        }
        // Special case if we are modifying a key value -
        // we need to remember the old key value if we are going to commit
        // later on.
        if (!$p_commit && in_array($p_dbColumnName, $this->m_keyColumnNames)) {
            // Remember the old value so we can tell the database which row
            // we are changing.
            if (!isset($this->m_oldKeyValues[$p_dbColumnName])) {
                $this->m_oldKeyValues[$p_dbColumnName] = $this->m_data[$p_dbColumnName];
            }
        }
        $success = true;
        // Commit value to the database if requested.
        if ($p_commit) {
            $value = $p_value;
            if (!$p_isSql) {
                $value = "'".mysql_real_escape_string($p_value)."'";
            }
            $queryStr = 'UPDATE '.$this->m_dbTableName
                        .' SET `'. $p_dbColumnName.'`='.$value
                        .' WHERE '.$this->getKeyWhereClause()
                        .' LIMIT 1';
            $result = $g_ado_db->Execute($queryStr);
            $success = ($result !== false && $g_ado_db->Affected_Rows() >= 0);
            if ($result !== false) {
                $this->m_exists = true;
            }
        }
        // Store the value locally.
        if (!$p_isSql) {
            $this->m_data[$p_dbColumnName] = $p_value;
        } else {
            // Fetch the data from the database.  This is for the
            // case when the database execute some operation (e.g. 'DATE')
            // to create the new value.
            $queryStr = 'SELECT '.$p_dbColumnName
                        .' FROM '.$this->m_dbTableName
                        .' WHERE '.$this->getKeyWhereClause();
            $value = $g_ado_db->GetOne($queryStr);
            if ($value !== false) {
                $this->m_data[$p_dbColumnName] = $value;
                $this->m_exists = true;
            } else {
                $errorMsg = $g_ado_db->ErrorMsg();
            }
        }

        // Write the object to cache
        if ($success !== false) {
            $this->writeCache();
        }

        return $success;
    } // fn setProperty


    /**
     * Update the database row with the given values.
     *
     * @param array $p_columns
     *        Array of (Column_Name => Value)
     *
     * @param boolean $p_commit
     *        If set to true, the value will be written to the database immediately.
     *        If set to false, the value will not be written to the database.
     *        Default is true.
     *
     * @param boolean $p_isSql
     *        Set this to TRUE if the values of p_columns contains SQL commands.
     *        There is no way to know what the result of the command is,
     *        so we will need to refetch the row from the database in
     *        order to update the internal variable's value.
     *
     * @return boolean
     *        Return TRUE on success, FALSE on error.
     */
    function update($p_columns = null, $p_commit = true, $p_isSql = false)
    {
        global $g_ado_db;

        // Check input
        if (!is_array($p_columns)) {
            return false;
        }

        $setColumns = array();
        foreach ($p_columns as $columnName => $columnValue) {
            // Set the value only if the column name exists.
            if (array_key_exists($columnName, $this->m_data)) {
                // Special case if we are setting a key value -
                // if we are going to commit later, then we need to
                // remember the old key values.
                if (!$p_commit && in_array($columnName, $this->m_keyColumnNames)) {
                    // Remember the old value so we can tell the database which row
                    // we are changing.
                    if (!isset($this->m_oldKeyValues[$columnName])) {
                        $this->m_oldKeyValues[$columnName] = $this->m_data[$columnName];
                    }
                }
                // Only set the value if it is different from the
                // current value.
                if ($columnValue != $this->m_data[$columnName]) {
                    $setColumns[] = "`".$columnName . "`='". mysql_real_escape_string($columnValue) ."'";
                    if (!$p_isSql) {
                        $this->m_data[$columnName] = $columnValue;
                    }
                }
            }
        }
        $success = true;
        if ($p_commit && (count($setColumns) > 0)) {
            $queryStr = 'UPDATE ' . $this->m_dbTableName
                        .' SET '.implode(',', $setColumns)
                        .' WHERE ' . $this->getKeyWhereClause()
                        .' LIMIT 1';
            $success = $g_ado_db->Execute($queryStr);
            if ($success !== false) {
                $this->m_exists = true;
            }
        }
        if ($p_isSql && (count($setColumns) > 0)) {
            $this->fetch();
            $this->m_oldKeyValues = array();
        }

        // Write the object to cache
        if ($success !== false) {
            $this->writeCache();
        }

        return $success;
    } // fn update


    /**
     * Commit the data stored in memory to the database.
     * This is useful if you make a bunch of setProperty() calls at once
     * and you dont want to update the database every time.  Instead you
     * can set all the variables without committing them, then call this function.
     *
     * @param array $p_ignoreColumns
     *        Specify column names to ignore when doing the commit.
     *
     * @return boolean
     *        Return TRUE if the database was updated, false otherwise.
     */
    function commit($p_ignoreColumns = null)
    {
        global $g_ado_db;

        $setColumns = array();
        foreach ($this->m_data as $columnName => $columnValue) {
            if (is_null($p_ignoreColumns) || !in_array($columnName, $p_ignoreColumns)) {
                $setColumns[] = "`".$columnName . "`='". mysql_real_escape_string($columnValue) ."'";
            }
        }
        $queryStr = 'UPDATE ' . $this->m_dbTableName
                    .' SET '.implode(',', $setColumns)
                    .' WHERE ' . $this->getKeyWhereClause()
                    .' LIMIT 1';
        $result = $g_ado_db->Execute($queryStr);
        $success = ($g_ado_db->Affected_Rows() >= 0);
        if ($result !== false) {
            $this->m_exists = true;
        }

        // Write the object to cache
        if ($success !== false) {
            $this->writeCache();
        }

        return $success;
    } // fn commit


    /**
     * Do a simple search.
     *
     * @param array $p_columns
     *        Array of arrays of two strings: column name and search value.
     * @param array $p_sqlOptions
     *        See ProcessOptions().
     *
     * @return array
     */
    function Search($p_className, $p_columns = null, $p_sqlOptions = null)
    {
        if (!class_exists($p_className)) {
            return array();
        }

        $tmpObj =& new $p_className;
        $queryStr = "SELECT * FROM ".$tmpObj->m_dbTableName;
        if (is_array($p_columns) && (count($p_columns) > 0)) {
            $contraints = array();
            foreach ($p_columns as $item) {
                if (count($item) == 2) {
                    list($columnName, $value) = $item;
                    $contraints[] = "`$columnName`='$value'";
                }
            }
            $queryStr .= " WHERE ".implode(" AND ", $contraints);
        }
        $queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
        $dbObjects = DbObjectArray::Create($p_className, $queryStr);
        return $dbObjects;
    } // fn search


    /**
     * Output the raw values of this object so that it displays nice in HTML.
     * @return void
     */
    function dumpToHtml()
    {
        echo "<pre>";
        print_r($this->m_data);
        echo "</pre>";
    } // fn dumpToHtml


    /**
     * This is used by subclasses to add extra SQL options to the end of a query.
     *
     * @param string $p_queryStr
     *        The current SQL query.
     *
     * @param array $p_sqlOptions
     *     <pre>
     *        Available options:
     *        'LIMIT' => [max_rows_to_fetch]
     *        'LIMIT' => array('START'=>[Starting_index],'MAX_ROWS'=>[Max_rows_to_fetch]
     *        'ORDER BY' => [column_name]
     *        'ORDER BY' => array([column_name_1], [column_name_2], ...)
     *        'ORDER BY' => array([column_name_1]=>[ASC|DESC], [column_name_2]=>[ASC|DESC], ...)
     *     </pre>
     *
     * @return string
     *        Original SQL query with the options appended at the end.
     */
    function ProcessOptions($p_queryStr, $p_sqlOptions)
    {
        if (!is_null($p_sqlOptions)) {
            if (isset($p_sqlOptions['ORDER BY'])) {
                if (!is_array($p_sqlOptions['ORDER BY'])) {
                    $p_queryStr .= ' ORDER BY '.$p_sqlOptions['ORDER BY'];
                } else {
                    $p_queryStr .= ' ORDER BY ';
                    $tmpItems = array();
                    foreach ($p_sqlOptions['ORDER BY'] as $key => $orderItem) {
                        // We assume here that the column name is not numeric
                        if (is_numeric($key)) {
                            // Not using the ASC/DESC option
                            $tmpItems[] = '`'.$orderItem.'`';
                        } else {
                            $orderItem = strtoupper($orderItem);
                            if (($orderItem == 'ASC') || ($orderItem == 'DESC')) {
                                // Using the ASC/DESC option
                                $tmpItems[] = '`'.$key.'` '.$orderItem;
                            }
                        }
                    }
                    $p_queryStr .= implode(',', $tmpItems);
                }
            }
            if (isset($p_sqlOptions['LIMIT'])) {
                if (is_array($p_sqlOptions['LIMIT'])) {
                    $p_queryStr .= ' LIMIT '.$p_sqlOptions['LIMIT']['START']
                        .','.$p_sqlOptions['LIMIT']['MAX_ROWS'];
                } else {
                    $p_queryStr .= ' LIMIT '.$p_sqlOptions['LIMIT'];
                }
            }
        }
        return $p_queryStr;
    } // fn ProcessOptions


    /**
     * Initializes the current object from cache if it exists
     *
     * @param array $p_recordSet
     *
     * @return mixed
     *    object The cached object on success
     *    boolean FALSE if the object did not exist
     */
    function readFromCache($p_recordSet = null)
    {
        if (!DatabaseObject::GetUseCache()) {
            return false;
        }

        if (is_array($p_recordSet) && sizeof($p_recordSet) > 0) {
            foreach ($this->m_keyColumnNames as $columnName) {
                if (!isset($p_recordSet[$columnName])) {
                    return false;
                }
            }
            $cacheKey = $this->getCacheKey($p_recordSet);
        } else {
            if (!$this->keyValuesExist()) {
                return false;
            }
            $cacheKey = $this->getCacheKey();
        }

        $cacheObj = CampCache::singleton();
        $object = $cacheObj->fetch($cacheKey);

        if ($object === false) {
            return false;
        }

        $this->duplicateObject($object);

        return $this;
    }


    /**
     * Copies the given object
     *
     * @param object $p_source
     * @return object
     */
    function duplicateObject($p_source)
    {
        foreach ($p_source as $key=>$value) {
            $this->$key = $value;
        }

        return $this;
    }


    /**
     * Returns true if cache use was enabled
     *
     * @return bool
     */
    function GetUseCache()
    {
        return DatabaseObject::$m_useCache;
    }


    /**
     * Sets cache enabled/disabled
     *
     * @param bool $p_useCache
     *
     * @return void
     */
    function SetUseCache($p_useCache)
    {
        DatabaseObject::$m_useCache = $p_useCache;
    }


    /**
     * Writes the object to cache.
     *
     * @return bool
     *    TRUE on success, FALSE on failure
     */
    function writeCache()
    {
        if (!DatabaseObject::GetUseCache()) {
            return false;
        }

        if (!$this->exists()) {
            return false;
        }

        $cacheKey = $this->getCacheKey();
        $cacheObj = CampCache::singleton();

        return $cacheObj->add($cacheKey, $this);
    } // fn writeCache


    /**
     * Generates the cache key for the object.
     *
     * @param array optional
     *    $p_recordSet The object data
     */
    function getCacheKey($p_recordSet = null)
    {
        if (is_array($p_recordSet)) {
            $recordSet =& $p_recordSet;
        } else {
            $recordSet =& $this->m_data;
        }

        $cacheKey = '';
        foreach ($this->m_keyColumnNames as $key) {
            if (!isset($recordSet[$key])) {
                return false;
            }
            $cacheKey .= $recordSet[$key];
        }

        return $cacheKey.get_class($this);
    } // fn getCacheKey

} // class DatabaseObject

?>