<?
require_once("$DOCUMENT_ROOT/db_connect.php");
require_once("$DOCUMENT_ROOT/priv/lib_campsite.php");

class Article {
	// These five fields are the primary key
	var $m_publication;
	var $m_issue;
	var $m_section;
	var $m_number;
	var $m_language;
	
	var $m_type;
	var $m_userId;
	
	// Name is the title of the article
	var $m_name;
	var $m_intro;
	var $m_body;
	
	/**
	 * Construct by passing in the primary key to access the article in 
	 * the database.
	 *
	 */
	function Article($publication, $issue, $section, $number, $language) {
		$this->m_publication = $publication;
		$this->m_issue = $issue;
		$this->m_section = $section;
		$this->m_number = $number;
		$this->m_language = $language;
		$this->fetch();
	} // ctor
	
	
	/**
	 * Fetch all the data for this article.
	 * Note: does not currently support all fields.
	 *
	 * @return void
	 */
	function fetch() {
		$queryStr = "SELECT Type, Name FROM Articles WHERE ".$this->__getKey();
	    $result=mysql_query($queryStr);
		$row = mysql_fetch_assoc($result);
		$this->m_type = $row["Type"];
		$this->m_name = $row["Name"];
	} // fn fetch

	
	/**
	 * Get the WHERE SQL clause used to fetch this article in the Articles table.
	 *
	 * @return string
	 */
	function __getKey() {
		$whereClause = "IdPublication='".$this->m_publication."'"
						." AND NrIssue='".$this->m_issue."'"
						." AND NrSection='".$this->m_section."'"
						." AND Number='".$this->m_number."'"
						." AND IdLanguage='".$this->m_language."'";
		return $whereClause;
	} // fn __getKey
	
	
	/**
	 * Get the name of the dynamic article type table.
	 *
	 * @return string
	 */
	function getArticleTypeTableName() {
		return "X".$this->m_type;
	}
	
	/**
	 * Set the body of the article.
	 *
	 * @param string body
	 * 
	 * @return void
	 */
	function setBody($body) {
		$queryStr = "UPDATE ".$this->getArticleTypeTableName()
					." SET Fbody='".addslashes(trim($body))."'"
					." WHERE NrArticle='".$this->m_number."'"
					." AND IdLanguage='".$this->m_language."'";
		mysql_query($queryStr);
		$this->m_body = trim($body);
	} // fn setBody

	
	/**
	 * Set the intro of the article.
	 *
	 * @param string into
	 *
	 * @return void
	 */
	function setIntro($intro) {
		$queryStr = "UPDATE ".$this->getArticleTypeTableName()
					." SET Fintro='".addslashes(trim($intro))."'"
					." WHERE NrArticle='".$this->m_number."'"
					." AND IdLanguage='".$this->m_language."'";
		mysql_query($queryStr);
		$this->m_intro = trim($intro);
	} // fn setIntro

	
	/**
	 * Set the title of the article.
	 *
	 * @param string title
	 *
	 * @return void
	 */
	function setTitle($title) {
		$queryStr = "UPDATE Articles "
					." SET Name='". addslashes(trim($title)) . "' "
					." WHERE " . $this->__getKey();
		mysql_query($queryStr);
		$this->m_name = trim($title);
	} // fn setTitle

	
	function getTitle() {
		return $this->m_name;
	} // fn getTitle
	
	function getType() {
		return $this->m_type;
	} // fn getType
	
} // class Article
?>