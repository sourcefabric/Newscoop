<?php
/**
 * @package Campsite
 */
class DebateAnswer extends DatabaseObject
{
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('fk_debate_nr', 'fk_language_id', 'nr_answer');

    var $m_dbTableName = 'plugin_debate_answer';

    var $m_columnNames = array(
        // int - debate id
        'fk_debate_nr',

        // int - language id
        'fk_language_id',

        // int - nr of answer
        'nr_answer',

        // string - the literal answer
        'answer',

        // int - number of votes for this answer
        'nr_of_votes',

        // float - score of this answers in this language
        'percentage',

        // float - score of this answers overall languages
        'percentage_overall',

        // int - the commulative value of all votes
        'value',

        // float - value / number of votes
        'average_value',

        // timestamp - last_modified
        'last_modified'
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
    function DebateAnswer($p_fk_language_id = null, $p_fk_debate_nr = null, $p_nr_answer = null, $userId = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_language_id'] = $p_fk_language_id;
        $this->m_data['fk_debate_nr'] = $p_fk_debate_nr;
        $this->m_data['nr_answer'] = $p_nr_answer;
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
        $this->m_data['user_id'] = $userId;
        $this->setPercentageFromVotes();
    } // constructor

    /**
     * does just what it says the hard way
     */
    public function setPercentageFromVotes()
    {
        if (!$this->getDebateNumber() || !$this->getNumber()) return false;

        global $g_ado_db;

        $debateObj = new Debate($this->m_data['fk_language_id'], $this->m_data['fk_debate_nr']);
        $begin = $debateObj->getProperty('date_begin');
        $end = $debateObj->getProperty('date_end');
        $query =
        "
        	SELECT * FROM plugin_debate_vote
        	WHERE
        		`fk_debate_nr` = '".$this->getDebateNumber()."'
        		%s
        		AND `added` >= '$begin' AND `added` < '$end'
        ";
        $res = $g_ado_db->Execute(sprintf($query,"AND `fk_answer_nr` = '".$this->getNumber()."'"));
        /* @var $res ADORecordSet_mysql */
        $answers = $res->RowCount();

        $res = $g_ado_db->Execute(sprintf($query, ""));
        /* @var $res ADORecordSet_mysql */
        $total = $res->RowCount();

        $this->m_data['percentage'] = $total>0 ? 100*$answers/$total : 0;
    }

    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    function __create($p_values = null) { return parent::create($p_values); }


    /**
     * Create an debate answer in the database.  Use the SET functions to
     * change individual values.
     *
     * @param string $p_fk_default_language_id
     * @param date $p_date_begin
     * @param date $p_date_end
     * @param int $p_nr_of_answers
     * @param bool $p_is_show_after_expiration
     * @return void
     */
    function create($p_answer)
    {
        global $g_ado_db;

        if (!strlen($p_answer)) {
            return false;
        }

        // Create the record
        $values = array(
            'answer' => $p_answer
        );

        $success = parent::create($values);
        if (!$success) {
            return false;
        }

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Debate Id $1 created.', $this->m_data['IdDebate']);
        Log::Message($logtext, null, 31);
        */

        $CampCache = CampCache::singleton();
        $CampCache->clear('user');

        return true;
    } // fn create

    /**
     * Create a translation of an answer set.
     *
     * @param int $p_fk_debate_nr
     * @param int $p_source_language_id
     * @param int $p_target_language_id
     * @return Debate
     */
    function CreateTranslationSet($p_fk_debate_nr, $p_source_language_id, $p_target_language_id)
    {
        // Construct the duplicate DebateAnswer object.
        foreach (DebateAnswer::getAnswers($p_fk_debate_nr, $p_source_language_id) as $answer) {
            $answer_copy = new DebateAnswer($p_target_language_id, $p_fk_debate_nr, $answer->getNumber());
            $answer_copy->create($answer->getProperty('answer'));
        }
        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
            $this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
            $articleCopy->getTitle(), $articleCopy->getLanguageName());
        Log::Message($logtext, null, 31);
        */

        return $answer_copy;
    } // fn createTranslation

    /**
     * Create a copy of an answer set.
     *
     * @param int $p_fk_debate_nr
     * @param int $p_fk_language_id
     * @param int $p_parent_nr
     * @param array $p_answers
     * @return Debate
     */
    function CreateCopySet($p_fk_debate_nr, $p_fk_language_id, $p_parent_nr, $p_answers)
    {
        // Construct the duplicate DebateAnswer object.
        foreach ($p_answers as $answer) {
            if (isset($answer['number']) && !empty($answer['number']) && strlen($answer['text'])) {
                $answer_copy = new DebateAnswer($p_fk_language_id, $p_fk_debate_nr, $answer['number']);
                $answer_copy->create($answer['text']);

                if (isset($answer['nr_of_votes']) && !empty($answer['nr_of_votes'])) {
                    $answer_copy->setProperty('nr_of_votes', $answer['nr_of_votes']);
                }

                if (isset($answer['value']) && !empty($answer['nr_of_votes'])) {
                    $answer_copy->setProperty('value', $answer['value']);
                    $answer_copy->setProperty('average_value', $answer_copy->getProperty('value') / $answer_copy->getProperty('nr_of_votes'));
                }
            }
        }

        // Copy DebateAnswerAttachments
        DebateAnswerAttachment::CreateCopySet($p_fk_debate_nr, $p_fk_language_id, $p_parent_nr);

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
            $this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
            $articleCopy->getTitle(), $articleCopy->getLanguageName());
        Log::Message($logtext, null, 31);
        */

