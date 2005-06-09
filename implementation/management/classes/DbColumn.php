<?php

class DbColumn {
	var $m_dbTableName;
	var $m_dbColumnName;
	var $Field;
	var $Type;
	var $Null;
	var $Key;
	var $Default;
	var $Extra;
	
	function DbColumn($p_tableName, $p_columnName = null) 
	{
		$this->m_dbTableName = $p_tableName;
		$this->m_dbColumnName = $p_columnName;
	} // constructor
	
	
	function getDbTableName() 
	{
		return $this->m_dbTableName;
	} // fn getDbTableName
	
	
	function fetch($p_recordSet = null) 
	{
		if (!is_null($p_recordSet)) {
			foreach ($p_recordSet as $key => $value) {
				$this->$key = $value;
			}
		}
	} // fn fetch
	
	
	function getName() 
	{
		return $this->Field;
	} // fn getName
	

	function getPrintName() 
	{
		return substr($this->Field, 1);
	} // fn getPrintName
	
	
	function getType() 
	{
		return strtolower($this->Type);
	} // fn getType
	
} // class DbColumn


?>