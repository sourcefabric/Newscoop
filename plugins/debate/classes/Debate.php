<?php
/**
 * @package Campsite
 */
class Debate extends DatabaseObject
{
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('debate_nr', 'fk_language_id');

    var $m_keyIsAutoIncrement = false;

    var $m_dbTableName = 'plugin_debate';

    var $m_columnNames = array
    (
        // int - debate debate_nr
        'debate_nr',

        // int - default language id
        'fk_language_id',

        // int - parent debate number (0 on master)
        'parent_debate_nr',

        // boolean - is extended debate type
        'is_extended',

        // string - title in given language
        'title',

        // string - question in given language
        'question',

        // date - Date when voting time starts
        'date_begin',

        // date - Date when voting time ends
        'date_end',

        // int - Number of Answers
        'nr_of_answers',

        // int - how many votes can single user make,
        'votes_per_user',

        // int - if not logged in users can vote, by cookie
        'allow_not_logged_in',

        // enum - daily, weekly, monthly
        'results_time_unit',

        // int - number of votes in this language
        'nr_of_votes',

        // int - number of votes overall languages
        'nr_of_votes_overall',

        // float - percentage of votes in this language of overall languages
        'percentage_of_votes_overall',

        // timestamp - last_modified
        'last_modified',

        // debate reset token - for allowing clients to vote again after reset
        'reset_token',
        );

    /**
     * This indicates each debate can just voted once by a user, identicated by cookie + session var
     *
     * @var unknown_type
     */
    var $m_mode = 'single';

    private $userId = null;

    /**
     * Construct by passing in the primary key to access the debate in
     * the database.
     *
     * @param int $p_language_id
     *        Not required if debate_nr is given.
     * @param int $p_debate_nr
     *        Not required when creating an debate.
     */
    public function __construct($p_language_id = null, $p_debate_nr = null, $p_user_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);

