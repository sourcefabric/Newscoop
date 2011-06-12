<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');

/**
 * @package Campsite
 */
class ArticleImage extends DatabaseObject {
	var $m_keyColumnNames = array('NrArticle','IdImage');
	var $m_dbTableName = 'ArticleImages';
	var $m_columnNames = array('NrArticle', 'IdImage', 'Number');
	var $m_image = null;

    private static $s_defaultOrder = array(array('field'=>'default', 'dir'=>'ASC'));

	/**
	 * The ArticleImage table links together Articles with Images.
	 *
	 * @param int $p_articleNumber
	 * @param int $p_imageId
	 * @param int $p_templateId
	 * @return ArticleImage
	 */
	public function ArticleImage($p_articleNumber = null, $p_imageId = null,
	                             $p_templateId = null)
	{
		if (!is_null($p_articleNumber) && !is_null($p_imageId)) {
			$this->m_data['NrArticle'] = $p_articleNumber;
			$this->m_data['IdImage'] = $p_imageId;
			$this->fetch();
		} elseif (!is_null($p_articleNumber) && !is_null($p_templateId)) {
			$this->m_data['NrArticle'] = $p_articleNumber;
			$this->m_data['Number'] = $p_templateId;
			$this->m_keyColumnNames = array('NrArticle', 'Number');
			$this->fetch();
			$this->m_keyColumnNames = array('NrArticle', 'IdImage');
		}
	} // constructor


