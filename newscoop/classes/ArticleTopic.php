<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');

use Newscoop\NewscoopBundle\Entity\Topic;

/**
 * @package Campsite
 */
class ArticleTopic extends DatabaseObject
{
    public $m_keyColumnNames = array('NrArticle','TopicId');
    public $m_dbTableName = 'ArticleTopics';
    public $m_columnNames = array('NrArticle', 'TopicId');

    public function ArticleTopic()
    {
        parent::DatabaseObject($this->m_columnNames);
    } // constructor

    /**
     * @return int
     */
    public function getTopicId()
    {
        return $this->m_data['TopicId'];
    } // fn getTopicId

    /**
     * @return int
     */
    public function getArticleNumber()
    {
        return $this->m_data['NrArticle'];
    } // fn getArticleNumber

    /**
     * Link a topic to an article.
     * @param  int  $p_topicId
     * @param  int  $p_articleNumber
     * @return void
     */
    public static function AddTopicToArticle($p_topicId, $p_articleNumber)
    {
        global $g_ado_db;
        $queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
                    .' VALUES('.$p_articleNumber.', '.$p_topicId.')';
        $g_ado_db->Execute($queryStr);
    } // fn AddTopicToArticle

    /**
     * Unlink a topic from an article.
     * @param  int  $p_topicId
     * @param  int  $p_articleNumber
     * @return void
     */
    public static function RemoveTopicFromArticle($p_topicId, $p_articleNumber)
    {
        global $g_ado_db;
        $queryStr = "DELETE FROM ArticleTopics WHERE NrArticle=$p_articleNumber AND TopicId=$p_topicId";
        $g_ado_db->Execute($queryStr);
    } // fn RemoveTopicFromArticle

    /**
     * Unlink a topic from all articles.
     * @param  int  $p_topicId
     * @return void
     */
    public static function RemoveTopicFromArticles($p_topicId)
    {
        global $g_ado_db;
        $queryStr = "DELETE FROM ArticleTopics WHERE TopicId=$p_topicId";
        $g_ado_db->Execute($queryStr);
    } // fn RemoveTopicFromArticles

    /**
     * Remove topic pointers for the given article.
     * @param  int  $p_articleNumber
     * @return void
     */
    public static function OnArticleDelete($p_articleNumber)
    {
        global $g_ado_db;
        $queryStr = 'DELETE FROM ArticleTopics'
                    ." WHERE NrArticle='".$p_articleNumber."'";
        $g_ado_db->Execute($queryStr);
    } // fn OnArticleDelete

