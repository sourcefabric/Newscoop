<?
require_once("$DOCUMENT_ROOT/classes/DatabaseObject.php");

class ArticleType extends DatabaseObject {
	var $m_dbColumnNames;
	var $m_primaryKeyColumnNames = array("NrArticle", "IdLanguage");
	var $m_dbTableName;
	var $NrArticle;
	var $IdLanguage;
	
	var $m_intro;
	var $m_body;
	
	function ArticleType($p_articleType, $p_articleId, $p_languageId) {
		$this->m_dbTableName = "X".$p_articleType;
		$this->NrArticle = $p_articleId;
		$this->IdLanguage = $p_languageId;
	} // constructor
	
	
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
		parent::setProperty("Fintro", "m_intro", $p_intro);
		$queryStr = "UPDATE ".$this->getArticleTypeTableName()
					." SET Fintro='".addslashes(trim($intro))."'"
					." WHERE NrArticle='".$this->m_number."'"
					." AND IdLanguage='".$this->m_language."'";
		mysql_query($queryStr);
		$this->m_intro = trim($intro);
	} // fn setIntro	
	
} // class ArticleType

?>