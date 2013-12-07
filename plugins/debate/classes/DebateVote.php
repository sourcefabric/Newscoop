<?php
/**
 * @package Campsite
 */
class DebateVote extends DatabaseObject
{
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array( 'fk_debate_nr', 'fk_user_id' );

    var $m_keyIsAutoIncrement = true;

    var $m_dbTableName = 'plugin_debate_vote';

    var $m_columnNames = array
    (
        // int - vote id
        'id_vote',

        // int - debate id
        'fk_debate_nr',

        // int - answer nr per debate
        'fk_answer_nr',

        // int - user id
        'fk_user_id',

        // date - the timestamp
        'added',
        );

    private static $s_defaultOrder = array('bynumber'=>'asc');

    /**
     * Construct by passing in the primary key to access the debate answer in
     * the database.
     *
     * @param int $p_fk_language_id
     * @param int $p_fk_debate_nr
     * @param int $p_nr_answer
     */
    public function __construct( $p_fk_debate_nr = null, $p_fk_answer_nr = null, $p_fk_user_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);

        $this->m_data['fk_debate_nr'] = $p_fk_debate_nr;
        $this->m_data['fk_user_id'] = $p_fk_user_id;
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
        if (!is_null($p_fk_answer_nr)) {
            $this->m_data['fk_answer_nr'] = $p_fk_answer_nr;
        }
    } // constructor


    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    function __create($p_values = null) { return parent::create($p_values); }

    function create($values=null)
    {
        // defaults from set on construct, overridden by passed values
        if (isset($this->m_data['fk_debate_nr'])) {
            $debateNr = $this->m_data['fk_debate_nr'];
        }
        if (isset($values['debate_nr'])) {
            $debateNr = $values['debate_nr'];
        }
        if (!isset($debateNr)) {
            return false;
        }

        if (isset($this->m_data['fk_answer_nr'])) {
            $answerNr = $this->m_data['fk_answer_nr'];
        }
        if (isset($values['answer_nr']) ) {
            $answerNr = $values['answer_nr'];
        }
        if (!isset($answerNr)) {
            return false;
        }

        if (isset($this->m_data['fk_user_id'])) {
            $userId = $this->m_data['fk_user_id'];
        }
        if (isset($values['user_id'])) {
            $userId = $values['user_id'];
        }
        if (!isset($userId)) {
             return false;
        }

        $added = "NOW()";
        if (isset($values['added'])) {
            $added = "'".strftime( "%Y-%m-%d %H:%M:%S", strtotime($values['added']))."'";
        }
        $queryStr = "REPLACE INTO `".$this->m_dbTableName."`
            SET `fk_debate_nr` = '$debateNr', `fk_answer_nr` = '$answerNr', `fk_user_id` = '$userId', `added` = $added";

        global $g_ado_db;
        $g_ado_db->executeUpdate($queryStr);
        $success = ($g_ado_db->affected_rows() > 0);
        $this->m_exists = $success;
        $this->m_data[$this->m_keyColumnNames[0]] = $g_ado_db->Insert_ID();

        self::dispatchEvent("{$this->getResourceName()}.create", $this, array(
            'id' => $this->getKey(),
            'diff' => $this->m_data,
            'title' => method_exists($this, 'getName') ? $this->getName() : '',
        ));

        $this->resetCache();
        return $success;
    }

    /**
     * Delete debate from database.  This will
     * only delete one specific translation of the debate question.
     *
     * @return boolean
     */
    function delete()
    {
        // Delete from plugin_debate_answer table
        $deleted = parent::delete();

        $CampCache = CampCache::singleton();
        $CampCache->clear('user');

        return $deleted;
    } // fn delete

    /**
     * Trigger to delete votes on answer vote
     * @param int $p_fk_debate_nr
     * @param int $p_fk_answer_nr
     */
    public function OnAnswerDelete($p_fk_debate_nr, $p_fk_answer_nr)
    {
        foreach (DebateAnswer::getVotes($p_fk_debate_nr, $p_fk_answer_nr) as $vote) {
            $vote->delete();
        }
    }

    public function getId()
    {
        return $this->m_data['id_vote'];
    }

    public function getDebateNumber()
    {
        return $this->m_data['fk_debate_nr'];
    }

    public function getAnswerNumber()
    {
        return $this->getProperty('fk_answer_nr');
    }

    public function getAdded()
    {
        return $this->m_data['added'];
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

    /**
     * Get vote results
     * @param int $p_fk_debate_nr
     * @param int $p_fk_debate_lang
     * @param int $limit from end time backwards
     */
    public function getResults($p_fk_debate_nr, $p_fk_debate_lang, $limit=null, $start=null)
    {
        $debate = new Debate($p_fk_debate_lang, $p_fk_debate_nr);
        $tunit = strtolower($debate->getProperty('results_time_unit'));
        $query = "
            SELECT
                `fk_debate_nr`,
                COUNT(`id_vote`) as `vote_cnt`,
                `fk_answer_nr`,
                %s `dg`,
                UNIX_TIMESTAMP(DATE(`added`)) `time`
            FROM `plugin_debate_vote`
            WHERE `fk_debate_nr` = '$p_fk_debate_nr' %s %s
            GROUP BY `dg`, `fk_answer_nr`
            ORDER BY `dg` ASC, `fk_answer_nr` ASC";

        $sqlLimit = '';
        if (!is_null($limit)) {
            $sqlLimit = "AND UNIX_TIMESTAMP(`added`) > $limit";
        }
        $sqlStart = '';
        if (!is_null($start)) {
            $sqlStart = "AND UNIX_TIMESTAMP(`added`) < $start";
        }

        switch ($tunit) // replacements for time unit, interval and start
        {
            case 'daily' :
                $query = sprintf($query, "YEAR(added)*1000 + DAYOFYEAR(added)", $sqlLimit, $sqlStart);
                break;

            case 'weekly' :
                $query = sprintf($query, "YEAR(added)*100 + WEEKOFYEAR(added)", $sqlLimit, $sqlStart);
                break;

            case 'monthly' :
                $query = sprintf($query, "YEAR(added)*100 + MONTH(added)", $sqlLimit, $sqlStart);
                break;

            default :
                return array();
        }

        global $g_ado_db;
        $sqlr = $g_ado_db->execute($query);

        $vote_total = 0;
        $tunit = null;
        $results = $current_result = array();

        while ($row = $sqlr->fetchRow())
        {
            if ($tunit != $row['dg'])
            {
                $tunit = $row['dg'];
                $current_result['total_count'] = $vote_total;
                $vote_total = 0;
                $current_result = &$results[];
                $current_result['time'] = $row['time'];
            }
            $vote_total += $row['vote_cnt'];
            $current_result[] = array( 'answer_nr' => $row['fk_answer_nr'], 'value' => $row['vote_cnt'] );
        };
        $current_result['total_count'] = $vote_total;

        return $results;
    }

    public function getUserVotes($p_debate_nr, $p_user_id)
    {
        $query = "SELECT * FROM plugin_debate_vote
            WHERE `fk_user_id` = '$p_user_id' AND `fk_debate_nr` = '$p_debate_nr'";
        global $g_ado_db;
        $sqlr = $g_ado_db->execute($query);
        $return = array();
        while ($row = $sqlr->fetchRow()) {
            if (!isset($return[$row['fk_answer_nr']])) {
                $return[$row['fk_answer_nr']] = 0;
            }
            $return[$row['fk_answer_nr']] += 1;
        }
        return $return;
    }


    /////////////////// Special template engine methods below here /////////////////////////////

    /**
     * Gets an issue list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_count
     *    The count of answers.
     *
     * @return array $issuesList
     *    An array of Issue objects
     */
    public static function GetList(array $p_parameters, $p_order = null, $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;
        $hasDebateNr = false;
        $hasLanguageId = fase;
        $selectClauseObj = new SQLSelectClause();

        if (!is_array($p_parameters)) {
            return null;
        }

        // adodb::selectLimit() interpretes -1 as unlimited
        if ($p_limit == 0) {
            $p_limit = -1;
        }

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (empty($comparisonOperation)) {
                continue;
            }
            if (strpos($comparisonOperation['left'], 'debate_nr') !== false) {
                $hasDebateNr = true;
            }
            if (strpos($comparisonOperation['left'], 'language_id') !== false) {
                $hasLanguageId = true;
            }
            $whereCondition = $g_ado_db->escapeOperation($comparisonOperation);
            $selectClauseObj->addWhere($whereCondition);
        }

        // validates whether publication identifier was given
        if ($hasDebateNr == false) {
            CampTemplate::singleton()->trigger_error('missed parameter Debate Number in statement list_debateanswers');
            return;
        }
        // validates whether language identifier was given
        if ($hasLanguageId == false) {
            CampTemplate::singleton()->trigger_error('missed parameter Language Identifier in statement list_debateanswers');
            return;
        }

        // sets the columns to be fetched
        $tmpDebateAnswer = new DebateAnswer();
        $columnNames = $tmpDebateAnswer->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpDebateAnswer->getDbTableName();
        $selectClauseObj->setTable($mainTblName);
        unset($tmpDebateAnswer);

        if (!is_array($p_order)) {
            $p_order = array();
        }

        // sets the ORDER BY condition
        $p_order = count($p_order) > 0 ? $p_order : DebateAnswer::$s_defaultOrder;
        $order = DebateAnswer::ProcessListOrder($p_order);
        foreach ($order as $orderColumn => $orderDirection) {
            $selectClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        $sqlQuery = $selectClauseObj->buildQuery();

        // count all available results
        $countRes = $g_ado_db->Execute($sqlQuery);
        $p_count = $countRes->recordCount();

        //get the wanted rows
        $debateAnswerRes = $g_ado_db->Execute($sqlQuery);

        // builds the array of debate objects
        $debateAnswersList = array();
        while ($debateAnswer = $debateAnswerRes->FetchRow()) {
            $debateAnswerObj = new DebateAnswer($debateAnswer['fk_language_id'], $debateAnswer['fk_debate_nr'], $debateAnswer['nr_answer']);
            if ($debateAnswerObj->exists()) {
                $debateAnswersList[] = $debateAnswerObj;
            }
        }

        return $debateAnswersList;
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

        switch (strtolower($p_param->getLeftOperand())) {
            case 'fk_debate_nr':
                $comparisonOperation['left'] = 'fk_debate_nr';
            break;
            case 'fk_language_id':
                $comparisonOperation['left'] = 'fk_language_id';
            break;
            case 'onhitlist':
                $comparisonOperation['left'] = 'on_hitlist';
            break;
        }

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
                    $dbField = 'nr_answer';
                    break;
                case 'byanswer':
                    $dbField = 'answer';
                    break;
                case 'byvotes':
                    $dbField = 'nr_of_votes';
                    break;
                case 'bypercentage':
                    $dbField = 'percentage';
                    break;
                case 'bypercentage_overall':
                    $dbField = 'percentage_overall';
                    break;
                case 'byvalue':
                    $dbField = 'value';
                    break;
                case 'byaverage_value':
                    $dbField = 'average_value';
                    break;
                case 'bylastmodified':
                    $dbField = 'last_modified';
                    break;
                default:
                    $dbField = 'nr_answer';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }
} // class DebateAnswer
