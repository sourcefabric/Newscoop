<?
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DatabaseObject.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');

class ArticleImage extends DatabaseObject {
	var $m_keyColumnNames = array('NrArticle','IdImage');
	var $m_dbTableName = 'ArticleImages';
	var $m_columnNames = array('NrArticle', 'IdImage', 'Number');
	var $m_image = null;
	
	function ArticleImage() { }
	
	function getImageId() {
		return $this->getProperty('IdImage');
	}
	
	function getArticleId() {
		return $this->getProperty('NrArticle');
	}

	function getTemplateId() {
		return $this->getProperty('Number');
	}
	
	function getImage() {
		return $this->m_image;
	}
	
	/**
	 * Get all the images that belong to this article.
	 * @return array
	 */
	function FetchImagesByArticleId($p_articleId) {
		global $Campsite;
		$tmpImage =& new Image();
		$columnNames = implode(',', $tmpImage->getColumnNames());
		
		$queryStr = 'SELECT '.$columnNames
					.', ArticleImages.Number, ArticleImages.NrArticle, ArticleImages.IdImage'
					.' FROM Images, ArticleImages'
					.' WHERE ArticleImages.NrArticle='.$p_articleId
					.' AND ArticleImages.IdImage=Images.Id';
		$rows =& $Campsite['db']->GetAll($queryStr);
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
	} // fn FetchImagesByArticleId
	
	
	/**
	 * @param int p_imageId
	 * @param int p_articleId
	 * @param int p_templateId
	 *
	 * @return void
	 */
	function AssociateImageWithArticle($p_imageId, $p_articleId, $p_templateId) {
		global $Campsite;
		$queryStr = 'INSERT IGNORE INTO ArticleImages(NrArticle, IdImage, Number)'
					." VALUES('".$this->getArticleId()."', '".$p_imageId."','".$p_templateId."')";
		$Campsite['db']->Execute($queryStr);
	} // fn AssociateImageWithArticle

	
	/**
	 * @param int p_imageId
	 * @param int p_articleId
	 * @param int p_templateId
	 * @return void
	 */
	function DisassociateImageFromArticle($p_imageId, $p_articleId, $p_templateId) {
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleImages'
					.' WHERE NrArticle='.$p_articleId
					.' AND IdImage='.$p_imageId
					.' AND Number='.$p_templateId
					.' LIMIT 1';
		$Campsite['db']->Execute($queryStr);
	} // fn DisassociateImageFromArticle

	
	/**
	 * Disassociate the image from all articles.
	 *
	 * @param int p_imageId
	 * @return void
	 */
	function OnImageDelete($p_imageId) {
		global $Campsite;
		$queryStr = 'DELETE FROM ArticleImages'
					." WHERE IdImage='".$p_imageId."'";
		$Campsite['db']->Execute($queryStr);
	} // fn OnImageDelete
	
	
} // class ArticleImages

?>