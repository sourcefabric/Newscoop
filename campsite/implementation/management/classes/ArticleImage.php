<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable 
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT'] 
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/Image.php');

/**
 * @package Campsite
 */
class ArticleImage extends DatabaseObject {
	var $m_keyColumnNames = array('NrArticle','IdImage');
	var $m_dbTableName = 'ArticleImages';
	var $m_columnNames = array('NrArticle', 'IdImage', 'Number');
	var $m_image = null;
	
	/**
	 * The ArticleImage table links together Articles with Images.
	 *
	 * @param int $p_articleNumber
	 * @param int $p_imageId
	 * @param int $p_templateId
	 * @return ArticleImage
	 */
	function ArticleImage($p_articleNumber = null, $p_imageId = null, $p_templateId = null) 
	{ 
		if (!is_null($p_articleNumber) && !is_null($p_imageId)) {
			$this->m_data['NrArticle'] = $p_articleNumber;
			$this->m_data['IdImage'] = $p_imageId;
			$this->fetch();
		}
		elseif (!is_null($p_articleNumber) && !is_null($p_templateId)) {
			$this->m_data['NrArticle'] = $p_articleNumber;
			$this->m_data['Number'] = $p_templateId;
			$this->m_keyColumnNames = array('NrArticle', 'Number');
			$this->fetch();
		}
	} // constructor
	
	
	/**
	 * @return int
	 */
	function getImageId() 
	{
		return $this->getProperty('IdImage');
	} // fn getImageId
	
	
	/**
	 * @return int
	 */
	function getArticleNumber() 
	{
		return $this->getProperty('NrArticle');
	} // fn getArticleNumber

	
	/**
	 * @return int
	 */
	function getTemplateId() 
	{
		return $this->getProperty('Number');
	} // fn getTemplateId
	
	
	/**
	 * Return an Image object.
	 */
	function getImage() 
	{
		return $this->m_image;
	}
	

