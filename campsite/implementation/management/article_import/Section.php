<?

class Section {
	var $m_publication;
	var $m_number;
	var $m_name;
	var $m_language;
	var $m_issue;
	
	function Section($publication, $issue, $language, $number) {
		$this->m_publication = $publication;
		$this->m_issue = $issue;
		$this->m_language = $language;
		$this->m_number = $number;
		$this->fetch();
	} // fn Section
	
	function fetch() {
		$queryStr = "SELECT Name "
					." FROM Sections "
					." WHERE IdPublication='".$this->m_publication."'"
					." AND NrIssue='".$this->m_issue."'"
					." AND IdLanguage='".$this->m_language."'"
					." AND Number='".$this->m_number."'";
		$result = mysql_query($queryStr);
		$row = mysql_fetch_assoc($result);
		$this->m_name = $row["Name"];
	} // fn fetch
	
	function getName() {
		return $this->m_name;
	} // fn getName
	
	function getNumber() {
		return $this->m_number;
	} // fn getNumber
} // class Section
?>