    /**
     * Copy the topic pointers
     * @param  int  $p_srcArticleNumber
     * @param  int  $p_destArticleNumber
     * @return void
     */
    public static function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
    {
        global $g_ado_db;
        $queryStr = 'SELECT * FROM ArticleTopics WHERE NrArticle='.$p_srcArticleNumber;
        $rows = $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
                        ." VALUES($p_destArticleNumber, ".$row['TopicId'].")";
            $g_ado_db->Execute($queryStr);
        }
    } // fn OnArticleCopy

    /**
     * Get the topics for the given article.
     *
     * @param int     $p_articleNumber
     *                                 Retrieve the topics for this article.
     * @param boolean $p_countOnly
     *                                 Only get the number of topics attached to the article.
     *
     * @return mixed
     *               Return an array or an int.
     */
    public static function GetArticleTopics($p_articleNumber, $p_countOnly = false)
    {
        global $g_ado_db;
        $selectStr = "*";
        if ($p_countOnly) {
            $selectStr = "COUNT(*)";
        }
        $queryStr = "SELECT $selectStr FROM ArticleTopics "
                    ." WHERE NrArticle = $p_articleNumber"
                    .' ORDER BY TopicId';
        if ($p_countOnly) {
            return $g_ado_db->GetOne($queryStr);
        } else {
            $rows = $g_ado_db->GetAll($queryStr);
            $topics = array();
            $em = \Zend_Registry::get('container')->getService('em');
            foreach ($rows as $row) {
                $topics[] = $em->getReference('Newscoop\NewscoopBundle\Entity\Topic', $row['TopicId']);
            }

            return $topics;
        }
    } // fn GetArticleTopics

    /**
     * Get the Articles that have the given Topic.
     * @param  int   $p_topicId
     * @return array
     */
    public static function GetArticlesWithTopic($p_topicId)
    {
        global $g_ado_db;

        $articleIds = array();
        $queryStr = "SELECT NrArticle FROM ArticleTopics WHERE Topicid = $p_topicId";
        $rows = $g_ado_db->GetAll($queryStr);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $articleIds[] = $row['NrArticle'];
            }
        }

        $queryStr = 'SELECT DISTINCT(ArticleType) FROM TopicFields';
        $rows = $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $queryStr = "SELECT FieldName FROM TopicFields WHERE ArticleType = '"
                        . $row['ArticleType'] . "'";
            $rows2 = $g_ado_db->GetAll($queryStr);
            if (!is_array($rows2) || sizeof($rows2) == 0) {
                continue;
            }
            $columns = '';
            foreach ($rows2 as $row2) {
                $columns .= " OR F" . $row2['FieldName'] . " = $p_topicId";
            }
            $columns = substr($columns, 3);
            $queryStr = "SELECT DISTINCT(NrArticle) FROM X" . $row['ArticleType']
                        . " WHERE $columns";
            $rows2 = $g_ado_db->GetAll($queryStr);
            if (!is_array($rows2)) {
                continue;
            }
            foreach ($rows2 as $row2) {
                foreach ($row2 as $fieldName=>$value) {
                    $articleIds[] = $value;
                }
            }
        }

        if (sizeof($articleIds) == 0) {
            return null;
        }

        $articleIds = array_unique($articleIds);
        $tmpArticle = new Article();
        $columnNames = implode(',', $tmpArticle->getColumnNames(true));
        $queryStr = "SELECT $columnNames FROM Articles WHERE Number IN ("
                    . implode(', ', $articleIds) . ")";

        return DbObjectArray::Create('Article', $queryStr);
    } // fn GetArticlesWithTopic

    /**
     * Returns an article topics list based on the given parameters.
     *
     * @param array   $p_parameters
     *                              An array of ComparisonOperation objects
     * @param string  $p_order
     *                              An array of columns and directions to order by
     * @param integer $p_start
     *                              The record number to start the list
     * @param integer $p_limit
     *                              The offset. How many records from $p_start will be retrieved.
     * @param integer $p_count
     *                              The total count of the elements; this count is computed without
     *                              applying the start ($p_start) and limit parameters ($p_limit)
     *
     * @return array $articleTopicsList
     *               An array of Topic objects
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
            $articleTopicsList = $cacheListObj->fetchFromCache();
            if ($articleTopicsList !== false
            && is_array($articleTopicsList)) {
                return $articleTopicsList;
            }
        }

        $selectClauseObj = new SQLSelectClause();
        $countClauseObj = new SQLSelectClause();
        $rootTopicIds = array();
        // processes the parameters
        $hasArticleNr = false;
        foreach ($p_parameters as $parameter) {
            $comparisonOperation = self::ProcessListParameters($parameter);
            if (sizeof($comparisonOperation) < 1) {
                break;
            }

            if (strpos($comparisonOperation['left'], 'NrArticle') !== false) {
                $hasArticleNr = true;
            }

            if (strpos($comparisonOperation['left'], 'RootTopic') !== false) {
                $rootTopicIds[] = (int) $comparisonOperation['right'];
                continue;
            }

            $whereCondition = $g_ado_db->escapeOperation($comparisonOperation);
            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // validates whether article number was given
        if ($hasArticleNr === false) {
            CampTemplate::singleton()->trigger_error("missed parameter Article Number in statement list_article_topics");

            return array();
        }

        if (count($rootTopicIds) > 0) {
            $subtopicsQuery = Topic::BuildSubtopicsQueryWithoutDepth($rootTopicIds);
            $whereCondition = 'TopicId IN ('.$subtopicsQuery->buildQuery().')';
            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // sets the main table and columns to be fetched
        $tmpArticleTopic = new ArticleTopic();
        $selectClauseObj->setTable($tmpArticleTopic->getDbTableName());
        $selectClauseObj->addColumn('TopicId');
        $countClauseObj->setTable($tmpArticleTopic->getDbTableName());
        $countClauseObj->addColumn('COUNT(*)');
        unset($tmpArticleTopic);

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
        $topics = $g_ado_db->GetAll($selectQuery);
        if (is_array($topics)) {
            $countQuery = $countClauseObj->buildQuery();
            $p_count = $g_ado_db->GetOne($countQuery);

            // builds the array of topic objects
            $articleTopicsList = array();
            foreach ($topics as $topic) {
                $articleTopicsList[] = $topic['TopicId'];
            }
        } else {
            $articleTopicsList = array();
            $p_count = 0;
        }
        if (!$p_skipCache && CampCache::IsEnabled()) {
            $cacheListObj->storeInCache($articleTopicsList);
        }

        return $articleTopicsList;
    } // fn GetList

    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *                       The array of parameters
     *
     * @return array $comparisonOperation;
     *               The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
        $comparisonOperation = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'nrarticle':
            $comparisonOperation['left'] = 'NrArticle';
            $comparisonOperation['right'] = (int) $p_param->getRightOperand();
            break;
        case 'roottopic':
            $comparisonOperation['left'] = 'RootTopic';
            $comparisonOperation['right'] = (int) $p_param->getRightOperand();
            break;
        }

        $operatorObj = $p_param->getOperator();
        $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');

        return $comparisonOperation;
    } // fn ProcessListParameters

} // class ArticleTopic
