<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
/**
 * @package Campsite
 */

class ContextBoxArticle extends DatabaseObject
{
    var $m_dbTableName = 'context_articles';

    var $m_keyColumnNames = array('fk_context_id', 'fk_article_no');

    var $m_columnNames = array('fk_context_id', 'fk_article_no');

    public function __construct($p_context_id = null, $fk_article_no = null) {
        parent::__construct($this->m_columnNames);
    }

    public static function saveList($p_context_id, $p_article_no_array) {
        self::removeList($p_context_id);
        self::insertList($p_context_id, array_unique($p_article_no_array));
    }

    public static function removeList($p_context_id) {
    	Global $g_ado_db;
        $queryStr = 'DELETE FROM context_articles'
                    .' WHERE fk_context_id=' . intval($p_context_id);
        $g_ado_db->executeUpdate($queryStr);
        $wasDeleted = ($g_ado_db->affected_rows());
        return $wasDeleted;
    }

    public static function insertList($p_context_id, $p_article_no_array) {
    	Global $g_ado_db;
    	foreach($p_article_no_array as $p_article_no) {
            $sql = 'INSERT INTO context_articles (fk_context_id, fk_article_no)
                    VALUES ('.intval($p_context_id).','.intval($p_article_no).')';
            $g_ado_db->Execute($sql);
    	}
    }


    /**
     * Gets an issues list based on the given parameters.
     *
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
     * @return array $issuesList
     *    An array of Issue objects
     */
    public static function GetList(array $params, $p_order = null,
    $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false)
    {
        global $g_ado_db;

        if (!$p_skipCache && CampCache::IsEnabled()) {
            $paramsArray['parameters'] = serialize($params);
            $paramsArray['order'] = (is_null($p_order)) ? 'id' : $p_order;
            $paramsArray['start'] = $p_start;
            $paramsArray['limit'] = $p_limit;
            $cacheListObj = new CampCacheList($paramsArray, __METHOD__);
            $issuesList = $cacheListObj->fetchFromCache();
            if ($issuesList !== false && is_array($issuesList)) {
                return $issuesList;
            }
        }

        if (isset($params['role']) && $params['role'] == 'child') {
            $sql = 'SELECT b.fk_article_no FROM context_boxes b, Articles a0'
                . ' WHERE a0.Number = b.fk_article_no AND '
                . ' a0.Type = "dossier" AND '
                . ' b.id IN (SELECT c.fk_context_id '
                . '     FROM Articles a, context_articles c '
                . '     WHERE c.fk_article_no = ' . $params['article']
                . '     AND a.Number = c.fk_article_no)'
                . ' ORDER BY a0.PublishDate DESC';
        } else {
            $sql = 'SELECT fk_article_no FROM context_articles'
                . ' WHERE fk_context_id = ' . $params['context_box']
                . ' ORDER BY id';
        }
        if ($p_limit > 0) {
            $sql .= ' LIMIT ' . $p_limit;
        }

        $returnArray = array();
        $rows = $g_ado_db->GetAll($sql);
        if (is_array($rows)) {
            foreach($rows as $row) {
                $returnArray[] = $row['fk_article_no'];
            }
        }

        $p_count = count($returnArray);

        return $returnArray;
    }

    /**
     * @param integer $destArticle
     */
    public static function OnArticleCopy($origArticle, $destArticle)
    {

        global $g_ado_db;

        $contextBox = new ContextBox(null, $destArticle);
        $sql = 'SELECT ca.fk_article_no as article_number
           FROM context_boxes cb, context_articles ca
           WHERE cb.id = ca.fk_context_id AND cb.fk_article_no = ' . $origArticle;
        $rows = $g_ado_db->GetAll($sql);

        foreach ($rows as $row) {
            $sql = 'INSERT IGNORE INTO context_articles (fk_context_id, fk_article_no) '
                . 'VALUES (' . $contextBox->getId() . ', ' . $row['article_number'] . ')';
            $g_ado_db->Execute($sql);
        }
    }

	/**
	 * Remove the article from any related articles list.
	 * @param int $articleNumber
	 * @return void
	 */
    public static function OnArticleDelete($articleNumber)
    {
		global $g_ado_db;

		$articleNumber = (int)$articleNumber;
		if ($articleNumber < 1) {
		    return;
		}

		$queryStr = 'DELETE FROM context_articles'
					." WHERE fk_article_no = '$articleNumber'";
		$g_ado_db->Execute($queryStr);
    }


	/**
	 * Remove the given context box articles.
	 * @param int $contextBoxId
	 * @return void
	 */
    public static function OnContextBoxDelete($contextBoxId)
    {
		global $g_ado_db;

		$contextBoxId = (int)$contextBoxId;
		if ($contextBoxId < 1) {
		    return;
		}

		$queryStr = 'DELETE FROM context_articles'
					." WHERE fk_context_id = '$contextBoxId'";
		$g_ado_db->Execute($queryStr);
    }
}
