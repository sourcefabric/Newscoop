<?

class Publication {
	var $m_publication;
	var $m_name;
	
	function Publication($publication) {
		$this->m_publication = $publication;
		$this->fetch();
	} // ctor
	
	function fetch() {
		$queryStr = "SELECT Name "
					." FROM Publications "
					." WHERE Id='".$this->m_publication."'";
		$result = mysql_query($queryStr);
		$row = mysql_fetch_assoc($result);
		$this->m_name = $row["Name"];				
	} // fn fetch
	
	function getName() {
		return $this->m_name;
	}
		
} // class Publication
?>