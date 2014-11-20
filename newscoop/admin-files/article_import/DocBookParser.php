<?
require_once('XML/Parser/Simple.php');

class DocBookParser extends XML_Parser_Simple {
//	var $m_firstName;
//	var $m_surName;
	var $m_author;
	var $m_title;
	var $m_intro;
	var $m_body;
	
	function DocBookParser() {
		$this->XML_Parser_Simple();
	} // constructor

	function handleElement($name, $attribs, $data) {
		//printf('handle %s<br>', $name);
		switch ($name) {
		case "AUTHOR":
			$this->m_author = $data;
			break;
//		case "FIRSTNAME":
//			$this->m_firstName = $data;
//			break;
//		case "SURNAME":
//			$this->m_surName = $data;
//			break;
		case "TITLE":
			$this->m_title = $data;
			break;
		case "ABSTRACT":
			$this->m_intro = $data;
			break;
		case "SIMPLESECT":
			$this->m_body  = $data;
			break;
		}
	} // fn handleElement
	
	function dump() {
		echo "Title: " . $this->m_title . "<Br>";
		//echo "Author: " . $this->m_firstName . " " . $this->m_surName . "<br>";
		echo "Author: " . $this->m_author . "<br>";
		echo "Intro: " . $this->m_intro . "<br>";
		echo "Body: " . $this->m_body . "<br>";
	} // fn dump

	
	function getAuthor() {
		//return $this->m_firstName . " " . $this->m_surName;
		return $this->m_author;
	} // fn getAuthor
	
	/**
	 * @return string
	 */
	function getTitle() {
		return $this->m_title;
	} // fn getTitle
	
	function getIntro() {
		return $this->m_intro;
	} // fn getIntro
	
	function getBody() {
		return $this->m_body;
	} // fn getBody
	
} // class DocBookParser

?>