<?php
/**
 * @package Campsite
 */

require_once 'PEAR.php';
require_once dirname(__FILE__) . '/../include/campsite_constants.php';
require_once dirname(__FILE__) . '/DbObjectArray.php';
require_once dirname(__FILE__) . '/CampCache.php';
require_once dirname(__FILE__) . '/Exceptions.php';
require_once dirname(__FILE__) . '/Log.php';

use \Newscoop\EventDispatcher\EventDispatcher;
use \Newscoop\EventDispatcher\Events\GenericEvent;

/**
 */
class DatabaseObject
{
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

    /** @var EventDispatcher */
    protected static $eventDispatcher = null;

    /** @var array */
    protected static $resourceNames = array();

	/**
	 * DatabaseObject represents a row in a database table.
	 * This class is meant to be subclassed in order to implement a
	 * specific table in the database.
	 *
	 * @param array $p_columnNames
	 *		The column names of this table.  These are optional.
	 *
	 */
	public function DatabaseObject($p_columnNames = null)
	{
	    if (!is_null($p_columnNames)) {
	    	$this->setColumnNames($p_columnNames);
	    }
	} // constructor


    /**
     * Returns true if the current object is the same type as the given
     * object then has the same value.
     * @param mix $p_otherObject
     * @return boolean
     */
	public function sameAs($p_otherObject)
	{
		if (get_class($this) != get_class($p_otherObject)
		|| $this->m_dbTableName != $p_otherObject->m_dbTableName) {
			return false;
		}
		if (!$this->m_exists && !$p_otherObject->m_exists) {
			return true;
		}
		foreach ($this->m_keyColumnNames as $keyColumnName) {
			if ($this->m_data[$keyColumnName] != $p_otherObject->m_data[$keyColumnName]) {
				return false;
			}
		}
		return true;
	}


	/**
	 * Return the column names used for the primary key.
	 * @return array
	 */
	public function getKeyColumnNames() { return $this->m_keyColumnNames; }


	/**
	 * Return the column names of this table.
	 *
	 * @param boolean $p_withTablePrefix
	 *		Set to true if you want to prefix the column names with the table name.
	 *		Default is false.
	 *
	 * @return array
	 */
	public function getColumnNames($p_withTablePrefix = false)
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
	 * 		The column names in the database.
	 *
	 * @return void
	 */
	public function setColumnNames($p_columnNames)
	{
		if (is_array($p_columnNames)) {
			$this->m_data = array_fill_keys($p_columnNames, null);
		}
	} // fn setColumnNames


	/**
	 * Return the row as an array indexed by the column names.
	 * @return array
	 */
	public function getData() { return $this->m_data; }


	/**
	 * Return the name of the database table.
	 * @return string
	 */
	public function getDbTableName() { return $this->m_dbTableName; }


