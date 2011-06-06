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
require_once($GLOBALS['g_campsiteDir'].'/classes/AuthorType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');


/**
 * @package Campsite
 */
class ArticleAuthor extends DatabaseObject
{
    const TABLE = 'ArticleAuthors';

    /**
     * @var string
     */
    public $m_dbTableName = self::TABLE;

    /**
     * @var array
     */
    public $m_keyColumnNames = array('fk_article_number',
                                     'fk_language_id',
                                     'fk_author_id',
                                     'fk_type_id');

    /**
     * @var array
     */
    public $m_columnNames = array('fk_article_number',
                                  'fk_language_id',
                                  'fk_author_id',
                                  'fk_type_id');

    /**
     * @var AuthorType
     */
    private $m_type = NULL;


    /**
     * The ArticleAuthors table links together articles with authors.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @param int $p_authorId
     * @param int $p_typeId
     */
    public function __construct($p_articleNumber = null, $p_languageId = null, $p_authorId = null, $p_typeId = null)
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
        if (is_numeric($p_typeId)) {
            $this->m_data['fk_type_id'] = $p_typeId;
        }
        if (!is_null($p_articleNumber) && !is_null($p_languageId)
                && !is_null($p_authorId) && !is_null($p_typeId)) {
            $this->fetch();
        }
    }

    /**
     * @return int
     */
    public function getArticleNumber()
    {
        return (int) $this->m_data['fk_article_number'];
    }

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return (int) $this->m_data['fk_language_id'];
    }


    /**
     * @return int
     */
    public function getAuthorId()
    {
        return (int) $this->m_data['fk_author_id'];
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return (int) $this->m_data['fk_type_id'];
    }

    /**
     * @return AuthorType
     */
    public function getType()
    {
        if (is_null($this->m_type)) {
            $this->m_type = new AuthorType((int) $this->m_data['fk_type_id']);
        }
        return $this->m_type;
    }

    /**
     * @param int $p_authorId
     * @return array
     */
    public static function GetArticlesByAuthor($p_authorId)
    {
        global $g_ado_db;

        $queryStr = 'SELECT fk_article_number, fk_language_id, fk_type_id
                     FROM ' . self::TABLE . '
                     WHERE fk_author_id = '. (int) $p_authorId;
        $rows = $g_ado_db->GetAll($queryStr);

        $returnArray = array();
        foreach ((array) $rows as $row) {
            $article = new Article((int) $row['fk_language_id'], (int) $row['fk_article_number']);
            $type = new AuthorType((int) $row['fk_type_id']);
            $returnArray[] = array('article' => $article, 'type' => $type);
        }
        return $returnArray;
    }

    /**
     * Get all the authors that wrote this article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return array $returnArray
     *      An array of Author objects
     */
    public static function GetAuthorsByArticle($p_articleNumber, $p_languageId = NULL)
    {
        global $g_ado_db;

        if (is_null($p_languageId)) {
            $langConstraint = "FALSE";
        } else {
            $langConstraint = "aa.fk_language_id = $p_languageId";
        }

        $queryStr = 'SELECT aa.fk_author_id, aa.fk_type_id
                     FROM ' . self::TABLE . ' AS aa
                     JOIN ' . AuthorType::TABLE . ' AS at
                     WHERE aa.fk_article_number = '. (int) $p_articleNumber . '
                     AND (aa.fk_language_id IS NULL OR ' . $langConstraint .')
                     AND aa.fk_type_id = at.id';
                     // ORDER BY order_no';
        $rows = $g_ado_db->GetAll($queryStr);
        $returnArray = array();
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $author = new Author($row['fk_author_id'], $row['fk_type_id']);
                if ($author->exists()) {
                	$returnArray[] = $author;
                }
            }
        }

		return $returnArray;
    }

    /**
     * Copy all the pointers for the given article.
     *
     * @param int $p_srcArticleNumber
     * @param int $p_destArticleNumber
     * @return void
     */
    public static function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
    {
        global $g_ado_db;

        $queryStr = 'SELECT fk_language_id, fk_author_id, fk_type_id
                     FROM ' . self::TABLE . '
                     WHERE fk_article_number = ' . (int) $p_srcArticleNumber;
        $rows = $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $tmpArticleAuthorObj = new ArticleAuthor($p_destArticleNumber,
                $row['fk_language_id'], $row['fk_author_id'], $row['fk_type_id']);
            if (!$tmpArticleAuthorObj->exists()) {
                $tmpArticleAuthorObj->create();
            }
        }
    }

    /**
     * Remove author pointers for the given article.
     *
     * @param int $p_articleNumber
     * @return void
     */
    public static function OnArticleDelete($p_articleNumber)
    {
        global $g_ado_db;

        $queryStr = 'DELETE FROM ' . self::TABLE . '
            WHERE fk_article_number = ' . (int) $p_articleNumber;
        $g_ado_db->Execute($queryStr);
    }

    /**
     * @param int $p_id
     * @return void
     */
    public static function OnArticleLanguageDelete($p_articleNumber, $p_languageId)
    {
        global $g_ado_db;

        $queryStr = 'DELETE FROM ' . self::TABLE . '
            WHERE fk_article_number = ' . (int) $p_articleNumber . '
            AND fk_language_id = ' . (int) $p_languageId;
        $g_ado_db->Execute($queryStr);
    }

    /**
     * Remove article pointers for the given author.
     * @param int $p_id
     * @return void
     */
    public static function OnAuthorDelete($p_authorId)
    {
        global $g_ado_db;

        $queryStr = 'DELETE FROM ' . self::TABLE . '
            WHERE fk_author_id = ' . (int) $p_authorId;
        $g_ado_db->Execute($queryStr);
    }

    /**
     * Remove author pointers for the given author type.
     *
     * @param int $p_authorTypeId
     * @return void
     */
    public static function OnAuthorTypeDelete($p_authorTypeId)
    {
        global $g_ado_db;

        $queryStr = 'DELETE FROM ' . self::TABLE . '
            WHERE fk_type_id = ' . (int) $p_authorTypeId;
        $g_ado_db->Execute($queryStr);
    }

    /**
     * @return array
     */
    public static function GetArticleAuthorList($p_articleNumber, $p_languageId)
    {
        global $g_ado_db;

        $sql = 'SELECT Authors.first_name, Authors.last_name, ArticleAuthors.fk_type_id
                FROM ' . Author::TABLE . ' JOIN ' . self::TABLE . '
                ON Authors.id = fk_author_id
                WHERE fk_language_id = ' . (int) $p_languageId . '
                AND fk_article_number = ' . (int) $p_articleNumber;
        return $g_ado_db->Execute($sql);
    }

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
                                   $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false)
    {
        global $g_ado_db;

        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$paramsArray['parameters'] = serialize($p_parameters);
        	$paramsArray['order'] = (is_null($p_order)) ? 'null' : $p_order;
        	$paramsArray['start'] = $p_start;
        	$paramsArray['limit'] = $p_limit;
        	$cacheListObj = new CampCacheList($paramsArray, __METHOD__);
        	$articleAuthorsList = $cacheListObj->fetchFromCache();
        	if ($articleAuthorsList !== false && is_array($articleAuthorsList)) {
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
        if ($hasArticleNr === false) {
            CampTemplate::singleton()->trigger_error("missed parameter Article Number in statement list_article_authors");
        }

        // sets the base table ArticleAuthors and the column to be fetched
        $tmpArticleAuthor = new ArticleAuthor();
        $selectClauseObj->setTable($tmpArticleAuthor->getDbTableName());
        $selectClauseObj->addJoin('JOIN ' . Author::TABLE . ' ON fk_author_id = id');
        $selectClauseObj->addColumn('fk_author_id');
        $selectClauseObj->addColumn('fk_type_id');
        $countClauseObj->setTable($tmpArticleAuthor->getDbTableName());
        $countClauseObj->addColumn('COUNT(*)');
        unset($tmpArticleAuthor);

        if (!is_array($p_order)) {
            $p_order = array();
        }

        $order = self::ProcessListOrder($p_order);
        // sets the order condition if any
        foreach ($order as $orderDesc) {
            $orderField = $orderDesc['field'];
            $orderDirection = $orderDesc['dir'];
            $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
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
        	$authorsList = array();
        	foreach ($authors as $author) {
        		$authorObj = new Author($author['fk_author_id'], $author['fk_type_id']);
        		if ($authorObj->exists()) {
        			$authorsList[] = $authorObj;
        		}
        	}
        } else {
        	$authorsList = array();
        	$p_count = 0;
        }
        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$cacheListObj->storeInCache($authorsList);
        }

        return $authorsList;
    }

    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     * @return array $parameter
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
        $parameter = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'article':
            $parameter['fk_article_number'] = (int) $p_param->getRightOperand();
            break;
        case 'language':
            $parameter['fk_language_id'] = (int) $p_param->getRightOperand();
            break;
        }

        return $parameter;
    }
    
    /**
     * Processes an order directive coming from template tags.
     *
     * @param array $p_order
     *      The array of order directives
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $orderDesc) {
            $dbField = null;
            $field = $orderDesc['field'];
            $direction = $orderDesc['dir'];
            switch (strtolower($field)) {
            case 'default':
                $dbField = 'first_name';
                break;
            case 'byfirstname':
                $dbField = 'first_name';
                break;
            case 'bylastname':
                $dbField = 'last_name';
                break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
                $order[] = array('field'=>$dbField, 'dir'=>$direction);
            }
        }
        return $order;
    }

    public static function BuildAuthorIdsQuery(array $p_names) {
        $authors_query = false;
        $author_names = array();

        foreach ($p_names as $one_name) {
            $one_name = str_replace('"', '""', trim($one_name));
            if (0 < strlen($one_name)) {
                $author_names[] = $one_name;
            }
        }

        if (0 < count($author_names)) {
            $authors_str = '"' . implode('", "', $author_names) . '"';
            $authors_query = "SELECT DISTINCT id FROM Authors WHERE trim(concat(first_name, \" \", last_name)) IN ($authors_str) OR trim(concat(last_name, \" \", first_name) IN ($authors_str))";
        }

        return $authors_query;
    } // fn BuildAuthorIdsQuery

}
