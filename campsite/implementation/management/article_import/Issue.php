<?

class Issue {
	var $m_publication;
	var $m_number;
	var $m_language;
	var $m_name;
	
	function Issue($publication, $number, $language) {
		$this->m_publication = $publication;
		$this->m_number = $number;
		$this->m_language = $language;
		$this->fetch();
	} // ctor

	
	function fetch() {
		$queryStr = "SELECT Name "
				    ." FROM Issues "
					." WHERE IdPublication='".$this->m_publication."'"
					." AND Number='".$this->m_number."'"
					." AND IdLanguage='".$this->m_language."'";
		$result = mysql_query($queryStr);
		$row = mysql_fetch_assoc($result);
		$this->m_name = $row["Name"];		
	} // fn fetch
	
	function getName() {
		return $this->m_name;
	}
	
	function getNumber() {
		return $this->m_number;
	}
} // class Issue

?>