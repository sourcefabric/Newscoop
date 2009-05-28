<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Author.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');


/**
 * @package Campsite
 */
class ArticleAuthor extends DatabaseObject {
    var $m_keyColumnNames = array('fk_article_number', 'fk_language_id', 'fk_author_id');
    var $m_dbTableName = 'ArticleAuthors';
    var $m_columnNames = array('fk_article_number',
                               'fk_language_id',
                               'fk_author_id');


    /**
     * The article authors table links together articles with authors.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @param int $p_authorId
     *
     */
    public function __construct($p_articleNumber = null, $p_languageId = null, $p_authorId = null)
    {
        if (is_numeric($p_articleNumber)) {
            $this->m_data['fk_article_number'] = $p_articleNumber;
        }
        if (is_numeric($p_languageId)) {
            $this->m_data['fk_language_id'] = $p_languageId;
        }
        if (is_numeric($p_authorId)) {
            $this->m_data['fk_author_id'] = $p_authorId;
        }
        if (!is_null($p_articleNumber) && !is_null($p_languageId)
        && !is_null($p_authorId)) {
            $this->fetch();
        }
    } // constructor


    /**
     * @return int
     */
    public function getArticleNumber()
    {
        return $this->m_data['fk_article_number'];
    } // fn getArticleNumber


    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->m_data['fk_language_id'];
    } // fn getLanguageId


    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->m_data['fk_author_id'];
    } // fn getAuthorId


    /**
     * Get all the authors that wrote this article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return array $returnArray
     *      An array of Author objects
     */
    public static function GetAuthorsByArticle($p_articleNumber,
                                               $p_languageId = null)
    {
        global $g_ado_db;

        if (is_null($p_languageId)) {
            $langConstraint = "FALSE";
        } else {
            $langConstraint = "fk_language_id = $p_languageId";
        }
        $queryStr = "SELECT fk_author_id
                     FROM ArticleAuthors
                     WHERE fk_article_number = '$p_articleNumber'
                     AND (fk_language_id IS NULL OR $langConstraint)
                     ORDER BY order_no";
        $rows = $g_ado_db->GetAll($queryStr);
        $returnArray = array();
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $author = new Author($row['fk_author_id']);
                if ($author->exists()) {
                	$returnArray[] = $author;
                }
            }
        }

		return $returnArray;
    } // fn GetAuthorsByArticle


    /**
     *
     * @param int $p_id
     *
     * @return void
     */
    public static function OnAuthorDelete($p_id)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAuthors
                     WHERE fk_author_id = '$p_id'";
        $g_ado_db->Execute($queryStr);
    } // fn OnAuthorDelete


    /**
     * Remove author pointers for the given article.
     *
     * @param int $p_articleNumber
     *
     * @return void
     */
    public static function OnArticleDelete($p_articleNumber)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAuthors
                     WHERE fk_article_number = '$p_articleNumber'";
        $g_ado_db->Execute($queryStr);
    } // fn OnArticleDelete


    /**
     * Copy all the pointers for the given article.
     *
     * @param int $p_srcArticleNumber
     * @param int $p_destArticleNumber
     *
     * @return void
     */
    public static function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
    {
        global $g_ado_db;

        $queryStr = "SELECT fk_language_id, fk_author_id
                     FROM ArticleAuthors
                     WHERE fk_article_number='$p_srcArticleNumber'";
        $rows = $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $queryStr = "INSERT IGNORE INTO ArticleAuthors
                         (fk_article_number, fk_language_id, fk_author_id)
                         VALUES ('$p_destArticleNumber', '"
                        .$row['fk_language_id']."', '"
                        .$row['fk_author_id']."')";
            $g_ado_db->Execute($queryStr);
        }
    } // fn OnArticleCopy


    /**
     * Returns an article authors list based on the given parameters.
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
     * @return array $articleAuthorsList
     *    An array of Author objects
     */
    public static function GetList(array $p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;

        if (CampCache::IsEnabled()) {
        	$paramsArray['parameters'] = serialize($p_parameters);
        	$paramsArray['order'] = (is_null($p_order)) ? 'null' : $p_order;
        	$paramsArray['start'] = $p_start;
        	$paramsArray['limit'] = $p_limit;
        	$cacheListObj = new CampCacheList($paramsArray, __CLASS__);
        	$articleAuthorsList = $cacheListObj->fetchFromCache();
        	if ($articleAuthorsList !== false
        	&& is_array($articleAuthorsList)) {
        		return $articleAuthorsList;
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

            switch (key($comparisonOperation)) {
            case 'fk_article_number':
                $whereCondition = 'fk_article_number = '
                    .$comparisonOperation['fk_article_number'];
                $hasArticleNr = true;
                break;
            case 'fk_language_id':
                $whereCondition = '(fk_language_id IS NULL OR '
                    .'fk_language_id = '.$comparisonOperation['fk_language_id'].')';
                break;
            }

            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // validates whether article number was given
        if ($hasArticleNr == false) {
            CampTemplate::singleton()->trigger_error("missed parameter Article Number in statement list_article_authors");
        }

        // sets the base table ArticleAuthors and the column to be fetched
        $tmpArticleAuthor = new ArticleAuthor();
        $selectClauseObj->setTable($tmpArticleAuthor->getDbTableName());
        $selectClauseObj->addColumn('fk_author_id');
        $countClauseObj->setTable($tmpArticleAuthor->getDbTableName());
        $countClauseObj->addColumn('COUNT(*)');
        unset($tmpArticleAuthor);

        if (!is_array($p_order)) {
            $p_order = array();
        }

        // sets the order condition if any
        foreach ($p_order as $orderColumn => $orderDirection) {
            $selectClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        // sets the limit
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the query and executes it
        $selectQuery = $selectClauseObj->buildQuery();
        $authors = $g_ado_db->GetAll($selectQuery);
        if (is_array($authors)) {
        	$countQuery = $countClauseObj->buildQuery();
        	$p_count = $g_ado_db->GetOne($countQuery);

        	// builds the array of attachment objects
        	$articleAuthorsList = array();
        	foreach ($authors as $author) {
        		$authorObj = new Author($author['fk_author_id']);
        		if ($authorObj->exists()) {
        			$articleAuthorsList[] = $authorObj;
        		}
        	}
        } else {
        	$articleAuthorsList = array();
        	$p_count = 0;
        }
        if (CampCache::IsEnabled()) {
        	$cacheListObj->storeInCache($articleAuthorsList);
        }

        return $articleAuthorsList;
    } // fn GetList


    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $parameter
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
        $parameter = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'article_number':
            $parameter['fk_article_number'] = (int) $p_param->getRightOperand();
            break;
        case 'language_id':
            $parameter['fk_language_id'] = (int) $p_param->getRightOperand();
            break;
        }

        return $parameter;
    } // fn ProcessListParameters

} // class ArticleAuthor

?>