        $this->m_data['fk_language_id'] = $p_language_id;
        $this->m_data['debate_nr'] = $p_debate_nr;
        $this->userId = $p_user_id;
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
    } // constructor


    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    private function __create($p_values = null) { return parent::create($p_values); }


    /**
     * Generate the next debate number
     *
     * @return int
     */
    protected function generatePollNumber()
    {
        global $g_ado_db;

        $query = "SELECT    MAX(debate_nr) + 1 AS number
                  FROM      plugin_debate";
        $result = $g_ado_db->execute($query);
        $row = $result->fetchRow();

        if (is_null($row['number'])) {
            return 1;
        }
        return $row['number'];
    }


    /**
     * Create an debate in the database.  Use the SET functions to
     * change individual values.
     *
     * @param date $p_date_begin
     * @param date $p_date_end
     * @param int $p_nr_of_answers
     * @param bool $p_votes_per_user
     * @return void
     */
    public function create($p_title, $p_question, $p_date_begin, $p_date_end, $p_nr_of_answers, $p_votes_per_user)
    {
        global $g_ado_db;

        if (!strlen($p_title) || !strlen($p_question) || !$p_date_begin || !$p_date_end || !$p_nr_of_answers) {
            return false;
        }

        $this->m_data['debate_nr'] = $this->generatePollNumber();

        // Create the record
        $values = array(
            'date_begin' => $p_date_begin,
            'date_end' => $p_date_end,
            'nr_of_answers' => $p_nr_of_answers,
            'title' => $p_title,
            'question' => $p_question,
            'votes_per_user' => $p_votes_per_user
        );


        $success = parent::create($values);
        if (!$success) {
            return false;
        }

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Debate Id $1 created.', $this->m_data['IdPoll']);
        Log::Message($logtext, null, 31);
        */

        $CampCache = CampCache::singleton();
        $CampCache->clear('user');

        return true;
    } // fn create

    /**
     * Create a translation of an debate.
     *
     * @param int $p_languageId
     * @param string $p_title
     * @param string $p_question
     * @return Debate
     */
    public function createTranslation($p_language_id, $p_title, $p_question)
    {
        // Construct the duplicate debate object.
        $debate_copy = new Debate();
        $debate_copy->m_data['debate_nr'] = $this->m_data['debate_nr'];
        $debate_copy->m_data['fk_language_id'] = $p_language_id;

        // Create the record
        $values = array(
            'title' => $p_title,
            'question' => $p_question,
            'date_begin' => $this->m_data['date_begin'],
            'date_end' => $this->m_data['date_end'],
            'nr_of_answers' => $this->m_data['nr_of_answers'],
            'votes_per_user' => $this->m_data['votes_per_user'],
            'is_extended' => $this->m_data['is_extended'] ? 'true' : 'false',
        );

        $success = $debate_copy->__create($values);

        if (!$success) {
            return false;
        }

        // create an set of answers
        DebateAnswer::CreateTranslationSet($this->m_data['debate_nr'], $this->m_data['fk_language_id'], $p_language_id);

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
            $this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
            $articleCopy->getTitle(), $articleCopy->getLanguageName());
        Log::Message($logtext, null, 31);
        */

        return $debate_copy;
    } // fn createTranslation


    /**
     * Create a copy of an debate.
     *
     * @param string $p_title
     * @param string $p_question
     * @param array $p_answers
     * @return Debate
     */
    public function createCopy($p_data, $p_answers)
    {
        // Construct the duplicate debate object.
        $debate_copy = new Debate();
        $debate_copy->m_data['debate_nr'] = Debate::generatePollNumber();
        $debate_copy->m_data['parent_debate_nr'] = $this->m_data['debate_nr'];
        $debate_copy->m_data['fk_language_id'] = $this->m_data['fk_language_id'];

        // Create the record
        $values = array(
            'title' => $p_data['title'],
            'question' => $p_data['question'],
            'date_begin' => $p_data['date_begin'],
            'date_end' => $p_data['date_end'],
            'nr_of_answers' => count($p_answers),
            'votes_per_user' => $p_data['votes_per_user'],
        );

        $success = $debate_copy->__create($values);

        if (!$success) {
            return false;
        }

        // create an set of answers
        DebateAnswer::CreateCopySet($debate_copy->getNumber(), $this->m_data['fk_language_id'], $this->m_data['debate_nr'], $p_answers);

        $debate_copy->triggerStatistics();

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
            $this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
            $articleCopy->getTitle(), $articleCopy->getLanguageName());
        Log::Message($logtext, null, 31);
        */

        return $debate_copy;
    } // fn createTranslation


    /**
     * Delete debate from database.  This will
     * only delete one specific translation of the debate.
     *
     * @return boolean
     */
    public function delete()
    {
        // Delete from plugin_debate_answer table
        DebateAnswer::OnDebateDelete($this->m_data['debate_nr'], $this->m_data['fk_language_id']);

        // Delete from plugin_debate_article table

        // Delete from plugin_debate_section table

        // Delete from plugin_debate_issue table

        // Delete from plugin_debate_publication table

        // Delete from plugin_debate_main table
        // note: first set votes to null, to recalculate statistics

        $this->setProperty('nr_of_votes', 0);
        $this->setProperty('nr_of_votes_overall', 0);
        $this->triggerStatistics();

        // finally delete the debate
        $deleted = parent::delete();

        /*
        if ($deleted) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('Article #$1: "$2" ($3) deleted.',
                $this->m_data['Number'], $this->m_data['Name'],    $this->getLanguageName())
                ." (".getGS("Publication")." ".$this->m_data['IdPublication'].", "
                ." ".getGS("Issue")." ".$this->m_data['NrIssue'].", "
                ." ".getGS("Section")." ".$this->m_data['NrSection'].")";
            Log::Message($logtext, null, 32);
        }
        */

        $CampCache = CampCache::singleton();
        $CampCache->clear('user');

        return $deleted;
    } // fn delete


    /**
     * Return an array of debate objects, one for each
     * type of language the article is written in.
     *
     * @param int $p_articleNumber
     *         Optional.  Use this if you call this function statically.
     *
     * @return array
     */
    public function getTranslations($p_debate_nr = null)
    {
        global $g_ado_db;

        $debate = array();

        if (!is_null($p_debate_nr)) {
            $debate_nr = $p_debate_nr;
        } elseif (isset($this)) {
            $debate_nr = $this->m_data['debate_nr'];
        } else {
            return array();
        }

        $query = "SELECT    debate_nr, fk_language_id
                  FROM      plugin_debate
                  WHERE     debate_nr = $debate_nr
                  ORDER BY  fk_language_id";
        $result = $g_ado_db->execute($query);

        while ($row = $result->FetchRow()) {
            $debates[] = new Debate($row['fk_language_id'], $row['debate_nr']);
        }

        return $debates;
    } // fn getTranslations


    /**
     * Construct query to recive debates from database
     *
     * @param int $p_language
     * @return string
     */
    static private function GetQuery($p_language = null, $p_orderBy = null)
    {
        switch ($p_orderBy) {
            case 'title':
                $orderBy = 'title ASC, debate_nr DESC, fk_language_id ASC';
            break;

            case 'begin':
                $orderBy = 'date_begin, debate_nr DESC, fk_language_id ASC';
            break;

            case 'end':
                $orderBy = 'date_end, debate_nr DESC, fk_language_id ASC';
            break;

            default:
                $orderBy = 'debate_nr DESC, fk_language_id ASC';
            break;
        }

        if (!empty($p_language)) {
            $query = "SELECT    debate_nr, fk_language_id
                      FROM      plugin_debate
                      WHERE     fk_language_id = $p_language
                      ORDER BY  $orderBy";
        } else {
            $query = "SELECT    debate_nr, fk_language_id
                      FROM      plugin_debate
                      ORDER BY  $orderBy";
        }

        return $query;
    }

    /**
     * Get an array of debate objects
     * You need to specify the language
     *
     * @param unknown_type $p_language_id
     * @param unknown_type $p_offset
     * @param unknown_type $p_limit
     * @return array
     */
    static public function getDebates($p_constraints = array(), $p_item = null, $p_offset = 0, $p_limit = 20, $p_orderBy = null, $p_filter = null)
    {
        $constraints = array();
        $operator = new Operator('is');

	    if (array_key_exists('language_id', $p_constraints) && !empty($p_constraints['language_id'])) {
    	    $comparisonOperation = new ComparisonOperation('language_id', $operator, $p_constraints['language_id']);
    	    $constraints[] = $comparisonOperation;
	    }

	    if (array_key_exists('publication_id', $p_constraints) && !empty($p_constraints['publication_id'])) {
    	    $comparisonOperation = new ComparisonOperation('_assign_publication_id', $operator, $p_constraints['publication_id']);
    	    $constraints[] = $comparisonOperation;
	    }

	    if (array_key_exists('issue_nr', $p_constraints) && !empty($p_constraints['issue_nr'])) {
    	    $comparisonOperation = new ComparisonOperation('_assign_issue_nr', $operator, $p_constraints['issue_nr']);
    	    $constraints[] = $comparisonOperation;
	    }

	    if (array_key_exists('section_nr', $p_constraints) && !empty($p_constraints['section_nr'])) {
    	    $comparisonOperation = new ComparisonOperation('_assign_section_nr', $operator, $p_constraints['section_nr']);
    	    $constraints[] = $comparisonOperation;
	    }

	    if (array_key_exists('article_nr', $p_constraints) && !empty($p_constraints['article_nr'])) {
    	    $comparisonOperation = new ComparisonOperation('_assign_article_nr', $operator, $p_constraints['article_nr']);
    	    $constraints[] = $comparisonOperation;
	    }

	    if (array_key_exists('is_extendet', $p_constraints)) {
    	    $comparisonOperation = new ComparisonOperation('is_extended', $operator, $p_constraints['is_extended']);
    	    $constraints[] = $comparisonOperation;
	    }

	    if (array_key_exists('parent_debate_nr', $p_constraints)) {
    	    $comparisonOperation = new ComparisonOperation('parent_debate_nr', $operator, $p_constraints['parent_debate_nr']);
    	    $constraints[] = $comparisonOperation;
	    }

	    $order = array($p_orderBy => 'ASC');

        return Debate::GetList($constraints, $p_item, $order, $p_offset, $p_limit, $p_count);
    }


    /**
     * Get the count for available debates
     *
     * @return int
     */
    public function countDebates($p_language_id = null)
    {
        global $g_ado_db;;

        $query   = Debate::getQuery($p_language_id);
        $res     = $g_ado_db->Execute($query);

        return $res->RecordCount();
    }


    /**
     * Get answer object for this debate by given number
     *
     * @param unknown_type $p_nr_answer
     * @return object
     */
    public function getAnswer($p_nr_answer)
    {
        $answer = new DebateAnswer($this->m_data['fk_language_id'], $this->m_data['debate_nr'], $p_nr_answer);
        return $answer;
    }

    /**
     * Get array of answer objects for an debate
     *
     * @return array
     */
    public function getAnswers()
    {
        return DebateAnswer::getAnswers($this->m_data['debate_nr'], $this->m_data['fk_language_id']);
    }

    /**
     * Get the debate number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->getProperty('debate_nr');
    }

    /**
     * Get the name/title
     *
     * @return string
     */
    public function getName()
    {
        return $this->getProperty('title');
    }

    /**
     * Get the name/title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getProperty('title');
    }

    /**
     * Get the language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->getProperty('fk_language_id');
    }

    /**
     * Get the english language name
     *
     * @return string
     */
    public function getLanguageName()
    {
        $language = new Language($this->m_data['fk_language_id']);

        return $language->getName();
    }

    public function setUserId($uid)
    {
        $this->userId = $uid;
        return $this;
    }

    /**
     * Get the english language name
     *
     * @return string
     */
    public function isExtended()
    {
        return $this->getProperty('is_extended') == 1 ? true : false;
    }

    /**
     * Update the statistic information in database
     * for all translations and all their answers
     *
     * @param int $p_debate_nr
     */
    public function triggerStatistics($p_debate_nr = null)
    {
        if (!is_null($p_debate_nr)) {
            $debate = new Debate(null, $p_debate_nr);;
        } elseif (isset($this)) {
            $debate = $this;
        }

        $votes = array();
        $nr_of_votes = array();
        $nr_of_votes_overall = 0;

        foreach ($debate->getTranslations() as $translation) {
        	$nr_of_votes[$translation->getLanguageId()] = 0;
            foreach ($translation->getAnswers() as $answer) {
                $votes[$translation->getLanguageId()][$answer->getProperty('nr_answer')] = $answer->getProperty('nr_of_votes');
                $nr_of_votes[$translation->getLanguageId()] += $answer->getProperty('nr_of_votes');
                $nr_of_votes_overall += $answer->getProperty('nr_of_votes');
            }
        }

        if ($nr_of_votes_overall) {
            foreach ($debate->getTranslations() as $translation) {
                foreach ($translation->getAnswers() as $answer) {
                    if ($nr_of_votes[$translation->getLanguageId()] > 0) {
                        $percentage = $votes[$translation->getLanguageId()][$answer->getProperty('nr_answer')] / $nr_of_votes[$translation->getLanguageId()] * 100;
                        $answer->setProperty('percentage', $percentage);
                    }

                    $percentag_overall = $votes[$translation->getLanguageId()][$answer->getProperty('nr_answer')] / $nr_of_votes_overall * 100;
                    $answer->setProperty('percentage_overall', $percentag_overall);
                }
                $translation->setProperty('nr_of_votes', $nr_of_votes[$translation->getLanguageId()]);
                $translation->setProperty('nr_of_votes_overall', $nr_of_votes_overall);
                $translation->setProperty('percentage_of_votes_overall', $nr_of_votes[$translation->getLanguageId()] / $nr_of_votes_overall * 100);
            }
        }
    }

    /**
     * Method to call parent::setProperty
     * with clening the cache.
     *
     * @param string $p_name
     * @param sring $p_value
     */
    function setProperty($p_name, $p_value)
    {
        $return = parent::setProperty($p_name, $p_value);
        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        return $return;
    }


    /////////////////// Special template engine methods below here /////////////////////////////

    /**
     * Gets an issue list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string item
     *    An indentifier which assignment should be used (publication/issue/section/article)
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     *
     * @return array $issuesList
     *    An array of Issue objects
     */
    public static function GetList(array $p_parameters, $p_item = null, $p_order = null, $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;

        if (!is_array($p_parameters)) {
            return null;
        }

        // adodb::selectLimit() interpretes -1 as unlimited
        if ($p_limit == 0) {
            $p_limit = -1;
        }

        $selectClauseObj = new SQLSelectClause();

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (empty($comparisonOperation)) {
                continue;
            }

            if (strpos($comparisonOperation['left'], '_assign_publication_id') !== false) {
                $assign_publication_id = $comparisonOperation['right'];
            } elseif (strpos($comparisonOperation['left'], '_assign_issue_nr') !== false) {
                $assign_issue_nr = $comparisonOperation['right'];
            } elseif (strpos($comparisonOperation['left'], '_assign_section_nr') !== false) {
                $assign_section_nr = $comparisonOperation['right'];
            } elseif (strpos($comparisonOperation['left'], '_assign_article_nr') !== false) {
                $assign_article_nr = $comparisonOperation['right'];
            } elseif (strpos($comparisonOperation['left'], '_current') !== false) {
                $whereCondition = "date_begin <= NOW()";
                $selectClauseObj->addWhere($whereCondition);
                $whereCondition = "date_end >= NOW()";
                $selectClauseObj->addWhere($whereCondition);
            } elseif (strpos($comparisonOperation['left'], 'language_id') !== false) {
                $language_id = $comparisonOperation['right'];
                $whereCondition = $g_ado_db->escapeOperation($comparisonOperation);
                $selectClauseObj->addWhere($whereCondition);
            } elseif (strpos($comparisonOperation['left'], 'number') !== false) {
                $whereCondition = $g_ado_db->escapeOperation($comparisonOperation);
                $selectClauseObj->addWhere($whereCondition);
            } else {
                $whereCondition = $g_ado_db->escapeOperation($comparisonOperation);
                $selectClauseObj->addWhere($whereCondition);
            }
        }

        // sets the columns to be fetched
        $tmpPoll = new Debate();
		$columnNames = $tmpPoll->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpPoll->getDbTableName();
        $selectClauseObj->setTable($mainTblName);
        unset($tmpPoll);

        switch ($p_item) {
            case 'publication':
                if (empty($assign_publication_id)) {
                    return;
                }
                $tmpAssignObj = new DebatePublication();
                $assignTblName = $tmpAssignObj->getDbTableName();
                $join = "LEFT JOIN `$assignTblName` AS j
                            ON
                            j.fk_debate_nr = `$mainTblName`.debate_nr
                            AND j.fk_publication_id = '$assign_publication_id'";
                $selectClauseObj->addJoin($join);
                $selectClauseObj->addWhere('j.fk_debate_nr IS NOT NULL');
                $selectClauseObj->setDistinct('plugin_debate.debate_nr');
            break;

            case 'issue':
                if (empty($assign_publication_id) || empty($assign_issue_nr)) {
                    return;
                }

                $tmpAssignObj = new DebateIssue();
                $assignTblName = $tmpAssignObj->getDbTableName();

                $join = "LEFT JOIN $assignTblName AS j
                            ON
                            j.fk_debate_nr = `$mainTblName`.debate_nr
                            AND j.fk_issue_nr = '$assign_issue_nr'
                            AND j.fk_publication_id = '$assign_publication_id'";

                if (isset($language_id)) {
                    $join .= " AND j.fk_issue_language_id = '$language_id'";
                }

                $selectClauseObj->addJoin($join);
                $selectClauseObj->addWhere('j.fk_debate_nr IS NOT NULL');
                $selectClauseObj->setDistinct('plugin_debate.debate_nr');
            break;

            case 'section':
                if (empty($assign_publication_id) || empty($assign_issue_nr) || empty($assign_section_nr)) {
                    return;
                }

                $tmpAssignObj = new DebateSection();
                $assignTblName = $tmpAssignObj->getDbTableName();

                $join = "LEFT JOIN `$assignTblName` AS j
                            ON
                            j.fk_debate_nr = `$mainTblName`.debate_nr
                            AND j.fk_section_nr = '$assign_section_nr'
                            AND j.fk_issue_nr = '$assign_issue_nr'
                            AND j.fk_publication_id = '$assign_publication_id'";

                if (isset($language_id)) {
                    $join .= " AND j.fk_section_language_id = '$language_id'";
                }

                $selectClauseObj->addJoin($join);
                $selectClauseObj->addWhere('j.fk_debate_nr IS NOT NULL');
                $selectClauseObj->setDistinct('plugin_debate.debate_nr');
            break;

            case 'article':
                if (empty($assign_article_nr)) {
                    return;
                }

                $tmpAssignObj = new DebateArticle();
                $assignTblName = $tmpAssignObj->getDbTableName();

                $join = "LEFT JOIN `$assignTblName` AS j
                            ON
                            j.fk_debate_nr = `$mainTblName`.debate_nr
                            AND j.fk_article_nr = '$assign_article_nr'";

                if (isset($language_id)) {
                    $join .= " AND j.fk_article_language_id = '$language_id'";
                }

                $selectClauseObj->addJoin($join);
                $selectClauseObj->addWhere('j.fk_debate_nr IS NOT NULL');
                $selectClauseObj->setDistinct('plugin_debate.debate_nr');
            break;
        }

        if (is_array($p_order)) {
            $order = Debate::ProcessListOrder($p_order);
            // sets the order condition if any
            foreach ($order as $orderField=>$orderDirection) {
                $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
            }
        }

        $sqlQuery = $selectClauseObj->buildQuery();

        // count all available results
        $countRes = $g_ado_db->Execute($sqlQuery);
        if (!is_null($countRes)) {
            $p_count = $countRes->recordCount();
        }

        //get the wanted rows
        $debateRes = $g_ado_db->SelectLimit($sqlQuery, $p_limit, $p_start);

        // builds the array of debate objects
        $debatesList = array();
        while ($debate = $debateRes->FetchRow()) {
            $debateObj = new Debate($debate['fk_language_id'], $debate['debate_nr']);
            if ($debateObj->exists()) {
                $debatesList[] = $debateObj;
            }
        }

        return $debatesList;
    } // fn GetList

    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $comparisonOperation
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters(ComparisonOperation $p_param)
    {
        $comparisonOperation = array();

        $comparisonOperation['left'] = DebateList::$s_parameters[strtolower($p_param->getLeftOperand())]['field'];

        if (isset($comparisonOperation['left'])) {
            $operatorObj = $p_param->getOperator();
            $comparisonOperation['right'] = $p_param->getRightOperand();
            $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');
        }

        return $comparisonOperation;
    } // fn ProcessListParameters

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
        foreach ($p_order as $field=>$direction) {
            $dbField = null;
            switch (strtolower($field)) {
                case 'bynumber':
                    $dbField = 'debate_nr';
                    break;
                case 'bylanguage':
                    $dbField = 'fk_language_id';
                    break;
                case 'byname':
                case 'bytitle':
                    $dbField = 'title';
                    break;
                case 'byquestion':
                    $dbField = 'question';
                    break;
                case 'bybegin':
                    $dbField = 'date_begin';
                    break;
                case 'byend':
                    $dbField = 'date_end';
                    break;
                case 'byanswers':
                    $dbField = 'nr_of_answers';
                    break;
                case 'byvotes':
                    $dbField = 'nr_of_votes';
                    break;
                case 'byvotes_overall':
                    $dbField = 'nr_of_votes_overall';
                    break;
                case 'bypercentage_overall':
                    $dbField = 'percentage_of_votes_overall';
                    break;
                case 'bylastmodified':
                    $dbField = 'last_modified';
                    break;
                default:
                    $dbField = 'debate_nr';
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }

    /**
     * Date checking for closed
     *
     * @return bool
     */
    public function isClosed()
    {
        return (strtotime($this->m_data['date_end']) > time()) ? false : true;
    }

    /**
     * Date checking for open
     *
     * @return bool
     */
    public function isStarted()
    {
        return (strtotime($this->m_data['date_begin']) > time()) ? false : true;
    }

    /**
     * Return if this debate can be voted
     * (must be within start-end interval,
     * and not been voted before by same client)
     *
     * @return boolean
     */
    public function isVotable()
    {
        if (!$this->isStarted() || $this->isClosed()) {
            return false;
        }

        if (!$this->m_data['allow_not_logged_in'])
        {
            if (is_null($this->userId)) {
                return false;
            }
            else {
                return true;
            }
        }

        if ($this->m_data['votes_per_user'] <= $this->getUserVoteCount()) {
            // check if debate was reseted
            $token = $this->getResetToken();
            $token_key = 'debate_reset_' . $token;
            if (!empty($token) && empty($_COOKIE[$token_key])) { // reset client count
                $this->increaseUserVoteCount(0);
                setcookie($token_key, time(), time() + 60 * 60 * 24 * 365);
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Increate counter debate has been voted by single client
     * @param int $force_value
     * @return void
     */
    public function increaseUserVoteCount($force_value = NULL)
    {
        $key = 'debate_'.$this->m_data['fk_language_id'].'_'.$this->m_data['debate_nr'];
        $value = $this->getUserVoteCount() + 1;
        if ($force_value !== NULL) {
            $value = (int) $force_value;
        }

        $_SESSION[$key] = $value;

        preg_match('/(https?:\/\/)?([-_.\w]+)(:\d+)?$/', $_SERVER['SERVER_NAME'], $hostname);
        setcookie($key, $value, time()+60*60*24*365, '/', $hostname[2]);
    }

    private $alreadyVoted = null;

    public function userVote($answer_nr)
    {
        if (!$this->m_data['allow_not_logged_in'])
        {
            $vote = new DebateVote($this->m_data['debate_nr'], $answer_nr, $this->userId);
            if (is_numeric($vote->m_data['id_vote'])) {
                $this->alreadyVoted = $vote->m_data['fk_answer_nr'];
            }
            $vote->create();
        }
        return false;
    }

    public function alreadyVoted()
    {
        return $this->alreadyVoted;
    }

    public function getAlreadyVoted($answer_nr)
    {
        if (!$this->m_data['allow_not_logged_in'])
        {
            // TODO duplicate fetch call...
            $vote = new DebateVote($this->m_data['debate_nr'], $answer_nr, $this->userId);
            $vote->fetch();
            // ---

            if (is_numeric($vote->m_data['id_vote'])) {
                $this->alreadyVoted = $vote->m_data['fk_answer_nr'];
            }
        }
        return $this->alreadyVoted;
    }

    /**
     * Return counter single client has votes this debate
     *
     * @return int
     */
    public function getUserVoteCount()
    {
        if ($this->m_data['allow_not_logged_in']) // in this case we search for cookie value
        {
            $key = 'debate_'.$this->m_data['fk_language_id'].'_'.$this->m_data['debate_nr'];

            if (array_key_exists($key, $_COOKIE)) {
                return $_COOKIE[$key];
            }
            if (array_key_exists($key, $_SESSION)) {
                return $_SESSION[$key];
            }
        }
        else // else for user id
        {
            if (is_null($this->userId)) {
                return false;
            };
            $votes = DebateVote::getUserVotes($this->m_data['debate_nr'], $this->userId);
            if (empty($votes)) {
                return 0;
            };
            $total = 0;
            foreach ($votes as $vote) {
                $total += $vote;
            }
            return $total;
        }

        return 0;
    }

    /**
     * Get reset token
     * @return string
     */
    public function getResetToken()
    {
        return (string) $this->m_data['reset_token'];
    }

    /**
     * Reset all counters
     * @return void
     */
    public function reset()
    {
        DebateVote::deleteByDebate($this->getNumber());
        foreach ($this->getAnswers() as $PollAnswer) {
            $PollAnswer->setProperty('nr_of_votes', 0);
            $PollAnswer->setProperty('percentage', 0);
            $PollAnswer->setProperty('percentage_overall', 0);
            $PollAnswer->setProperty('value', 0);
            $PollAnswer->setProperty('average_value', 0);

            $this->setProperty('reset_token', sha1(uniqid()));
        }
        $this->triggerStatistics();
    }
}