        return $debateAnswerCopy;
    } // fn createTranslation

    function getDebateAnswerAttachments()
    {
        $DebateAnswerAttachments = DebateAnswerAttachment::getDebateAnswerAttachments($this->getDebateNumber(), $this->getNumber());

        return $DebateAnswerAttachments;
    }


    /**
     * Delete debate from database.  This will
     * only delete one specific translation of the debate question.
     *
     * @return boolean
     */
    function delete()
    {
        // Delte DebateAnswerAttachments
        DebateAnswerAttachment::OnDebateAnswerDelete($this->getDebateNumber(), $this->getNumber());

        // Delete from plugin_debate_answer table
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

    public function OnDebateDelete($p_fk_debate_nr, $p_fk_language_id)
    {
        foreach (DebateAnswer::getAnswers($p_fk_debate_nr, $p_fk_language_id) as $answer) {
            $answer->delete();
        }
    }

    public function getVotes($p_fk_debate_nr, $p_answer_nr)
    {
        global $g_ado_db;
        if (is_null($p_fk_debate_nr) || is_null($p_answer_nr)) {
            return false;
        }
        $query = "
        	SELECT * FROM plugin_debate_answer
        	WHERE fk_debate_nr = '$p_fk_debate_nr' AND nr_answer = '$p_answer_nr'
        ";

        $votes = array();

        $res = $g_ado_db->Execute($query);
        if ($res) while ($row = $res->fetchRow()) {
            $answers[] = new DebateVote($row['fk_debate_nr'], $row['nr_answer']);
        }

        return $votes;
    }

    public function getAnswers($p_fk_debate_nr = null, $p_fk_language_id = null)
    {
        global $g_ado_db;
        $answers = array();

        if (!is_null($p_fk_debate_nr) && !is_null($p_fk_language_id)) {
            $fk_debate_nr = $p_fk_debate_nr;
            $fk_language_id = $p_fk_language_id;
        } elseif (isset($this)) {
            $fk_debate_nr = $this->m_data['fk_debate_nr'];
            $fk_language_id = $this->m_data['fk_language_id'];
        }

        if (!$fk_debate_nr || !$fk_language_id) {
            return array();
        }

        $query = "SELECT    nr_answer
                  FROM      plugin_debate_answer
                  WHERE     fk_debate_nr = $fk_debate_nr
                        AND fk_language_id = $fk_language_id
                  ORDER BY  nr_answer";

        $res = $g_ado_db->Execute($query);

        if ($res) while ($row = $res->fetchRow()) {
            $answers[] = new DebateAnswer($fk_language_id, $fk_debate_nr, $row['nr_answer']);
        }

        return $answers;
    }

    public static function SyncNrOfAnswers($p_fk_language_id, $p_fk_debate_nr)
    {
        global $g_ado_db;

        $debate = new Debate($p_fk_language_id, $p_fk_debate_nr);

        if (count($debate->getTranslations()) > 1) {
            $nr_of_answers = $debate->getProperty('nr_of_answers');

            $query = "DELETE FROM   plugin_debate_answer
                      WHERE         fk_debate_nr = $p_fk_debate_nr
                                AND fk_language_id = $p_fk_language_id
                                AND nr_answer > $nr_of_answers";
            $g_ado_db->execute($query);

            Debate::triggerStatistics($p_fk_debate_nr);
        }
    }

    public function getDebate()
    {
        $debate = new Debate($this->m_data['fk_language_id'], $this->m_data['fk_debate_nr'], $this->m_data['user_id']);

        return $debate;
    }

    public function vote($p_value = 1)
    {
        if (!settype($p_value, 'float')) {
            return false;
        }
        $debate = $this->getDebate();
        $voted = $debate->getAlreadyVoted($this->getProperty('nr_answer'));
        $debate->userVote($this->getProperty('nr_answer'));

        if (!is_null($voted) && $voted != $this->getProperty('nr_answer')) // switch votes
        {
            $otherAnswer = new DebateAnswer($this->getProperty('fk_language_id'), $this->getProperty('fk_debate_nr'), $voted);
            $otherAnswer->setProperty('nr_of_votes', $otherAnswer->getProperty('nr_of_votes')-1);
            $otherValueVotes = $otherAnswer->getProperty('value')-$p_value;
            $otherAnswer->setProperty('value', $otherValueVotes < 0 ? 0 : $otherValueVotes);
            $otherNrVotes = $otherAnswer->getProperty('nr_of_votes');
            $otherAnswer->setProperty('average_value', $otherNrVotes!=0 ? $otherAnswer->getProperty('value')/$otherNrVotes : 0);

            $this->setProperty('nr_of_votes', $this->getProperty('nr_of_votes') + 1);
            $this->setProperty('value', $this->getProperty('value') + $p_value);
            $this->setProperty('average_value', $this->getProperty('value') / $this->getProperty('nr_of_votes'));
        }
        elseif (is_null($voted)) // first vote for user or not logged in user
        {
            $this->setProperty('nr_of_votes', $this->getProperty('nr_of_votes') + 1);
            $this->setProperty('value', $this->getProperty('value') + $p_value);
            $this->setProperty('average_value', $this->getProperty('value') / $this->getProperty('nr_of_votes'));
        }

        $debate->increaseUserVoteCount();

        Debate::triggerStatistics($this->m_data['fk_debate_nr']);

    }

    public function getNumber()
    {
        return $this->m_data['nr_answer'];
    }

    public function getDebateNumber()
    {
        return $this->m_data['fk_debate_nr'];
    }

    public function getAnswer()
    {
        return $this->getProperty('answer');
    }

    public function getLanguageId()
    {
        return $this->getProperty('fk_language_id');
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
        $hasLanguageId = false;
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
            $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $g_ado_db->escape($comparisonOperation['right']) . "' ";
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

?>