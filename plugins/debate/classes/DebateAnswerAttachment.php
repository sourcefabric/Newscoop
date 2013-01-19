<?php
/**
 * @package Campsite
 */
class DebateAnswerAttachment extends DatabaseObject {
    /**
     * The column names used for the primary key.
     *
     * @var array
     */
    var $m_keyColumnNames = array('fk_debate_nr', 'fk_debateanswer_nr', 'fk_attachment_id');

    /**
     * Table name
     *
     * @var string
     */
    var $m_dbTableName = 'plugin_debateanswer_attachment';

    /**
     * All column names in the table
      *
     * @var array
     */
    var $m_columnNames = array(
        // int - debate nr
        'fk_debate_nr',

         // int - debate answer nr
        'fk_debateanswer_nr',

        // int - attachment id
        'fk_attachment_id'
        );

    private static $s_defaultOrder = array('byidentifier'=>'asc');

    /**
     * Construct by passing in the primary key to access the
     * debateanswer <-> attachment relations
     *
     * @param int $p_debate_nr
     * @param int $p_article_language_id
     * @param int $p_article_nr
     */
    function DebateAnswerAttachment($p_debate_nr = null, $p_debateanswer_nr = null, $p_attachment_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_debate_nr'] = $p_debate_nr;
        $this->m_data['fk_debateanswer_nr'] = $p_debateanswer_nr;
        $this->m_data['fk_attachment_id'] = $p_attachment_id;

        if ($this->keyValuesExist()) {
            $this->fetch();
        }
    } // constructor


    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    function __create($p_values = null) { return parent::create($p_values); }


