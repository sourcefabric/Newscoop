<?
class DatabaseObject {
	/**
	 * @var array
	 */
	var $m_columnNames = array();
	
	/**
	 * @var array
	 */
	var $m_primaryKeyColumnNames = array();
	
	/**
	 * @var string
	 */
	var $m_dbTableName = "";
	
	
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
	 * 
	 * @return string
	 */
	function getDbTableName() { return $this->m_dbTableName; }
	
	
	/**
	 * Fetch a single record from the database for the given key.
	 * Note: The subclass must implement getVarMap() for this function to work.
	 */
	function fetch() {
		global $Campsite;
		
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
		return true;
	} // fn fetch
	
	
	/**
	 * Return true if the object exists in the database.
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
	 * @param array p_values
	 *		Extra values to be set at create time, in the form of:
	 *		(DB Column Name) => (value)
	 * @return boolean
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
		$resultSet = $Campsite["db"]->Execute($queryStr);
		return (($resultSet != false) && ($this->exists()));
	} // fn create
	
	
	function delete() {
		global $Campsite;
		$queryStr = "DELETE FROM " . $this->m_dbTableName
					. " WHERE " . $this->getKeyWhereClause();
		$Campsite["db"]->Execute($queryStr);
	} // fn delete

	
	function setProperty($p_dbColumnName, $p_value) {
		global $Campsite;
		$queryStr = "UPDATE ".$this->m_dbTableName
					. " SET `". $p_dbColumnName."`='".$p_value."'"
					. " WHERE ".$this->getKeyWhereClause();
		$Campsite["db"]->Execute($queryStr);
		$this->$p_dbColumnName = $p_value;
	} // fn setProperty
	
	
} // class DatabaseObject

?>