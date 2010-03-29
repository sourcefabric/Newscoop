<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');

/**
 * @package Campsite
 */
class ArticleIndex extends DatabaseObject {
	var $m_keyColumnNames = array(
		'IdPublication',
		'IdLanguage',
		'IdKeyword',
		'NrIssue',
		'NrSection',
		'NrArticle');
	var $m_dbTableName = 'ArticleIndex';
	var $m_columnNames = array(
		'IdPublication',
		'IdLanguage',
		'IdKeyword',
		'NrIssue',
		'NrSection',
		'NrArticle');

	public function ArticleIndex()
	{
		parent::DatabaseObject($this->m_columnNames);
	} // constructor


	/**
	 * @return int
	 */
	public function getArticleNumber()
	{
		return $this->m_data['NrArticle'];
	} // fn getArticleNumber


	public static function SearchQuery($p_searchPhrase)
	{
	    global $g_ado_db;

	    $matchAll = false;
        $keywords = preg_split('/[\s,.-]/', $p_searchPhrase);
        if (isset($keywords[0]) && strtolower($keywords[0]) == '__match_all') {
            $matchAll = true;
            array_shift($keywords);
        }

        // set search keywords
        if ($matchAll && count($keywords) > 1) {
            $selectKeywordClauseObj = new SQLSelectClause();
            $selectKeywordClauseObj->addColumn('DISTINCT AI1.NrArticle');
            $selectKeywordClauseObj->addColumn('AI1.IdLanguage');
            $selectKeywordClauseObj->setTable('ArticleIndex AS AI1');
            $selectKeywordClauseObj->addJoin('LEFT JOIN KeywordIndex AS KI1 ON AI1.IdKeyword = KI1.Id');
            for ($tableIndex = 2; $tableIndex <= count($keywords); $tableIndex++) {
                $selectKeywordClauseObj->addJoin("LEFT JOIN ArticleIndex AS AI$tableIndex "
                    . "ON AI1.NrArticle = AI$tableIndex.NrArticle "
                    . "AND AI1.IdLanguage = AI$tableIndex.IdLanguage");
                $selectKeywordClauseObj->addJoin("LEFT JOIN KeywordIndex AS KI$tableIndex "
                    . "ON AI$tableIndex.IdKeyword = KI$tableIndex.Id");
            }

            $tableIndex = 1;
            foreach ($keywords as $keyword) {
                $keywordConstraint = "KI$tableIndex.Keyword = '" . $g_ado_db->escape($keyword) . "'";
                $selectKeywordClauseObj->addWhere($keywordConstraint);
                $tableIndex++;
            }
        } else {
            $selectKeywordClauseObj = new SQLSelectClause();
            $selectKeywordClauseObj->addColumn('DISTINCT AI1.NrArticle');
            $selectKeywordClauseObj->addColumn('AI1.IdLanguage');
            $selectKeywordClauseObj->setTable('ArticleIndex AS AI1');
            $selectKeywordClauseObj->addJoin('LEFT JOIN KeywordIndex AS KI1 ON AI1.IdKeyword = KI1.Id');

            foreach ($keywords as $keyword) {
                $keywordConstraint = "KI1.Keyword = '" . $g_ado_db->escape($keyword) . "'";
                $selectKeywordClauseObj->addConditionalWhere($keywordConstraint);
            }
        }

        return $selectKeywordClauseObj->buildQuery();
	}


	/**
	 * Remove index pointers for the given article.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_sectionId
	 * @param int $p_languageId
	 * @param int $p_articleNumber
	 * @return void
	 */
	public static function OnArticleDelete($p_publicationId, $p_issueId,
	                                       $p_sectionId, $p_languageId, $p_articleNumber)
	{
		global $g_ado_db;
		$queryStr = 'DELETE FROM ArticleIndex'
					." WHERE IdPublication=$p_publicationId "
					." AND NrIssue=$p_issueId "
					." AND NrSection=$p_sectionId "
					." AND NrArticle=$p_articleNumber "
					." AND IdLanguage=$p_languageId";
		$g_ado_db->Execute($queryStr);
	} // fn OnArticleDelete


