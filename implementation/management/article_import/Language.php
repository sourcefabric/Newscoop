<?

class Language {
	var $m_id;
	var $m_name;
	
	function Language($id) {
		$this->m_id = $id;
		$this->fetch();
	} // ctor
	
	function fetch() {
		$queryStr = "SELECT Name "
					." FROM Languages "
					." WHERE Id='".$this->m_id."'";
		$result = mysql_query($queryStr);
		$row = mysql_fetch_assoc($result);
		$this->m_name = $row["Name"];					
	} // fn fetch
	
	function getName() {
		return $this->m_name;
	}	
} // class Language

?>