<?php
require_once('PEAR.php');

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
	 * DatabaseObject represents a row in a database table.
	 * This class is meant to be subclassed in order to implement a
	 * specific table in the database.
	 *
	 * @param array p_columnNames
	 *		The column names of this table.  These are optional.
	 *
	 */
	function DatabaseObject($p_columnNames = null) {
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
	 * @return array
	 */	
	function getColumnNames() { return $this->m_columnNames; }

	
	/**
	 * Initialize the column names for this object.
	 * All column values will be initialized to null.
	 *
	 * @param array p_columnNames
	 * 		The column names in the database.
	 *
	 * @return void
	 */
	function setColumnNames($p_columnNames) {
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
	function getKey() {
		$key = array();
		foreach ($this->m_keyColumnNames as $columnName) {
			$key[$columnName] = $this->m_data[$columnName];
		}
		return $key;
	} // fn getKey
	
	
	/**
	 * Set the key column names and optionally the values as well.
	 * @param array p_columnNames
	 *		Can be either:
	 *		[0] => 'column name 1', [1] => 'column name 2', ...
	 *		or:
	 *		['column name 1'] => 'value', ['column name 2'] => 'value',...
	 *
	 * @return void
	 */
	function setKey($p_columnNames) {
		if (isset($p_columnNames[0])) {
			$this->m_keyColumnNames = array_values($p_columnNames);
		}
		else {
			$this->m_keyColumnNames = array_keys($p_columnNames);
			foreach ($this->m_keyColumnNames as $columnName) {
				$this->m_data[$columnName] = $p_columnNames[$columnName];
			}
		}
	} // fn setKey
	
	/**
	 * Fetch a single record from the database for the given key.
	 *
	 * @param array p_recordSet
	 *		If the record has already been fetched and we just need to 
	 * 		assign the data to the object's internal member variable.
	 *
	 * @return boolean
	 *		TRUE on success, FALSE on failure
	 */
	function fetch($p_recordSet = null) {
		global $Campsite;
		
		if (is_null($p_recordSet)) {
			$queryStr = 'SELECT ';
			$tmpColumnNames = array();
			foreach ($this->getColumnNames() as $columnName) {
				$tmpColumnNames[] = '`'.$columnName.'`';
			}
			$queryStr .= implode(', ', $tmpColumnNames);
			$queryStr .= ' FROM ' . $this->m_dbTableName;
			$queryStr .= ' WHERE ' . $this->getKeyWhereClause();
			$queryStr .= ' LIMIT 1';
			$resultSet =& $Campsite['db']->GetRow($queryStr);
			if ($resultSet) {
				foreach ($this->getColumnNames() as $dbColumnName) {
					$this->m_data[$dbColumnName] = $resultSet[$dbColumnName];
				}
			}
			else {
				return false;
			}
		}
		else {
			foreach ($this->getColumnNames() as $dbColumnName) {
				if (isset($p_recordSet[$dbColumnName])) {
					$this->m_data[$dbColumnName] = $p_recordSet[$dbColumnName];
				}
				else {
					$this->m_data[$dbColumnName] = null;
				}
			}
		}
		return true;
	} // fn fetch
	
	
	/**
	 * Return true if the object exists in the database.
	 *
	 * @return boolean
	 */
	function exists() {
		global $Campsite;
		$queryStr = 'SELECT `'.$this->m_keyColumnNames[0].'`';
		$queryStr .= ' FROM ' . $this->m_dbTableName;
		$queryStr .= ' WHERE ' . $this->getKeyWhereClause();
		$queryStr .= ' LIMIT 1';
		$resultSet =& $Campsite['db']->GetRow($queryStr);
		return (count($resultSet) > 0);
	} // fn exists
	
	
	/**
	 * Return a string for the primary key of the table.
	 *
	 * @return string
	 */
	function getKeyWhereClause() {
		$whereParts = array();
		foreach ($this->m_keyColumnNames as $columnName) {
			$whereParts[] = '`' . $columnName . "`='". $this->m_data[$columnName] ."'";
		}
		return implode(' AND ', $whereParts);		
	} // fn getKeyWhereClause
	
	
	/**
	 * Return true if the object has all the values required
	 * to fetch a unique record from the table.
	 *
	 * @return boolean
	 */
	function keyValuesExist() {
		foreach ($this->m_keyColumnNames as $columnName) {
			if (!$this->m_data[$columnName]) {
				return false;
			}
		}
		return true;
	} // fn keyValuesExist
	
	
	/**
	 * Create the record in the database for this object.
	 *
	 * @param array p_values
	 *		Extra values to be set at create time, in the form of:
	 *		(DB Column Name) => (value)
	 *
	 * @return boolean
	 *		TRUE if the record was added, false if not.
	 */
	function create($p_values = null) {
		global $Campsite;
		$queryStr = 'INSERT IGNORE INTO ' . $this->m_dbTableName;
		
		// Make sure we have the key required to create the row.
		// If auto-increment is set, the database will create the key for us.
		$columnNames = array();
		$columnValues = array();
		if ($this->keyValuesExist()) {
			// If the key values exist, use those.
			$columnNames = array_keys($this->getKey());
			$columnValues = array_values($this->getKey());
		}
		elseif (!$this->m_keyIsAutoIncrement) {
			// We dont have the key values and
			// the key is not an auto-increment value,
			// so we cant create the row.
			return false;			
		}
		
		// Optionally set some values when we create the row.
		if (!is_null($p_values)) {
			$parts = array();
			foreach ($p_values as $columnName => $value) {
				// Construct value string for the SET clause.
				$columnNames[] = $columnName;
				$columnValues[] = "'".$value."'";
				$this->m_data[$columnName] = $value;
			}
		}

		$queryStr .= '(' . implode(',', $columnNames) . ')';
		$queryStr .= ' VALUES ('.implode(',', $columnValues) .')';

		// Create the row.
		$Campsite['db']->Execute($queryStr);
		$success = ($Campsite['db']->Affected_Rows() > 0);
		
		// Fetch the row ID if it is auto-increment
		if ($this->m_keyIsAutoIncrement) {
			// There should only be one key column because
			// its an auto-increment key.
			$this->m_data[$this->m_keyColumnNames[0]] = 
				$Campsite['db']->Insert_ID();
		}
		return $success;
	} // fn create
	
	
	/**
	 * Delete the row from the database.
	 *
	 * @return boolean
	 *		TRUE if the record was deleted, false if not.
	 */
	function delete() {
		global $Campsite;
		$queryStr = 'DELETE FROM ' . $this->m_dbTableName
					.' WHERE ' . $this->getKeyWhereClause()
					.' LIMIT 1';
		$Campsite['db']->Execute($queryStr);
		return ($Campsite['db']->Affected_Rows() > 0);
	} // fn delete


	/**
	 * Return the data stored in the given column.
	 *
	 * @param string p_dbColumnName
	 *
	 * @param boolean p_forceFetchFromDatabase
	 *		Get the data from the database instead of cached value 
	 *		that is stored in the object.
	 * 
	 * @return mixed
	 *		Return a string if the property exists,
	 *		return a PEAR_Error otherwise.
	 */
	function getProperty($p_dbColumnName, $p_forceFetchFromDatabase = false) {
		global $Campsite;
		if (array_key_exists($p_dbColumnName, $this->m_data)) {
			if ($p_forceFetchFromDatabase) {
				$queryStr = 'SELECT '.$p_dbColumnName
							.' FROM '.$this->m_dbTableName
							.' WHERE '.$this->getKeyWhereClause();
				$this->m_data[$p_dbColumnName] = $Campsite['db']->GetOne($queryStr);
			}
			return $this->m_data[$p_dbColumnName];
		}
		else {
			return new PEAR_Error('Property \'' . $p_dbColumnName . '\' does not exist.');
		}
	} // fn getProperty
	
	
	/**
	 * Set the given column name to the given value.
	 * The object's internal variable will also be updated.
	 *
	 * @param string p_dbColumnName
	 *		The name of the column that is to be updated.
	 *
	 * @param string p_value
	 *		The value to set.
	 *
	 * @param boolean p_commit
	 *		If set to true, the value will be written to the database immediately.
	 *		If set to false, the value will not be written to the database.
	 *		Default is true.
	 *
	 * @param boolean p_isSql
	 *		Set this to TRUE if p_value consists of SQL commands.
	 *		There is no way to know what the result of the command is,
	 *		so we will need to refetch the value from the database in
	 *		order to update the internal variable's value.
	 *
	 * @return boolean
	 *		TRUE if the database was changed, FALSE if it wasnt.
	 */
	function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false) {
		global $Campsite;
		// If we are not committing, the value cannot be SQL.
		if (!$p_commit && $p_isSql) {
			return false;
		}
		// Check that the value is a valid column name.
		if (!array_key_exists($p_dbColumnName, $this->m_data)) {
			return false;
		}
		// If the value hasnt changed, dont update it.
		if ($p_value == $this->m_data[$p_dbColumnName]) {
			return false;
		}
		// If we dont have the key to this row, we cant update it.
		if ($p_commit && !$this->keyValuesExist()) {
			return false;
		}
		// Commit value to the database if requested.
		$databaseChanged = false;
		if ($p_commit) {
			$value = $p_value;
			if (!$p_isSql) {
				$value = "'".$p_value."'";
			}
			$queryStr = 'UPDATE '.$this->m_dbTableName
						.' SET `'. $p_dbColumnName.'`='.$value
						.' WHERE '.$this->getKeyWhereClause()
						.' LIMIT 1';
			$Campsite['db']->Execute($queryStr);
			$databaseChanged = ($Campsite['db']->Affected_Rows() > 0);
		}
		// Store the value locally.
		if (!$p_isSql) {
			$this->m_data[$p_dbColumnName] = $p_value;
		}
		else {
			// Fetch the data from the database.  This is for the
			// case when the database execute some operation (e.g. 'DATE')
			// to create the new value.
			$queryStr = 'SELECT '.$p_dbColumnName
						.' FROM '.$this->m_dbTableName
						.' WHERE '.$this->getKeyWhereClause();
			$value = $Campsite['db']->GetOne($queryStr);
			if ($value) {
				$this->m_data[$p_dbColumnName] = $value;
			}
			else {
				$errorMsg = $Campsite['db']->ErrorMsg();
			}
		}
		return $databaseChanged;
	} // fn setProperty
	
	
	/**
	 * Update the database row with the given values.
	 *
	 * @param array $p_columns
	 *		Array of (Column_Name => Value)
	 *
	 * @param boolean p_commit
	 *		If set to true, the value will be written to the database immediately.
	 *		If set to false, the value will not be written to the database.
	 *		Default is true.
	 *
	 * @param boolean p_isSql
	 *		Set this to TRUE if the values of p_columns contains SQL commands.
	 *		There is no way to know what the result of the command is,
	 *		so we will need to refetch the row from the database in
	 *		order to update the internal variable's value.
	 *
	 * @return boolean
	 *		Return TRUE if the database was updated, FALSE otherwise.
	 *		This means that if p_commit is FALSE, this function will 
	 *		always return false.
	 */
	function update($p_columns = null, $p_commit = true, $p_isSql = false) {
		global $Campsite;
		if (!is_array($p_columns)) {
			return false;
		}
        $setColumns = array();
        foreach ($p_columns as $columnName => $columnValue) {
        	if (!array_key_exists($columnName, $this->m_data)) {
        		//return new PEAR_Error('Column name '.$columnName.' does not exist.');
        		return false;
        	}
        	$setColumns[] = $columnName . "='". $columnValue ."'";
        	if (!$p_isSql) {
        		$this->m_data[$columnName] = $columnValue;
        	}
        }
        $databaseChanged = false;
        if ($p_commit) {
	        $queryStr = 'UPDATE ' . $this->m_dbTableName
	        			.' SET '.implode(',', $setColumns)
	        			.' WHERE ' . $this->getKeyWhereClause()
	        			.' LIMIT 1';
	        $Campsite['db']->Execute($queryStr);
			$databaseChanged = ($Campsite['db']->Affected_Rows() > 0);
        }
        if ($p_isSql) {
        	$this->fetch();
        }
		return $databaseChanged;
	} // fn update

	
	/**
	 * Commit the data stored in memory to the database.
	 * This is useful if you make a bunch of setProperty() calls at once
	 * and you dont want to update the database every time.  Instead you
	 * can set all the variables without committing them, then call this function.
	 *
	 * @param array p_ignoreColumns
	 *		Specify column names to ignore when doing the commit.
	 *
	 * @return boolean
	 *		Return TRUE if the database was updated, false otherwise.
	 */
	function commit($p_ignoreColumns = null) {
		global $Campsite;
        $setColumns = array();
        foreach ($this->m_data as $columnName => $columnValue) {
        	if (!is_null($p_ignoreColumns) && !in_array($columnName, $p_ignoreColumns)) {
        		$setColumns[] = $columnName . "='". $columnValue ."'";
        	}
        }
        $databaseChanged = false;
		$queryStr = 'UPDATE ' . $this->m_dbTableName
	        		.' SET '.implode(',', $setColumns)
	        		.' WHERE ' . $this->getKeyWhereClause()
	        		.' LIMIT 1';
        $Campsite['db']->Execute($queryStr);
		return ($Campsite['db']->Affected_Rows() > 0);
	} // fn commit
		
} // class DatabaseObject

?>