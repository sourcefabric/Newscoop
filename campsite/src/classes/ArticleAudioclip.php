<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Audioclip.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');

/**
 * @package Campsite
 */
class ArticleAudioclip extends DatabaseObject {
    var $m_keyColumnNames = array('fk_article_number', 'fk_audioclip_gunid');
    var $m_dbTableName = 'ArticleAudioclips';
    var $m_columnNames = array('fk_article_number',
                               'fk_audioclip_gunid',
                               'fk_language_id',
                               'order_no');

    /**
     * The article audioclip table links together articles with Audioclips.
     *
     * @param int $p_articleNumber
     * @param int $p_audioclipGunId
     *
     * @return object ArticleAudioclip
     */
    public function ArticleAudioclip($p_articleNumber = null, $p_audioclipGunId = null)
    {
        if (is_numeric($p_articleNumber)) {
            $this->m_data['fk_article_number'] = $p_articleNumber;
        }
        if (!is_null($p_audioclipGunId)) {
            $this->m_data['fk_audioclip_gunid'] = $p_audioclipGunId;
        }
        if (!is_null($p_articleNumber) && !is_null($p_audioclipGunId)) {
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
     * @return string
     */
    public function getAudioclipGunId()
    {
        return $this->m_data['fk_audioclip_gunid'];
    } // fn getAudioclipGunId


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
    public function getAudioclipOrder()
    {
        return $this->m_data['order_no'];
    } // fn getAudioclipOrder


    /**
     * Sets the order for the article audioclip
     *
     * @param int $p_orderNo
     *      The order number to be set
     */
    public function setOrder($p_orderNo)
    {
        global $g_ado_db;

        if (!$this->m_exists) {
            return false;
        }
        $queryStr = "UPDATE ".$this->m_dbTableName."
                     SET order_no = '".intval($p_orderNo)."' "
                   ."WHERE fk_article_number = '".$this->getArticleNumber()."' "
                   ."AND fk_audioclip_gunid = '".$g_ado_db->escape($this->getAudioclipGunId())."'";
        $g_ado_db->Execute($queryStr);
    } // fn setOrder


    /**
     * Get all the audioclips that belong to this article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return array $returnArray
     *      An array of AudioclipMetadataEntry objects
     */
    public static function GetAudioclipsByArticleNumber($p_articleNumber,
                                                        $p_languageId = null)
    {
        global $g_ado_db;

        if (is_null($p_languageId)) {
            $langConstraint = "FALSE";
        } else {
            $langConstraint = "fk_language_id=$p_languageId";
        }
        $queryStr = "SELECT fk_audioclip_gunid
                     FROM ArticleAudioclips
                     WHERE fk_article_number = '$p_articleNumber'
                     AND (fk_language_id IS NULL OR $langConstraint)
                     ORDER BY order_no";
        $rows = $g_ado_db->GetAll($queryStr);
        $returnArray = array();
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $audioClip = new Audioclip($row['fk_audioclip_gunid']);
                if ($audioClip->exists()) {
                	$returnArray[] = $audioClip;
                }
            }
        }

		return $returnArray;
    } // fn GetAudioclipsByArticleNumber


    /**
     * This is called when an audioclip file is deleted.
     * It will disassociate the audioclip from all articles.
     *
     * @param int $p_gunId
     *
     * @return void
     */
    public static function OnAudioclipDelete($p_gunId)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAudioclips
                     WHERE fk_audioclip_gunid = '$p_gunId'";
        $g_ado_db->Execute($queryStr);
    } // fn OnAudioclipDelete


    /**
     * Remove audioclip pointers for the given article.
     *
     * @param int $p_articleNumber
     *
     * @return void
     */
    public static function OnArticleDelete($p_articleNumber)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAudioclips
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

        $queryStr = "SELECT fk_audioclip_gunid, order_no
                     FROM ArticleAudioclips
                     WHERE fk_article_number='$p_srcArticleNumber'";
        $rows = $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $queryStr = "INSERT IGNORE INTO ArticleAudioclips
                         (fk_article_number, fk_audioclip_gunid, order_no)
                         VALUES ('$p_destArticleNumber', '"
                        .$row['fk_audioclip_gunid']."', '"
                        .$row['order_no']."')";
            $g_ado_db->Execute($queryStr);
        }
    } // fn OnArticleCopy


    /**
     * Returns an article audioclips list based on the given parameters.
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
     * @return array $articleAudioclipsList
     *    An array of Audioclip objects
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
        	$articleAudioclipsList = $cacheListObj->fetchFromCache();
        	if ($articleAudioclipsList !== false
        	&& is_array($articleAudioclipsList)) {
        		return $articleAudioclipsList;
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
            CampTemplate::singleton()->trigger_error("missed parameter Article Number in statement list_article_audioclips");
        }

        // sets the base table ArticleAudioclips and the column to be fetched
        $tmpArticleAudioclip = new ArticleAudioclip();
        $selectClauseObj->setTable($tmpArticleAudioclip->getDbTableName());
        $selectClauseObj->addColumn('fk_audioclip_gunid');
        $countClauseObj->setTable($tmpArticleAudioclip->getDbTableName());
        $countClauseObj->addColumn('COUNT(*)');
        unset($tmpArticleAudioclip);

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
        $audioclips = $g_ado_db->GetAll($selectQuery);
        if (is_array($audioclips)) {
        	$countQuery = $countClauseObj->buildQuery();
        	$p_count = $g_ado_db->GetOne($countQuery);

        	// builds the array of attachment objects
        	$articleAudioclipsList = array();
        	foreach ($audioclips as $audioclip) {
        		$aclipObj = new Audioclip($audioclip['fk_audioclip_gunid']);
        		if ($aclipObj->exists()) {
        			$articleAudioclipsList[] = $aclipObj;
        		}
        	}
        } else {
        	$articleAudioclipsList = array();
        	$p_count = 0;
        }
        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$cacheListObj->storeInCache($articleAudioclipsList);
        }

        return $articleAudioclipsList;
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

} // class ArticleAudioclip

?>