	public static function RunIndexer($p_timeLimit = null, $p_articlesLimit = null,
	$p_lastModifiedFirst = true)
	{
	    global $g_ado_db;

	    $startTime = microtime(true);

	    $sql = "CREATE TABLE IF NOT EXISTS `__index_lock` (\n"
	    . "  `id` INT  NOT NULL AUTO_INCREMENT,\n"
	    . "  PRIMARY KEY (`id`)\n"
	    . ") ENGINE = MyISAM;";
	    if (!$g_ado_db->Execute($sql)) {
	        return new PEAR_Error('Error creating lock table: ' . $g_ado_db->ErrorMsg());
	    }
//	    if (!$g_ado_db->Execute('LOCK TABLE __index_lock WRITE')) {
//	        return new PEAR_Error('Error locking index table: ' . $g_ado_db->ErrorMsg());
//	    }

	    try {
	        if ($p_lastModifiedFirst) {
	            $order = 'time_updated DESC';
	        } else {
	            $order = 'Number ASC';
	        }
	        // selects articles not yet indexed
	        $sql_query = 'SELECT IdPublication, NrIssue, NrSection, Number, '
	        . 'IdLanguage, Type, Keywords, Name, '
	        . "TRIM(CONCAT(first_name, ' ', last_name)) AS AuthorName \n"
	        . "FROM Articles as a LEFT JOIN Authors as auth \n"
	        . "  ON a.fk_default_author_id = auth.Id \n"
	        . "WHERE IsIndexed = 'N' ORDER BY $order";
	        $sql_result = $g_ado_db->GetAll($sql_query);
	        if ($sql_result === false) {
	            throw new Exception('Error selecting articles not yet indexed');
	        }

	        $total_art = count($sql_result);
	        $nr_art = 0;
	        $nr_new = 0;
	        $nr_word = 0;

	        $existing_words = array();
	        foreach ($sql_result as $row) {
	            $article['IdPublication'] = ($row['IdPublication']) ? (int)$row['IdPublication'] : 0;
	            $article['NrIssue'] = ($row['NrIssue']) ? (int)$row['NrIssue'] : 0;
	            $article['NrSection'] = ($row['NrSection']) ? (int)$row['NrSection'] : 0;
	            $article['Number'] = ($row['Number']) ? (int)$row['Number'] : 0;
	            $article['IdLanguage'] = ($row['IdLanguage']) ? (int)$row['IdLanguage'] : 0;
	            $article['Type'] = ($row['Type']) ? $row['Type'] : '';
	            $article['Keywords'] = ($row['Keywords']) ? $row['Keywords'] : '';
	            $article['Name'] = ($row['Name']) ? $row['Name'] : '';
	            $article['AuthorName'] = $row['AuthorName'];

	            // deletes from index
	            $sql_query = 'DELETE FROM ArticleIndex '
	            . 'WHERE IdPublication = ' . $article['IdPublication']
	            . ' AND IdLanguage = ' . $article['IdLanguage']
	            . ' AND NrIssue = ' . $article['NrIssue']
	            . ' AND NrSection = ' . $article['NrSection']
	            . ' AND NrArticle = ' . $article['Number'];
	            if (!$g_ado_db->Execute($sql_query)) {
	                throw new Exception('Error deleting the old article index');
	            }

	            $nr_art++;

	            $keywordsHash = array();
	            self::BuildKeywordsList($article, $keywordsHash);

	            foreach ($keywordsHash as $keyword=>$isSet) {
	                if (empty($keyword)) {
	                    continue;
	                }

	                $nr_word++;

	                if (isset($existing_words[$keyword])) {
	                    $kwd_id = $existing_words[$keyword];
	                } else {
	                    $sql_query = 'SELECT Id FROM KeywordIndex '
	                    . "WHERE Keyword = '" . $g_ado_db->escape($keyword) ."'";
	                    $kwd_id = 0 + $g_ado_db->GetOne($sql_query);
	                }
	                if ($kwd_id == 0) {
	                    $sql_query = 'SELECT MAX(Id) AS Id FROM KeywordIndex';
	                    $last_kwd_id = 0 + $g_ado_db->GetOne($sql_query);
	                    $kwd_id = $last_kwd_id + 1;

	                    // inserts in keyword list
	                    $sql_query = 'INSERT IGNORE INTO KeywordIndex '
	                    . "SET Keyword = '" . $g_ado_db->escape($keyword) . "', "
	                    . "Id = $kwd_id";
	                    if (!$g_ado_db->Execute($sql_query)) {
	                        throw new Exception('Error adding keyword');
	                    }

	                    $nr_new++;
	                }
	                $existing_words[$keyword] = $kwd_id;

	                // inserts in article index
	                $sql_query = 'INSERT IGNORE INTO ArticleIndex '
	                . 'SET IdPublication = ' . $article['IdPublication']
	                . ', IdLanguage = ' . $article['IdLanguage']
	                . ', IdKeyword = ' . $kwd_id
	                . ', NrIssue = ' . $article['NrIssue']
	                . ', NrSection = ' . $article['NrSection']
	                . ', NrArticle = ' . $article['Number'];
	                if (!$g_ado_db->Execute($sql_query)) {
	                    throw new Exception('Error adding article to index');
	                }
	            }

	            unset($article['Name']);
	            unset($article['Keywords']);
	            unset($article['Type']);

	            $sql_query = "UPDATE Articles SET IsIndexed = 'Y' "
	            . 'WHERE IdPublication = ' . $article['IdPublication']
	            . ' AND NrIssue = ' . $article['NrIssue']
	            . ' AND NrSection = ' . $article['NrSection']
	            . ' AND Number = ' . $article['Number']
	            . ' AND IdLanguage = ' . $article['IdLanguage'];
	            if (!$g_ado_db->Execute($sql_query)) {
	                throw new Exception('Error updating the article');
	            }

	            if ($p_articlesLimit > 0 && $nr_art >= $p_articlesLimit) {
	                break;
	            }

	            $runTime = microtime(true) - $startTime;
	            $articleTime = $runTime / $nr_art;
	            if ($p_timeLimit > 0 && $runTime >= ($p_timeLimit - $articleTime)) {
	                break;
	            }
	        }
	    } catch (Exception $ex) {
//	        $g_ado_db->Execute('UNLOCK TABLES');
            return new PEAR_Error($ex->getMessage() . ': ' . $g_ado_db->ErrorMsg());
	    }

//	    if (!$g_ado_db->Execute('UNLOCK TABLES')) {
//	        return new PEAR_Error('Error unlocking the index table: ' . $g_ado_db->ErrorMsg());
//	    }
        $totalTime = microtime(true) - $startTime;
        $articleTime = $nr_art > 0 ? $totalTime / $nr_art : 0;
	    return array('articles'=>$nr_art, 'words'=>$nr_word, 'new words'=>$nr_new,
	    'total_articles'=>$total_art, 'total_time'=>$totalTime, 'article_time'=>$articleTime);
	} // fn RunIndexer