    /**
     * Create an link debate <-> publication record in the database.
     *
     * @return void
     */
    function create()
    {
        global $g_ado_db;

        // Create the record
        $success = parent::create();
        if (!$success) {
            return;
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
     * Delete record from database.
     *
     * @return boolean
     */
    function delete()
    {
        // delete correspondending Attachment object if not used by other DebateAnswers
        $DebateAnswerAttachments = DebateAnswerAttachment::getDebateAnswerAttachments(null, null, $this->getProperty('fk_attachment_id'));
        if (count($DebateAnswerAttachments) === 1) {
            $DebateAnswerAttachment = current($DebateAnswerAttachments);
            $DebateAnswerAttachment->getAttachment()->delete();
        }


        // Delete record from the database
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
     * Call this if an DebateAnswer is deleted
     *
     * @param int $p_publication_id
     */
    public static function OnDebateAnswerDelete($p_debate_nr, $p_debateanswer_nr)
    {
        foreach (self::getDebateAnswerAttachments($p_debate_nr, $p_debateanswer_nr) as $record) {
            $record->delete();
        }
    }

    /**
     * Get array of DebateAnswerAttachment objects
     *
     * @param int $p_debate_nr
     * @param int $p_debateanswer_nr
     * @return array(object DebateAnswerAttachment, object DebateAnswerAttachment, ...)
     */
    public static function getDebateAnswerAttachments($p_debate_nr = null, $p_debateanswer_nr = null, $p_attachment_id = null)
    {
        global $g_ado_db;
        $DebateAnswerAttachments = array();

        $DebateAnswerAttachment = new DebateAnswerAttachment();
        $where = '';
        if (!empty($p_debate_nr)) {
            $where .= "AND fk_debate_nr = $p_debate_nr ";
        }
        if (!empty($p_debateanswer_nr)) {
            $where .= "AND fk_debateanswer_nr = $p_debateanswer_nr ";
        }
        if (!empty($p_attachment_id)) {
            $where .= "AND fk_attachment_id = $p_attachment_id ";
        }

        if (empty($where)) {
            return array();
        }

        $query = "SELECT    fk_debate_nr, fk_debateanswer_nr, fk_attachment_id
                  FROM      {$DebateAnswerAttachment->m_dbTableName}
                  WHERE     1 $where
                  ORDER BY  fk_debateanswer_nr";
        $res = $g_ado_db->execute($query);

        if ($res) while ($row = $res->fetchRow()) {
            $DebateAnswerAttachments[] = new DebateAnswerAttachment($row['fk_debate_nr'], $row['fk_debateanswer_nr'], $row['fk_attachment_id']);
        }

        return $DebateAnswerAttachments;
    }

    /**
     * Get the correspondending Attachment object
     * for an DebateAnswerAttachment
     *
     * @return object Attachment
     */
    public function getAttachment()
    {
        $Attachment = new Attachment($this->getProperty('fk_attachment_id'));

        return $Attachment;
    }

        /**
     * Create a copy of an answer set.
     *
     * @param int $p_fk_debate_nr
     * @param int $p_parent_nr
     * @param array $p_answers
     * @return Article
     */
    function CreateCopySet($p_debate_nr, $p_language_id, $p_parent_nr)
    {
        $ParentDebate = new Debate($p_language_id, $p_parent_nr);
        $parentAnswers = $ParentDebate->getAnswers();

        foreach ($parentAnswers as $ParentDebateAnswer) {
            $TargetDebateAnswer = new DebateAnswer($p_language_id,
                                               $p_debate_nr,
                                               $ParentDebateAnswer->getNumber());
            if ($TargetDebateAnswer->exists()) {
                $parentDebateAnswerAttachments = $ParentDebateAnswer->getDebateAnswerAttachments();

                foreach ($parentDebateAnswerAttachments as $ParentDebateAnswerAttachment) {
                    $TargetDebateAnswerAttachment = new DebateAnswerAttachment($p_debate_nr,
                                                                           $ParentDebateAnswerAttachment->getProperty('fk_debateanswer_nr'),
                                                                           $ParentDebateAnswerAttachment->getProperty('fk_attachment_id'));
                    $TargetDebateAnswerAttachment->create();
                }
            }
        }
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
     * @return array $debateAnswerAttachmentsList
     *    An array of Attachment objects
     */
    public static function GetList(array $p_parameters, $p_order = null, $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;

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
                $debate_nr = $comparisonOperation['right'];
            }
            if (strpos($comparisonOperation['left'], 'debateanswer_nr') !== false) {
                $debateanswer_nr = $comparisonOperation['right'];
            }
        }

        $sqlClauseObj = new SQLSelectClause();

        // sets the columns to be fetched
        $tmpDebateAnswerAttachment = new DebateAnswerAttachment($language_id, $debate_nr);
		$columnNames = $tmpDebateAnswerAttachment->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $sqlClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpDebateAnswerAttachment->getDbTableName();
        $sqlClauseObj->setTable($mainTblName);
        unset($tmpDebateAnswerAttachment);


        if (empty($debateanswer_nr) || empty($debate_nr)) {
            return;
        }

        $sqlClauseObj->addWhere("fk_debate_nr = " . $g_ado_db->escape($debate_nr));
        $sqlClauseObj->addWhere("fk_debateanswer_nr = " . $g_ado_db->escape($debateanswer_nr));

        if (!is_array($p_order)) {
            $p_order = array();
        }

        // sets the ORDER BY condition
        $p_order = count($p_order) > 0 ? $p_order : self::$s_defaultOrder;
        $order = self::ProcessListOrder($p_order);
        foreach ($order as $orderColumn => $orderDirection) {
            $sqlClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

        $sqlQuery = $sqlClauseObj->buildQuery();

        // count all available results
        $countRes = $g_ado_db->Execute($sqlQuery);
        $p_count = $countRes->recordCount();

        //get the wanted rows
        $debateAnswerAttachments = $g_ado_db->Execute($sqlQuery);

        // builds the array of debate objects
        $debateAnswerAttachmentsList = array();
        while ($debateAnswerAttachment = $debateAnswerAttachments->FetchRow()) {
            $debateAnswerAttachment = new Attachment($debateAnswerAttachment['fk_attachment_id']);
            if ($debateAnswerAttachment->exists()) {
                $debateAnswerAttachmentsList[] = $debateAnswerAttachment;
            }
        }

        return $debateAnswerAttachmentsList;
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
        case 'debate_nr':
            $comparisonOperation['left'] = 'debate_nr';
            break;
        case 'debateanswer_nr':
            $comparisonOperation['left'] = 'debateanswer_nr';
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
                case 'byidentifier':
                    $dbField = 'fk_attachment_id';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }
} // class DebateAnswerAttachment

?>
