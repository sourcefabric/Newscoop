<?php 

/**
 * 
 *
 */
class DbObjectArray {
	
	/**
	 * Given an array of DatabaseObjects, return one column
	 * of the data.
	 *
	 * @param array p_array
	 * @param string p_columnName
	 * @return array
	 */ 
	function GetColumn($p_array, $p_columnName) {
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
	 * @param array p_array
	 * @return array
	 */
	function GetTable($p_array) {
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