	/**
	 * Return the key as an array indexed by column names.
	 * @return array
	 */
	public function getKey()
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
	 *		Can be either:
	 *		[0] => 'column name 1', [1] => 'column name 2', ...
	 *		or:
	 *		['column name 1'] => 'value', ['column name 2'] => 'value',...
	 *     </pre>
	 * @return void
	 */
	public function setKey($p_columnNames)
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
	public function modifyKeyValue($p_columnName, $p_value)
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
	 *		If the record has already been fetched and we just need to
	 * 		assign the data to the object's internal member variable.
	 * @param bool $p_forceExists
	 *		prevents to check for the object existence in the database,
	 * 		a performance thing for situations where we know it exists.
	 *
	 * @return boolean
	 *		TRUE on success, FALSE on failure
	 */
	public function fetch($p_recordSet = null, $p_forceExists = false)
	{
		$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

		if (is_null($p_recordSet)) {
			if ($this->readFromCache() !== false) {
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
			$resultSet = $g_ado_db->GetRow($queryStr);
			if ($resultSet) {
				foreach ($this->getColumnNames() as $dbColumnName) {
					$this->m_data[$dbColumnName] = $resultSet[$dbColumnName];
				}
				$this->m_exists = true;
			} else {
				$this->m_exists = false;
			}
		} else {
			if ($this->readFromCache($p_recordSet) !== false) {
				return true;
			}

		    // We were given a pre-fetched recordset.
		    $this->m_data = $p_recordSet;
		    // Make sure all columns have a value even if they arent in the dataset.
			foreach ($this->getColumnNames() as $dbColumnName) {
				if (!isset($p_recordSet[$dbColumnName])) {
					$this->m_data[$dbColumnName] = null;
                }
			}
            if ($p_forceExists) {
                $this->m_exists = true;
            } else {
				$this->m_exists = false;
				if ($this->keyValuesExist()) {
					$queryStr = 'SELECT * FROM ' . $this->m_dbTableName
								. ' WHERE ' . $this->getKeyWhereClause();
					if ($g_ado_db->GetRow($queryStr)) {
						$this->m_exists = true;
					}
				}
			}
		}
		// Reset old key values - we are now synced with the database.
		$this->m_oldKeyValues = array();

		if ($this->m_exists) {
		    // Write the object to cache
		    $this->writeCache();
		}

		return $this->m_exists;
	} // fn fetch


	/**
	 * Return true if the object exists in the database.
	 *
	 * @return boolean
	 */
	public function exists()
	{
		return !is_null($this->m_exists) && $this->m_exists;
	} // fn exists


	/**
	 * Return a string for the primary key of the table.
	 *
	 * @return string
	 */
	public function getKeyWhereClause()
	{
	    $g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

		$whereParts = array();
		foreach ($this->m_keyColumnNames as $columnName) {
            $value = isset($this->m_oldKeyValues[$columnName]) ? $this->m_oldKeyValues[$columnName]
                : $this->m_data[$columnName];
            $whereParts[] = $g_ado_db->escapeKeyVal($columnName, $value);
		}

		return implode(' AND ', $whereParts);
	} // fn getKeyWhereClause


	/**
	 * Return true if the object has all the values required
	 * to fetch a unique record from the table.
	 *
	 * @return boolean
	 */
	public function keyValuesExist($p_recordSet = null)
	{
        $recordSet = is_null($p_recordSet) ? $this->m_data : $p_recordSet;
	    foreach ($this->m_keyColumnNames as $columnName) {
			if (!isset($recordSet[$columnName])
			|| is_null($recordSet[$columnName])) {
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
	 *		Extra values to be set at create time, in the form of:
	 *		(DB Column Name) => (value)
	 *
	 * @return boolean
	 *		TRUE if the record was added, false if not.
	 */
	public function create($p_values = null)
	{
		$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

		$queryStr = 'INSERT IGNORE INTO ' . $this->m_dbTableName;

		// Make sure we have the key required to create the row.
		// If auto-increment is set, the database will create the key for us.
		$columns = array();
		if ($this->keyValuesExist()) {
			// If the key values exist, use those.
			$columns = $this->getKey();
			$autoGenerated = false;
		} elseif (!$this->m_keyIsAutoIncrement) {
			// We dont have the key values and
			// the key is not an auto-increment value,
			// so we cant create the row.
			return false;
		} else {
			$autoGenerated = true;
		}

		// Check if any columns values in the class are already set.
		// If so, automatically set these values when we create the row.
		foreach ($this->m_columnNames as $columnName) {
			if (!empty($this->m_data[$columnName])) {
				$columns[$columnName] = $g_ado_db->escape($this->m_data[$columnName]);
			}
		}

		// Optionally set some values when we create the row.
		// These override values that are preset in the class.
		if (!is_null($p_values)) {
			$parts = array();
			foreach ($p_values as $columnName => $value) {
				if (in_array($columnName, $this->m_columnNames)) {
					// Construct value string for the SET clause.
					$columns[$columnName] = $g_ado_db->escape($value);
					$this->m_data[$columnName] = $value;
				}
			}
		}

		if (count($columns) > 0) {
			$queryStr .= '(`' . implode('`,`', array_keys($columns)) . '`)';
		} else {
			$queryStr .= '(' . implode('`,`', array_keys($columns)) . ')';
		}
		$queryStr .= ' VALUES ('.implode(',', array_values($columns)) .')';

		// Create the row.
		$g_ado_db->executeUpdate($queryStr);
		$success = ($g_ado_db->Affected_Rows() > 0);
		$this->m_exists = $success;

		// Fetch the row ID if it is auto-increment
		if ($this->m_keyIsAutoIncrement && $autoGenerated) {
			// There should only be one key column because
			// its an auto-increment key.
			$this->m_data[$this->m_keyColumnNames[0]] =
				$g_ado_db->Insert_ID();
		}

        self::dispatchEvent("{$this->getResourceName()}.create", $this, array(
            'id' => $this->getKey(),
            'diff' => $this->m_data,
            'title' => method_exists($this, 'getName') ? $this->getName() : '',
        ));
		$this->resetCache();
		return $success;
	} // fn create


	/**
	 * Delete the row from the database.
	 *
	 * @return boolean
	 *		TRUE if the record was deleted, false if not.
	 */
	public function delete()
	{
		$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

		$queryStr = 'DELETE FROM ' . $this->m_dbTableName
					.' WHERE ' . $this->getKeyWhereClause()
					.' LIMIT 1';
		$g_ado_db->executeUpdate($queryStr);
		$wasDeleted = ($g_ado_db->Affected_Rows() > 0);

		// removes object from cache
		if (DatabaseObject::GetUseCache()) {
		    $cacheKey = $this->getCacheKey();
		    $cacheObj = CampCache::singleton();
		    $cacheObj->delete($cacheKey);
		}

        self::dispatchEvent("{$this->getResourceName()}.delete", $this, array(
            'id' => $this->getKey(),
            'diff' => $this->m_data,
            'title' => method_exists($this, 'getName') ? $this->getName() : '',
        ));

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
	 *		Get the data from the database instead of cached value
	 *		that is stored in the object.
	 *
	 * @return mixed
	 *		Return a string if the property exists,
	 *		NULL if the value doesnt exist,
	 *      or a PEAR_Error if $p_forceFetchFromDatabase is TRUE
	 *      and there was a problem fetching the data.
	 */
	public function getProperty($p_dbColumnName, $p_forceFetchFromDatabase = false)
	{
		$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

        if (in_array($p_dbColumnName, $this->m_columnNames) === false
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
	 *		The name of the column that is to be updated.
	 *
	 * @param string $p_value
	 *		The value to set.
	 *
	 * @param boolean $p_commit
	 *		If set to true, the value will be written to the database immediately.
	 *		If set to false, the value will not be written to the database.
	 *		Default is true.
	 *
	 * @param boolean $p_isSql
	 *		Set this to TRUE if p_value consists of SQL commands.
	 *		There is no way to know what the result of the command is,
	 *		so we will need to refetch the value from the database in
	 *		order to update the internal variable's value.
	 *
	 * @return boolean
	 *		TRUE on success, FALSE on error.
	 */
	public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
	{
		$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

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
		if ($p_value == $this->m_data[$p_dbColumnName] && !$p_isSql) {
			return true;
		}
		// If we don't have the key to this row, we can't update it.
		if ($p_commit && !$this->keyValuesExist()) {
			return false;
		}
		$isKeyColumn = in_array($p_dbColumnName, $this->m_keyColumnNames);
		// Special case if we are modifying a key value -
		// we need to remember the old key value if we are going to commit
		// later on.
		if (!$p_commit && $isKeyColumn) {
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
				$value = is_null($p_value) ? 'NULL' : $g_ado_db->escape($p_value);
			}
			$queryStr = 'UPDATE '.$this->m_dbTableName
						.' SET `'. $p_dbColumnName.'`='.$value
						.' WHERE '.$this->getKeyWhereClause()
						.' LIMIT 1';
			try {
				$result = $g_ado_db->executeUpdate($queryStr);
				$success = ($result !== false && $g_ado_db->Affected_Rows() >= 0);
			} catch (\Exception $e) {
				Log::Message("Exception thrown when executing update query " . $queryStr . " " . $e->getMessage());
				$success = false;
				$result = false;
			}

			if ($result !== false) {
				$this->m_exists = true;
				if ($isKeyColumn && DatabaseObject::GetUseCache()) {
					$cacheKey = $this->getCacheKey();
					$cacheObj = CampCache::singleton();
					$cacheObj->delete($cacheKey);
				}
			}
		}

        $diff = array($p_dbColumnName => array(
            $this->m_data[$p_dbColumnName],
        ));

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

        $diff[$p_dbColumnName][] = $this->m_data[$p_dbColumnName];
        self::dispatchEvent("{$this->getResourceName()}.update", $this, array(
            'id' => $this->getKey(),
            'diff' => $diff,
            'title' => method_exists($this, 'getName') ? $this->getName() : '',
        ));

        // Write the object to cache
        if ($success !== false && $p_commit) {
            $this->writeCache();
        }

		return $success;
	} // fn setProperty


	/**
	 * Update the database row with the given values.
	 *
	 * @param array $p_columns
	 *		Array of (Column_Name => Value)
	 *
	 * @param boolean $p_commit
	 *		If set to true, the value will be written to the database immediately.
	 *		If set to false, the value will not be written to the database.
	 *		Default is true.
	 *
	 * @param boolean $p_isSql
	 *		Set this to TRUE if the values of p_columns contains SQL commands.
	 *		There is no way to know what the result of the command is,
	 *		so we will need to refetch the row from the database in
	 *		order to update the internal variable's value.
	 *
	 * @return boolean
	 *		Return TRUE on success, FALSE on error.
	 */
	public function update($p_columns = null, $p_commit = true, $p_isSql = false)
	{
		$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

		// Check input
		if (!is_array($p_columns)) {
			return false;
		}

        $diff = array();

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
    	        	$setColumns[] = $g_ado_db->escapeKeyVal($columnName, $columnValue);
    	        	if (!$p_isSql) {
                        $diff[$columnName] = array($this->m_data[$columnName], $columnValue);
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
			try {
				$success = $g_ado_db->executeUpdate($queryStr);
			} catch (\Exception $e) {
				Log::Message("Exception thrown when executing update query " . $queryStr . " " . $e->getMessage());
				$success = false;
			}

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
            self::dispatchEvent("{$this->getResourceName()}.update", $this, array(
                'id' => $this->getKey(),
                'diff' => $diff,
                'title' => method_exists($this, 'getName') ? $this->getName() : '',
            ));
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
	 *		Specify column names to ignore when doing the commit.
	 *
	 * @return boolean
	 *		Return TRUE if the database was updated, false otherwise.
	 */
	public function commit($p_ignoreColumns = null)
	{
		$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

        $setColumns = array();
        foreach ($this->m_data as $columnName => $columnValue) {
        	$value = is_null($columnValue) ? 'NULL' : $g_ado_db->escape($columnValue);
        	if (is_null($p_ignoreColumns) || !in_array($columnName, $p_ignoreColumns)) {
        		$setColumns[] = $columnName.' = '.$value;
        	}
        }
		$queryStr = 'UPDATE ' . $this->m_dbTableName
	        		.' SET '.implode(',', $setColumns)
	        		.' WHERE ' . $this->getKeyWhereClause()
	        		.' LIMIT 1';
        $result = $g_ado_db->executeUpdate($queryStr);
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
	 *		Array of arrays of two strings: column name and search value.
	 * @param array $p_sqlOptions
	 *		See ProcessOptions().
	 *
	 * @return array
	 */
	public static function Search($p_className, $p_columns = null, $p_sqlOptions = null)
	{
        $g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

		if (!class_exists($p_className)) {
			return array();
		}

		$tmpObj = new $p_className;
		$queryStr = "SELECT * FROM ".$tmpObj->m_dbTableName;
		if (is_array($p_columns) && (count($p_columns) > 0)) {
			$contraints = array();
			foreach ($p_columns as $item) {
				if (count($item) == 2) {
					list($columnName, $value) = $item;
					$contraints[] = $g_ado_db->escapeKeyVal($columnName, $value);
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
	public function dumpToHtml()
	{
	    echo "<pre>";
	    print_r($this->m_data);
	    echo "</pre>";
	} // fn dumpToHtml


	/**
	 * This is used by subclasses to add extra SQL options to the end of a query.
	 *
	 * @param string $p_queryStr
	 *		The current SQL query.
	 *
	 * @param array $p_sqlOptions
	 *     <pre>
	 *		Available options:
	 *		'LIMIT' => [max_rows_to_fetch]
	 *		'LIMIT' => array('START'=>[Starting_index],'MAX_ROWS'=>[Max_rows_to_fetch]
	 *		'ORDER BY' => [column_name]
	 *		'ORDER BY' => array([column_name_1], [column_name_2], ...)
	 *		'ORDER BY' => array([column_name_1]=>[ASC|DESC], [column_name_2]=>[ASC|DESC], ...)
	 *     </pre>
	 *
	 * @return string
	 *		Original SQL query with the options appended at the end.
	 */
	public static function ProcessOptions($p_queryStr, $p_sqlOptions)
	{
		if (!is_null($p_sqlOptions)) {
            if (isset($p_sqlOptions['GROUP BY'])) {
                if (!is_array($p_sqlOptions['GROUP BY'])) {
                    $p_queryStr .= " \nGROUP BY ".$p_sqlOptions['GROUP BY'];
                } else {
                    $p_queryStr .= " \nGROUP BY ";
                    $tmpItems = array();
                    foreach ($p_sqlOptions['GROUP BY'] as $key => $orderItem) {
                        // We assume here that the column name is not numeric
                        if (is_numeric($key)) {
                            // Not using the ASC/DESC option
                            $tmpItems[] = $orderItem;
                        } else {
                            $orderItem = strtoupper($orderItem);
                            if (($orderItem == 'ASC') || ($orderItem == 'DESC')) {
                                // Using the ASC/DESC option
                                $tmpItems[] = $key.' '.$orderItem;
                            }
                        }
                    }
                    $p_queryStr .= implode(',', $tmpItems);
                }
            }
			if (isset($p_sqlOptions['HAVING'])) {
				if (!is_array($p_sqlOptions['HAVING'])) {
					$p_queryStr .= " \nHAVING ".$p_sqlOptions['HAVING'];
				} else {
					$p_queryStr .= " \nHAVING " . implode(', ', $p_sqlOptions['HAVING']);
				}
			}
            if (isset($p_sqlOptions['ORDER BY'])) {
				if (!is_array($p_sqlOptions['ORDER BY'])) {
					$p_queryStr .= " \nORDER BY ".$p_sqlOptions['ORDER BY'];
				} else {
					$p_queryStr .= " \nORDER BY ";
					$tmpItems = array();
					foreach ($p_sqlOptions['ORDER BY'] as $key => $orderItem) {
						// We assume here that the column name is not numeric
						if (is_numeric($key)) {
							// Not using the ASC/DESC option
							$tmpItems[] = $orderItem;
						} else {
							$orderItem = strtoupper($orderItem);
							if (($orderItem == 'ASC') || ($orderItem == 'DESC')) {
								// Using the ASC/DESC option
								$tmpItems[] = $key.' '.$orderItem;
							}
						}
					}
					$p_queryStr .= implode(',', $tmpItems);
				}
			}
			if (isset($p_sqlOptions['LIMIT'])) {
				if (is_array($p_sqlOptions['LIMIT'])) {
					$p_queryStr .= " \nLIMIT ".$p_sqlOptions['LIMIT']['START']
						.','.$p_sqlOptions['LIMIT']['MAX_ROWS'];
				} else {
					$p_queryStr .= " \nLIMIT ".$p_sqlOptions['LIMIT'];
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
	public function readFromCache($p_recordSet = null)
	{
		if (!DatabaseObject::GetUseCache()) {
			return false;
		}

        $cacheKey = '';
        if (!$this->keyValuesExist($p_recordSet)) {
            return false;
        }

        $cacheKey = $this->getCacheKey($p_recordSet);
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
	public function duplicateObject($p_source)
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
	public function GetUseCache()
	{
		return false;
	}


	/**
	 * Sets cache enabled/disabled
	 *
	 * @param bool $p_useCache
	 *
	 * @return void
	 */
	public function SetUseCache($p_useCache)
	{
		return false;
	}


	public function resetCache()
	{
        if (!DatabaseObject::GetUseCache() || !$this->m_exists) {
            return false;
        }

        $cacheKey = $this->getCacheKey();
        $cacheObj = CampCache::singleton();

        return $cacheObj->delete($cacheKey);
	}


	/**
	 * Writes the object to cache.
	 *
	 * @return bool
     *    TRUE on success, FALSE on failure
	 */
	public function writeCache()
	{
		if (!DatabaseObject::GetUseCache() || !$this->m_exists) {
			return false;
		}

        $cacheKey = $this->getCacheKey();
        if ($cacheKey === false) {
        	return false;
        }
        $cacheObj = CampCache::singleton();

        return $cacheObj->add($cacheKey, $this);
	} // fn writeCache


    /**
     * Generates the cache key for the object.
     *
     * @param array optional
     *    $p_recordSet The object data
     */
    public function getCacheKey($p_recordSet = null)
    {
		if (is_array($p_recordSet)) {
			$recordSet = $p_recordSet;
		} else {
			$recordSet = $this->m_data;
		}

		$cacheKey = '';
		foreach ($this->m_keyColumnNames as $key) {
			if (!isset($recordSet[$key]) || is_null($recordSet[$key])) {
				return false;
			}
			$cacheKey .= (strlen($cacheKey) < 1) ? '' : '_';
            $cacheKey .= strtolower($recordSet[$key]);
		}

        return $cacheKey.'_'.get_class($this);
    } // fn getCacheKey


    protected function lockTables(array $p_tables = array(), $p_write = true)
    {
    	$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

    	if (count($p_tables) == 0) {
    		return;
    	}
    	$mode = $p_write ? 'WRITE' : 'READ';
    	$lockQuery = 'LOCK TABLES ' . implode(', ', $p_tables) . " $mode";
    	return $g_ado_db->Execute($lockQuery);
    }


    protected function unlockTables()
    {
    	$g_ado_db = \Zend_Registry::get('container')->get('doctrine.adodb');

    	$unlockQuery = 'UNLOCK TABLES';
    	return $g_ado_db->Execute($unlockQuery);
    }

    /**
     * Set event dispatcher.
     *
     * @param EventDispatcher $dispatcher
     * @return void
     */
    public static function setEventDispatcher($dispatcher)
    {
        self::$eventDispatcher = $dispatcher;
    }

    /**
     * Dispatch event.
     *
     * @param string $event
     * @param string $subject
     * @param array $params
     */
    protected static function dispatchEvent($event, $subject, $params = array())
    {
        if (empty(self::$eventDispatcher)) {
            return;
        }

        self::$eventDispatcher->dispatch($event, new GenericEvent($subject, $params));
    }

    /**
     * Set resource names.
     *
     * @param array $names
     * @return void
     */
    public static function setResourceNames(array $names)
    {
        self::$resourceNames = $names;
    }

    /**
     * Get resource name.
     *
     * @return string
     */
    protected function getResourceName()
    {
        return isset(self::$resourceNames[$this->m_dbTableName]) ? self::$resourceNames[$this->m_dbTableName] : $this->m_dbTableName;
    }
}