    /**
     * Wrapper around DatabaseObject::setProperty
     *
     * @see classes/DatabaseObject#setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql)
     */
    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        if ($p_dbColumnName == 'Number') {
            $this->m_keyColumnNames = array('NrArticle', 'Number');
            $this->resetCache();
            $this->m_keyColumnNames = array('NrArticle', 'IdImage');
        }
        return parent::setProperty($p_dbColumnName, $p_value);
    } // fn setProperty


	/**
	 * @return int
	 */
	public function getImageId()
	{
		if (in_array('IdImage', array_keys($this->m_data))) return $this->m_data['IdImage'];
		else return false;
	} // fn getImageId


	/**
	 * @return int
	 */
	public function getImageArticleIndex()
	{
	    return $this->m_data['Number'];
	}


	/**
	 * @return int
	 */
	public function getArticleNumber()
	{
		return $this->m_data['NrArticle'];
	} // fn getArticleNumber


	/**
	 * @return int
	 */
	public function getTemplateId()
	{
		return $this->m_data['Number'];
	} // fn getTemplateId


	/**
	 * Return an Image object.
	 */
	public function getImage()
	{
		if (is_object($this->m_image)) {
			return $this->m_image;
		} else {
			return new Image($this->m_data['IdImage']);
		}
	} // fn getImage


    /**
     * This call will only work for entries that already exist.
     *
     * @param int $p_templateId
     *
     * @return boolean
     */
    public function setTemplateId($p_templateId)
    {
        return $this->setProperty('Number', $p_templateId);
    } // fn setTemplateId


    /**
     * Remove the linkage between the given image and the given article and remove
     * the image tags from the article text.
     *
     * @return boolean
     */
    public function delete()
    {
        if (!$this->exists()) {
            return false;
        }
        ArticleImage::RemoveImageTagsFromArticleText($this->getArticleNumber(), $this->getTemplateId());
        $result = parent::delete();
        if ($result) {
        	if (function_exists("camp_load_translation_strings")) {
        		camp_load_translation_strings("api");
        	}
        	$logtext = getGS('Image $1 unlinked from article $2', $p_imageId, $p_articleNumber);
        	Log::Message($logtext, null, 42);
        }
        return $result;
    }


	/**
	 * Get a free Template ID.
	 * @param int $p_articleNumber
	 */
	public static function GetUnusedTemplateId($p_articleNumber)
	{
		global $g_ado_db;
		// Get the highest template ID and add one.
		$queryStr = "SELECT MAX(Number)+1 FROM ArticleImages WHERE NrArticle=$p_articleNumber";
		$templateId = $g_ado_db->GetOne($queryStr);
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
	public static function TemplateIdInUse($p_articleNumber, $p_templateId)
	{
		global $g_ado_db;
		$queryStr = "SELECT Number FROM ArticleImages"
					." WHERE NrArticle=$p_articleNumber AND Number=$p_templateId";
		$value = $g_ado_db->GetOne($queryStr);
		if ($value !== false) {
			return true;
		} else {
			return false;
		}
	} // fn TemplateIdInUse


	/**
	 * Get all the images that belong to this article.
	 *
	 * @param int $p_articleNumber
	 * 		The specific article you want the images from.
	 * @param boolean $p_countOnly
	 * 		Only return the number of images in the article.
	 * @return mixed
	 * 		Return either an array or an int.
	 */
	public static function GetImagesByArticleNumber($p_articleNumber, $p_countOnly = false)
	{
		global $g_ado_db;

		if ($p_countOnly) {
			$selectStr = "COUNT(*)";
		} else {
			$tmpImage = new Image();
			$selectStr = implode(',', $tmpImage->getColumnNames());
			$selectStr .= ', ArticleImages.Number, ArticleImages.NrArticle, ArticleImages.IdImage';
		}
		$queryStr = 'SELECT '.$selectStr
					.' FROM Images, ArticleImages'
					.' WHERE ArticleImages.NrArticle='.$p_articleNumber
					.' AND ArticleImages.IdImage=Images.Id'
					.' ORDER BY ArticleImages.Number';
		if ($p_countOnly) {
			return $g_ado_db->GetOne($queryStr);
		} else {
			$rows = $g_ado_db->GetAll($queryStr);
			$returnArray = array();
			if (is_array($rows)) {
				foreach ($rows as $row) {
					$tmpArticleImage = new ArticleImage();
					$tmpArticleImage->fetch($row);
					$tmpArticleImage->m_image = new Image();
					$tmpArticleImage->m_image->fetch($row);
					$returnArray[] = $tmpArticleImage;
				}
			}
			return $returnArray;
		}
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
	public static function AddImageToArticle($p_imageId, $p_articleNumber,
	                                         $p_templateId = null)
	{
		global $g_ado_db;
		if (is_null($p_templateId)) {
			$p_templateId = ArticleImage::GetUnusedTemplateId($p_articleNumber);
		}
		$queryStr = 'INSERT IGNORE INTO ArticleImages(NrArticle, IdImage, Number)'
					.' VALUES('.$p_articleNumber.', '.$p_imageId.', '.$p_templateId.')';
		$g_ado_db->Execute($queryStr);
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('Image $1 linked to article $2', $p_imageId, $p_articleNumber);
		Log::Message($logtext, null, 41);
	} // fn AddImageToArticle


	/**
	 * Remove the image tags in the article text.
	 *
	 * @param int $p_imageId
	 * @param int $p_articleNumber
	 * @param int $p_templateId
	 * @return void
	 */
	public static function RemoveImageTagsFromArticleText($p_articleNumber, $p_templateId)
	{
		// Get all the articles
		$articles = Article::getTranslations($p_articleNumber);

		// The REGEX
		$altAttr = '(alt\s*=\s*("[^"]*"|[^\s]*))';
		$alignAttr = '(align\s*=\s*("[^"]*"|[^\s]*))';
		$subAttr = '(sub\s*=\s*("[^"]*"|[^\s]*))';
		$otherAttr = '(\w+\s*=\s*("[^"]*"|[^\s]*))*';
		$matchString = "/<!\*\*\s*Image\s*$p_templateId\s*(($altAttr|$alignAttr|$subAttr|$otherAttr)\s*)*>/i";

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
	public static function OnImageDelete($p_imageId)
	{
		global $g_ado_db;
		// Get the articles that use this image.
		$queryStr = "SELECT * FROM ArticleImages WHERE IdImage=$p_imageId";
		$rows = $g_ado_db->GetAll($queryStr);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				ArticleImage::RemoveImageTagsFromArticleText($row['NrArticle'], $row['Number']);
			}
			$queryStr = "DELETE FROM ArticleImages WHERE IdImage=$p_imageId";
			$g_ado_db->Execute($queryStr);
		}
	} // fn OnImageDelete


	/**
	 * Remove image pointers for the given article.
	 * @param int $p_articleNumber
	 * @return void
	 */
	public static function OnArticleDelete($p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'DELETE FROM ArticleImages'
					." WHERE NrArticle='".$p_articleNumber."'";
		$g_ado_db->Execute($queryStr);
	} // fn OnArticleDelete


	/**
	 * Copy all the pointers for the given article.
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	public static function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
	{
		global $g_ado_db;
		$queryStr = 'SELECT * FROM ArticleImages WHERE NrArticle='.$p_srcArticleNumber;
		$rows = $g_ado_db->GetAll($queryStr);
		foreach ($rows as $row) {
			$queryStr = 'INSERT IGNORE INTO ArticleImages(NrArticle, IdImage, Number)'
						." VALUES($p_destArticleNumber, ".$row['IdImage'].",".$row['Number'].")";
			$g_ado_db->Execute($queryStr);
		}
	} // fn OnArticleCopy


	/**
	 * Return an array of Article objects, all the articles
	 * which use this image.
	 *
	 * @return array
	 */
	public static function GetArticlesThatUseImage($p_imageId)
	{
		global $g_ado_db;
		$article = new Article();
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
		$rows = $g_ado_db->GetAll($queryStr);
		$articles = array();
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpArticle = new Article();
				$tmpArticle->fetch($row);
				$articles[] = $tmpArticle;
			}
		}
		return $articles;
	} // fn GetArticlesThatUseImage


    /**
     * Returns an article images list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     * @param integer $p_count
     *    The total count of the elements; this count is computed without
     *    applying the start ($p_start) and limit parameters ($p_limit)
     *
     * @return array $articleImagesList
     *    An array of Image objects
     */
    public static function GetList(array $p_parameters, array $p_order = array(),
                                   $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false)
    {
        global $g_ado_db;

        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$paramsArray['parameters'] = serialize($p_parameters);
        	$paramsArray['order'] = (is_null($p_order)) ? 'null' : $p_order;
        	$paramsArray['start'] = $p_start;
        	$paramsArray['limit'] = $p_limit;
        	$cacheListObj = new CampCacheList($paramsArray, __METHOD__);
        	$articleImagesList = $cacheListObj->fetchFromCache();
        	if ($articleImagesList !== false && is_array($articleImagesList)) {
        		return $articleImagesList;
        	}
        }

        $hasArticleNr = false;
        $selectClauseObj = new SQLSelectClause();
        $countClauseObj = new SQLSelectClause();

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (sizeof($comparisonOperation) < 1) {
                break;
            }

            if (strpos($comparisonOperation['left'], 'NrArticle')) {
                $hasArticleNr = true;
            }
            $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $g_ado_db->escape($comparisonOperation['right']) . "' ";
            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // validates whether article number was given
        if ($hasArticleNr === false) {
            CampTemplate::singleton()->trigger_error('Missing parameter Article '
                .'Number in statement list_article_images');
            return;
        }

        // sets the columns to be fetched
        $tmpImage = new Image();
		$columnNames = $tmpImage->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }
        $countClauseObj->addColumn('COUNT(*)');

        // sets the base table Attachment
        $selectClauseObj->setTable($tmpImage->getDbTableName());
        $countClauseObj->setTable($tmpImage->getDbTableName());
        unset($tmpImage);

        // adds the ArticleImages join and condition to the query
        $selectClauseObj->addTableFrom('ArticleImages');
        $selectClauseObj->addWhere('ArticleImages.IdImage = Images.Id');
        $countClauseObj->addTableFrom('ArticleImages');
        $countClauseObj->addWhere('ArticleImages.IdImage = Images.Id');

        // sets the ORDER BY condition
        $p_order = array_merge($p_order, self::$s_defaultOrder);
        $order = self::ProcessListOrder($p_order);
        foreach ($order as $orderDesc) {
            $orderColumn = $orderDesc['field'];
            $orderDirection = $orderDesc['dir'];
            $selectClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        // sets the limit
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the query executes it
        $selectQuery = $selectClauseObj->buildQuery();
        $images = $g_ado_db->GetAll($selectQuery);
        if (is_array($images)) {
        	$countQuery = $countClauseObj->buildQuery();
        	$p_count = $g_ado_db->GetOne($countQuery);

        	// builds the array of image objects
        	$articleImagesList = array();
        	foreach ($images as $image) {
        		$imgObj = new Image($image['Id']);
        		if ($imgObj->exists()) {
        			$articleImagesList[] = $imgObj;
        		}
        	}
        } else {
        	$articleImagesList = array();
        	$p_count = 0;
        }
        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$cacheListObj->storeInCache($articleImagesList);
        }

        return $articleImagesList;
    } // fn GetList


    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $comparisonOperation;
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
        $comparisonOperation = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'nrarticle':
            $comparisonOperation['left'] = 'ArticleImages.NrArticle';
            $comparisonOperation['right'] = (int) $p_param->getRightOperand();
            break;
        }

        if (isset($comparisonOperation)) {
            $operatorObj = $p_param->getOperator();
            $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');
        }

        return $comparisonOperation;
    } // fn ProcessListParameters


    /**
     * Processes an order directive coming from template tags.
     *
     * @param array $p_order
     *      The array of order directives in the format:
     *      array('field'=>field_name, 'dir'=>order_direction)
     *      field_name can take one of the following values:
     *        bynumber, byname, bydate, bycreationdate, bypublishdate,
     *        bypublication, byissue, bysection, bylanguage, bysectionorder,
     *        bypopularity, bycomments
     *      order_direction can take one of the following values:
     *        asc, desc
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $orderDesc) {
            $field = $orderDesc['field'];
            $direction = $orderDesc['dir'];
            $dbField = null;
            switch (strtolower($field)) {
            	case 'default':
                case 'bynumber':
                    $dbField = 'ArticleImages.Number';
                    break;
                case 'bydescription':
                    $dbField = 'Images.Description';
                    break;
                case 'byphotographer':
                    $dbField = 'Images.Photographer';
                    break;
                case 'bydate':
                    $dbField = 'Images.Date';
                    break;
                case 'bylastupdate':
                	$dbField = 'Images.LastModified';
                	break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
                $order[] = array('field'=>$dbField, 'dir'=>$direction);
            }
        }
        return $order;
    }

} // class ArticleImages

?>