	/**
	 * Get a free Template ID.
	 * @param int $p_articleNumber
	 */
	function GetUnusedTemplateId($p_articleNumber) 
	{
		global $Campsite;
		// Get the highest template ID and add one.
		$queryStr = "SELECT MAX(Number)+1 FROM ArticleImages WHERE NrArticle=$p_articleNumber";
		$templateId = $Campsite['db']->GetOne($queryStr);
		if (!$templateId) {
			$templateId = 1;
		}
		return $templateId;
	} // fn GetUnusedTemplateId
	
	
	/**
	 * Return true if article already is using the given template ID, false otherwise.
	 *
	 * @param int $p_articleNumber
	 * @param int $p_templateId
	 *
	 * @return boolean
	 */
	function TemplateIdInUse($p_articleNumber, $p_templateId) 
	{
		global $Campsite;
		$queryStr = "SELECT Number FROM ArticleImages"
					." WHERE NrArticle=$p_articleNumber AND Number=$p_templateId";
		$value = $Campsite['db']->GetOne($queryStr);
		if ($value !== false) {
			return true;
		}
		else {
			return false;
		}
	} // fn TemplateIdInUse
	
	
	/**
	 * Get all the images that belong to this article.
	 * @param int $p_articleNumber
	 * @return array
	 */
	function GetImagesByArticleNumber($p_articleNumber) 
	{
		global $Campsite;
		$tmpImage =& new Image();
		$columnNames = implode(',', $tmpImage->getColumnNames());
		
		$queryStr = 'SELECT '.$columnNames
					.', ArticleImages.Number, ArticleImages.NrArticle, ArticleImages.IdImage'
					.' FROM Images, ArticleImages'
					.' WHERE ArticleImages.NrArticle='.$p_articleNumber
					.' AND ArticleImages.IdImage=Images.Id'
					.' ORDER BY ArticleImages.Number';
		$rows = $Campsite['db']->GetAll($queryStr);
		$returnArray = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpArticleImage =& new ArticleImage();
				$tmpArticleImage->fetch($row);
				$tmpArticleImage->m_image =& new Image();
				$tmpArticleImage->m_image->fetch($row);
				$returnArray[] =& $tmpArticleImage;
			}
		}
		return $returnArray;
	} // fn GetImagesByArticleNumber
	
	
	/**
	 * Link the given image with the given article.  The template ID
	 * is the image's position in the template.
	 *
	 * @param int $p_imageId
	 * @param int $p_articleNumber
	 * @param int $p_templateId
	 *		Optional.  If not specified, this will be the next highest number
	 *		of the existing values.
	 *
	 * @return void
	 */
	function AddImageToArticle($p_imageId, $p_articleNumber, $p_templateId = null) 
	{
		global $Campsite;
		if (is_null($p_templateId)) {
			$p_templateId = ArticleImage::GetUnusedTemplateId($p_articleNumber);
		}
		$queryStr = 'INSERT IGNORE INTO ArticleImages(NrArticle, IdImage, Number)'
					.' VALUES('.$p_articleNumber.', '.$p_imageId.', '.$p_templateId.')';
		$Campsite['db']->Execute($queryStr);
	} // fn AddImageToArticle

	
	/**
	 * This call will only work for entries that already exist.
	 *
	 * @param int $p_articleNumber
	 * @param int $p_imageId
	 * @param int $p_templateId
	 *
	 * @return void
	 */
	function SetTemplateId($p_articleNumber, $p_imageId, $p_templateId) 
	{
		global $Campsite;
		$queryStr = "UPDATE ArticleImages SET Number=$p_templateId"
					." WHERE NrArticle=$p_articleNumber AND IdImage=$p_imageId";
		$Campsite['db']->Execute($queryStr);
	} // fn SetTemplateId
	
	
	/**
	 * Remove the linkage between the given image and the given article and remove
	 * the image tags from the article text.
	 *
	 * @param int $p_imageId
	 * @param int $p_articleNumber
	 * @param int $p_templateId
	 *
	 * @return void
	 */
	function RemoveImageFromArticle($p_imageId, $p_articleNumber, $p_templateId) 
	{
		global $Campsite;
		ArticleImage::RemoveImageTagsFromArticleText($p_articleNumber, $p_templateId);
		$queryStr = 'DELETE FROM ArticleImages'
					.' WHERE NrArticle='.$p_articleNumber
					.' AND IdImage='.$p_imageId
					.' AND Number='.$p_templateId
					.' LIMIT 1';
		$Campsite['db']->Execute($queryStr);
	} // fn RemoveImageFromArticle

	
	/**
	 * Remove the image tags in the article text.
	 *
	 * @param int $p_imageId
	 * @param int $p_articleNumber
	 * @param int $p_templateId
	 * @return void
	 */
	function RemoveImageTagsFromArticleText($p_articleNumber, $p_templateId)
	{
		// Get all the articles
		$articles = Article::GetArticles(null, null, null, null, $p_articleNumber);
		
		// The REGEX
		$altAttr = "(alt\s*=\s*[\"][^\"]*[\"])";
		$alignAttr = "(align\s*=\s*\w*)";
		$subAttr = "(sub\s*=\s*[\"][^\"]*[\"])";
		$matchString = "/<!\*\*\s*Image\s*$p_templateId\s*(($altAttr|$alignAttr|$subAttr)\s*)*>/i";

		// Replace the article tag in each one with the empty string
		foreach ($articles as $article) {
			$articleData = $article->getArticleData();
			$dbColumns = $articleData->getUserDefinedColumns();
			foreach ($dbColumns as $dbColumn) {
				$originalText = $articleData->getProperty($dbColumn->getName());
				$newText = preg_replace($matchString, '', $originalText);
				if ($originalText != $newText) {
					$articleData->setProperty($dbColumn->getName(), $newText);
				}
			}
		}
	} // fn RemoveImageTagsFromArticleText
	
	
	/**
	 * This is called when an image is deleted.
	 * It will disassociate the image from all articles.
	 *
	 * @param int $p_imageId
	 * @return void
	 */
	function OnImageDelete($p_imageId) 
	{
		global $Campsite;
		// Get the articles that use this image.
		$queryStr = "SELECT * FROM ArticleImages WHERE IdImage=$p_imageId";
		$rows = $Campsite['db']->GetAll($queryStr);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				ArticleImage::RemoveImageTagsFromArticleText($row['NrArticle'], $row['Number']);
			}
			$queryStr = "DELETE FROM ArticleImages WHERE IdImage=$p_imageId";
			$Campsite['db']->Execute($queryStr);
		}
	} // fn OnImageDelete
	
	
	/**
	 * Remove image pointers for the given article.
	 * @param int $p_articleNumber
	 * @return void
	 */
	function OnArticleDelete($p_articleNumber) 
	{
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleImages'
					." WHERE NrArticle='".$p_articleNumber."'";
		$Campsite['db']->Execute($queryStr);		
	} // fn OnArticleDelete
	

	/**
	 * Copy all the pointers for the given article.
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM ArticleImages WHERE NrArticle='.$p_srcArticleNumber;
		$rows = $Campsite['db']->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleImages(NrArticle, IdImage, Number)'
						." VALUES($p_destArticleNumber, ".$row['IdImage'].",".$row['Number'].")";
			$Campsite['db']->Execute($queryStr);
		}
	} // fn OnArticleCopy
	
	
	/**
	 * Return an array of Article objects, all the articles
	 * which use this image.
	 *
	 * @return array
	 */
	function GetArticlesThatUseImage($p_imageId) 
	{
		global $Campsite;
		$article =& new Article();
		$columnNames = $article->getColumnNames();
		$columnQuery = array();
		foreach ($columnNames as $columnName) {
			$columnQuery[] = 'Articles.'.$columnName;
		}
		$columnQuery = implode(',', $columnQuery);
		$queryStr = 'SELECT '.$columnQuery.' FROM Articles, ArticleImages '
					.' WHERE ArticleImages.IdImage='.$p_imageId
					.' AND ArticleImages.NrArticle=Articles.Number'
					.' ORDER BY Articles.Number, Articles.IdLanguage';
		$rows = $Campsite['db']->GetAll($queryStr);
		$articles = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpArticle =& new Article();
				$tmpArticle->fetch($row);
				$articles[] =& $tmpArticle;
			}
		}
		return $articles;
	} // fn GetArticlesThatUseImage
	
} // class ArticleImages

?>