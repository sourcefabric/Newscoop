<?php
class DatabaseObject {
	/**
	 * Redefine this in the subclass.
	 * @var string
	 */
	var $m_dbTableName = "";

	/**
	 * Redefine this in the subclass.
	 * @var array
	 */
	var $m_primaryKeyColumnNames = array();

	/**
	 * Redefine this in the subclass.
	 * @var array
	 */
	var $m_columnNames = array();
	
	
	function DatabaseObject() {
	} // constructor
	
	
	/**
	 * Return the column names used for the primary key.
	 * @return array
	 */
	function getPrimaryKeyColumnNames() { return $this->m_primaryKeyColumnNames; }

	
	/**
	 * Return a mapping of database column name => internal var name.
	 * @return array
	 */	
	function getColumnNames() { return $this->m_columnNames; }
	
	
	/**
	 * Return the name of the database table.
	 * @return string
	 */
	function getDbTableName() { return $this->m_dbTableName; }
	
	
	/**
	 * Fetch a single record from the database for the given key.
	 *
	 * @param array p_recordSet
	 *		If the record has already been fetched and we just need to 
	 * 		assign the values to the object's internal member variables.
	 *
	 * @return boolean
	 *		TRUE on success, FALSE on failure
	 */
	function fetch($p_recordSet = null) {
		global $Campsite;
		
		if (is_null($p_recordSet)) {
			$queryStr = "SELECT ";
			$tmpColumnNames = array();
			foreach ($this->m_columnNames as $columnName) {
				$tmpColumnNames[] = "`".$columnName."`";
			}
			$queryStr .= implode(", ", $tmpColumnNames);
			$queryStr .= " FROM " . $this->m_dbTableName;
			$queryStr .= " WHERE " . $this->getKeyWhereClause();
			$queryStr .= " LIMIT 1";
			$resultSet =& $Campsite["db"]->GetRow($queryStr);
			if ($resultSet) {
				foreach ($this->m_columnNames as $dbColumnName) {
					$this->$dbColumnName = $resultSet[$dbColumnName];
				}
			}
			else {
				return false;
			}
		}
		else {
			foreach ($this->m_columnNames as $dbColumnName) {
				$this->$dbColumnName = $p_recordSet[$dbColumnName];
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
		$queryStr = "SELECT `".$this->m_primaryKeyColumnNames[0]."`";
		$queryStr .= " FROM " . $this->m_dbTableName;
		$queryStr .= " WHERE " . $this->getKeyWhereClause();
		$queryStr .= " LIMIT 1";
		$resultSet =& $Campsite["db"]->GetRow($queryStr);
		return (count($resultSet) > 0);
	} // fn exists
	
	
	/**
	 * Return a string for the primary key of the table.
	 *
	 * @return string
	 */
	function getKeyWhereClause() {
		$whereParts = array();
		foreach ($this->m_primaryKeyColumnNames as $columnName) {
			$whereParts[] = "`" . $columnName . "`='". $this->$columnName ."'";
		}
		return implode(" AND ", $whereParts);		
	} // fn getKeyWhereClause
	
	
	/**
	 * Return a string used to create or set columns in the database.
	 *
	 * @return string
	 */ 
	function getKeyCreateClause() {
		$parts = array();
		foreach ($this->m_primaryKeyColumnNames as $columnName) {
			$parts[] = "`".$columnName."`='".$this->$columnName."'";
		}
		return implode(", ", $parts);
	} // fn getKeyCreateClause
	
	
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
		$queryStr = "INSERT IGNORE INTO " . $this->m_dbTableName
					. " SET " . $this->getKeyCreateClause();
		if (!is_null($p_values)) {
			$parts = array();
			foreach ($p_values as $columnName => $value) {
				// Construct value string for the SET clause.
				$parts[] = "`".$columnName."`='".$value."'";
			}
			$queryStr .= ",".implode(", ", $parts);
		}
		$Campsite["db"]->Execute($queryStr);
		return ($Campsite["db"]->AffectedRows() > 0);
	} // fn create
	
	
	/**
	 * Delete the row from the database.
	 *
	 * @return boolean
	 *		TRUE if the record was deleted, false if not.
	 */
	function delete() {
		global $Campsite;
		$queryStr = "DELETE FROM " . $this->m_dbTableName
					. " WHERE " . $this->getKeyWhereClause();
		$Campsite["db"]->Execute($queryStr);
		return ($Campsite["db"]->Affected_Rows() > 0);
	} // fn delete

	
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
	 * @param boolean p_refetch
	 *		Set this to TRUE if p_value consists of SQL commands.
	 *		There is no way to know what the result of the command is,
	 *		so we will need to refetch the value from the database in
	 *		order to update the internal variable's value.
	 *
	 * @return boolean
	 *		TRUE if the database was changed, FALSE if it wasnt.
	 */
	function setProperty($p_dbColumnName, $p_value, $p_refetch = false) {
		global $Campsite;
		if ($p_value == $this->$p_dbColumnName) {
			return false;
		}
		$queryStr = "UPDATE ".$this->m_dbTableName
					. " SET `". $p_dbColumnName."`='".$p_value."'"
					. " WHERE ".$this->getKeyWhereClause();
		$Campsite["db"]->Execute($queryStr);
		$databaseChanged = ($Campsite["db"]->Affected_Rows() > 0);
		if (!$p_refetch) {
			$this->$p_dbColumnName = $p_value;
		}
		else {
			if ($databaseChanged) {
				$queryStr = "SELECT ".$this->$p_dbColumnName 
							." FROM ".$this->m_dbTableName
							." WHERE ".$this->getKeyWhereClause();
				$this->$p_dbColumnName = $Campsite["db"]->GetOne($queryStr);
			}
		}
		return $databaseChanged;
	} // fn setProperty
	
	
} // class DatabaseObject

?>