	public static function BuildKeywordsList($p_article, array &$p_keywordsHash)
	{
	    global $g_ado_db;

	    self::ParseKeywords($p_keywordsHash, $p_article['Keywords'], false);
	    self::ParseKeywords($p_keywordsHash, $p_article['Name'], false);
	    self::ParseKeywords($p_keywordsHash, $p_article['AuthorName'], false);

	    if (empty($p_article['Type'])) {
	        return;
	    }

	    $sql_query = 'SELECT * FROM X' . $p_article['Type']
	    . ' WHERE NrArticle = ' . $p_article['Number']
	    . ' AND IdLanguage = ' . $p_article['IdLanguage'];
	    $sql_result = $g_ado_db->GetAll($sql_query);
	    if ($sql_result === false) {
	        return new PEAR_Error('Error reading article data: ' . $g_ado_db->ErrorMsg());
	    }

	    foreach ($sql_result as $row) {
	        foreach ($row as $field=>$value) {
                if (substr($field, 0, 1) == 'F' && !empty($value)) {
                    self::ParseKeywords($p_keywordsHash, $value);
                }
	        }
	    }
	} // fn BuildKeywordsList


	public static function ParseKeywords(array &$p_keywordsHash, $p_kwd, $p_isHTML = true)
	{
	    // table of characters that may be part of words (marked by 1)
	    static $t = array(
	    /*             0  1  2  3  4  5  6  7  8  9 10 11 12 13 14 15 16 17 18 19 */
	    /*  00-19  */  0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
	    /*  20-39  */  0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1,
	    /*  40-59  */  0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0,
	    /*  60-79  */  0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
	    /*  80-99  */  1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 1, 1, 1,
	    /* 100-119 */  1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
	    /* 120-139 */  1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
	    /* 140-159 */  1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
	    /* 160-179 */  1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
	    /* 180-199 */  1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1, 1,
	    /* 200-219 */  1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
	    /* 220-239 */  1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
	    /* 240-255 */  1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
	    );

	    // characters that may be part of words but can not make works by themselves
	    static $nonWordForming = array(36, 39, 45, 64, 95);

	    if (empty($p_kwd)) {
	        return false;
	    }

	    $inHTMLTag = 0;
	    $kwd_l = strlen($p_kwd);
	    $q = $p_kwd;
	    $x = 0;
	    $l = 0;
	    while ($x < $kwd_l) {
	        $w_l = 0;
	        $charCount = 0;
	        $validWord = false;
	        $splitPoints = array(0);

	        while ($x < $kwd_l && $t[ord($q[$x])]) {
	            $char = $q[$x];
	            if ($p_isHTML) {
	                self::SetHtmlTag($inHTMLTag, $char);
	            }
	            if ($inHTMLTag) {
	                // do not process HTML tags
	                $x++;
	                $w_l++;
	                continue;
	            }
	            $charOrd = ord($char);
	            // increment the letter count if an ASCII character
	            // or start of UTF-8 sequence
	            if ($charOrd < 128 || ($charOrd >= 194 && $charOrd <= 223)
	            || ($charOrd >= 224 && $charOrd <= 239)
	            || ($charOrd >= 240 && $charOrd <= 244)) {
	                $charCount++;
	            }
	            if (array_search(ord($char), $nonWordForming) === false
	            && (ord($char) < 128 || ord($char) > 191)) {
	                $validWord = true;
	            }
	            if (array_search(ord($char), $nonWordForming) !== false) {
	                $splitPoints[] = $w_l;
	            }
	            $x++;
	            $w_l++;
	        }

	        if ($w_l > 1 && $validWord) {
	            if ($inHTMLTag) {
	                continue;
	            }
	            $splitPoints[] = $w_l;
	            $word = substr($q, $l, $w_l);
	            if (is_numeric($word) && $word < 100) {
	                continue;
	            }
	            if ($charCount > 1) {
	                self::AddKeyword($p_keywordsHash, $word);
	            } else {
	                continue;
	            }
	            for ($i = 0; $i < (count($splitPoints) - 1); $i++) {
	                $splitStart = $i == 0 ? $splitPoints[0] : $splitPoints[$i] + 1;
	                $partLen = $splitPoints[$i+1] - $splitStart;
	                if ($partLen > 1) {
	                    self::AddKeyword($p_keywordsHash, substr($word, $splitStart, $partLen));
	                }
	            }
	        } else {
	            $l = $x;
	            while ($l < $kwd_l && !$t[ord(substr($q, $l, 1))]) {
	                if ($p_isHTML) {
	                    self::SetHtmlTag($inHTMLTag, $q[$l]);
	                }
	                $l++;
	            }
	            $x = $l;
	        }
	    }
	} // fn ParseKeywords


	public static function AddKeyword(array &$p_keywordsHash, $p_kwd)
	{
	    $p_kwd = trim($p_kwd);
	    if (!isset($p_keywordsHash[$p_kwd])) {
	        $p_keywordsHash[$p_kwd] = false;
	    } else {
	        $p_keywordsHash[$p_kwd] = true;
	    }
	} // fn AddKeyword


	public static function SetHtmlTag(&$p_inHTMLTag, $p_char)
	{
	    if ($p_char == '<') {
	        $p_inHTMLTag++;
	    }
	    if ($p_char == '>') {
	        $p_inHTMLTag = $p_inHTMLTag > 0 ? $p_inHTMLTag - 1 : 0;
	    }
	} // fn SetHtmlTag
} // class ArticleIndex

?>