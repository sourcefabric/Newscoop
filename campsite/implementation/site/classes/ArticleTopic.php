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
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/SQLSelectClause.php');
require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/Topic.php');
require_once($g_documentRoot.'/classes/Log.php');

/**
 * @package Campsite
 */
class ArticleTopic extends DatabaseObject {
	var $m_keyColumnNames = array('NrArticle','TopicId');
	var $m_dbTableName = 'ArticleTopics';
	var $m_columnNames = array('NrArticle', 'TopicId');

	function ArticleTopic()
	{
		parent::DatabaseObject($this->m_columnNames);
	} // constructor

	/**
	 * @return int
	 */
	function getTopicId()
	{
		return $this->m_data['TopicId'];
	} // fn getTopicId


	/**
	 * @return int
	 */
	function getArticleNumber()
	{
		return $this->m_data['NrArticle'];
	} // fn getArticleNumber


	/**
	 * Link a topic to an article.
	 * @param int $p_topicId
	 * @param int $p_articleNumber
	 * @return void
	 */
	function AddTopicToArticle($p_topicId, $p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'INSERT IGNORE INTO ArticleTopics(NrArticle, TopicId)'
					.' VALUES('.$p_articleNumber.', '.$p_topicId.')';
		$g_ado_db->Execute($queryStr);
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('Topic $1 added to article', $p_topicId);
		Log::Message($logtext, null, 144);
	} // fn AddTopicToArticle


	/**
	 * Unlink a topic from an article.
	 * @param int $p_topicId
	 * @param int $p_articleNumber
	 * @return void
	 */
	function RemoveTopicFromArticle($p_topicId, $p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = "DELETE FROM ArticleTopics WHERE NrArticle=$p_articleNumber AND TopicId=$p_topicId";
		$g_ado_db->Execute($queryStr);
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('Article topic $1 deleted', $p_topicId);
		Log::Message($logtext, null, 145);
	} // fn RemoveTopicFromArticle


	/**
	 * Remove topic pointers for the given article.
	 * @param int $p_articleNumber
	 * @return void
	 */
	function OnArticleDelete($p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'DELETE FROM ArticleTopics'
					." WHERE NrArticle='".$p_articleNumber."'";
		$g_ado_db->Execute($queryStr);
	} // fn OnArticleDelete


	/**
	 * Copy the topic pointers
	 * @param int $p_srcArticleNumber
	 * @param int $p_destArticleNumber
	 * @return void
	 */
	function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
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
	 * @param int $p_articleNumber
	 *		Retrieve the topics for this article.
	 * @param boolean $p_countOnly
	 * 		Only get the number of topics attached to the article.
	 *
	 * @return mixed
	 * 		Return an array or an int.
	 */
	function GetArticleTopics($p_articleNumber, $p_countOnly = false)
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
			foreach ($rows as $row) {
				$topics[] =& new Topic($row['TopicId']);
			}
			return $topics;
		}
	} // fn GetArticleTopics


	/**
	 * Get the Articles that have the given Topic.
	 * @param int $p_topicId
	 * @return array
	 */
	function GetArticlesWithTopic($p_topicId)
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
		$tmpArticle =& new Article();
		$columnNames = implode(',', $tmpArticle->getColumnNames(true));
		$queryStr = "SELECT $columnNames FROM Articles WHERE Number IN ("
					. implode(', ', $articleIds) . ")";
    	return DbObjectArray::Create('Article', $queryStr);
	} // fn GetArticlesWithTopic


    /**
     * Gets an article topics list based on the given parameters.
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
     *    Returns the total number of elements
     *
     * @return array $articleTopicsList
     *    An array of Topic objects
     */
    public static function GetList($p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count = 0)
    {
        global $g_ado_db;

        if (!is_array($p_parameters)) {
            return null;
        }

        $sqlClauseObj = new SQLSelectClause();

        // processes the parameters
        foreach ($p_parameters as $parameter) {
            $comparisonOperation = self::ProcessListParameters($parameter);
            if (sizeof($comparisonOperation) < 1) {
                break;
            }

            if (strpos($comparisonOperation['left'], 'NrArticle') !== false) {
                $hasArticleNr = true;
            }
            $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $comparisonOperation['right'] . "' ";
            $sqlClauseObj->addWhere($whereCondition);
        }

        // validates whether article number was given
        if ($hasArticleNr == false) {
            CampTemplate::singleton()->trigger_error("missed parameter Article Number in statement list_article_topics");
            return array();
        }

        // sets the main table and columns to be fetched
        $tmpArticleTopic = new ArticleTopic();
        $sqlClauseObj->setTable($tmpArticleTopic->getDbTableName());
        $sqlClauseObj->addColumn('TopicId');
        unset($tmpArticleTopic);

        if (!is_array($p_order)) {
            $p_order = array();
        }

        // sets the order condition if any
        foreach ($p_order as $orderColumn => $orderDirection) {
            $sqlClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        // sets the limit
        $sqlClauseObj->setLimit($p_start, $p_limit);

        // builds the query and executes it
        $sqlQuery = $sqlClauseObj->buildQuery();
        $topics = $g_ado_db->GetAll($sqlQuery);
        if (!is_array($topics)) {
            return array();
        }

        // sets the counter to zero
        $p_count = 0;
        // builds the array of topic objects
        $articleTopicsList = array();
        foreach ($topics as $topic) {
            $topObj = new Topic($topic['TopicId']);
            if ($topObj->exists()) {
                $articleTopicsList[] = $topObj;
                $p_count++;
            }
        }

        return $articleTopicsList;
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
            $comparisonOperation['left'] = 'NrArticle';
            $comparisonOperation['right'] = (int) $p_param->getRightOperand();
            break;
        }

        $operatorObj = $p_param->getOperator();
        $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');

        return $comparisonOperation;
    } // fn ProcessListParameters

} // class ArticleTopic

?>