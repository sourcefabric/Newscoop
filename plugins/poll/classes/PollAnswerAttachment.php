<?php
/**
 * @package Campsite
 */
class PollAnswerAttachment extends DatabaseObject {
    /**
     * The column names used for the primary key.
     *
     * @var array
     */
    var $m_keyColumnNames = array('fk_poll_nr', 'fk_pollanswer_nr', 'fk_attachment_id');

    /**
     * Table name
     *
     * @var string
     */
    var $m_dbTableName = 'plugin_pollanswer_attachment';

    /**
     * All column names in the table
      *
     * @var array
     */
    var $m_columnNames = array(
        // int - poll nr
        'fk_poll_nr',

         // int - poll answer nr
        'fk_pollanswer_nr',

        // int - attachment id
        'fk_attachment_id'
        );

    private static $s_defaultOrder = array('byidentifier'=>'asc');

    /**
     * Construct by passing in the primary key to access the
     * pollanswer <-> attachment relations
     *
     * @param int $p_poll_nr
     * @param int $p_article_language_id
     * @param int $p_article_nr
     */
    function PollAnswerAttachment($p_poll_nr = null, $p_pollanswer_nr = null, $p_attachment_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_poll_nr'] = $p_poll_nr;
        $this->m_data['fk_pollanswer_nr'] = $p_pollanswer_nr;
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
     * Create an link poll <-> publication record in the database.
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
        $logtext = getGS('Poll Id $1 created.', $this->m_data['IdPoll']);
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
        // delete correspondending Attachment object if not used by other PollAnswers
        $PollAnswerAttachments = PollAnswerAttachment::getPollAnswerAttachments(null, null, $this->getProperty('fk_attachment_id'));
        if (count($PollAnswerAttachments) === 1) {
            $PollAnswerAttachment = current($PollAnswerAttachments);
            $PollAnswerAttachment->getAttachment()->delete();
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
     * Call this if an PollAnswer is deleted
     *
     * @param int $p_publication_id
     */
    public static function OnPollAnswerDelete($p_poll_nr, $p_pollanswer_nr)
    {
        foreach (self::getPollAnswerAttachments($p_poll_nr, $p_pollanswer_nr) as $record) {
            $record->delete();
        }
    }

    /**
     * Get array of PollAnswerAttachment objects
     *
     * @param int $p_poll_nr
     * @param int $p_pollanswer_nr
     * @return array(object PollAnswerAttachment, object PollAnswerAttachment, ...)
     */
    public static function getPollAnswerAttachments($p_poll_nr = null, $p_pollanswer_nr = null, $p_attachment_id = null)
    {
        global $g_ado_db;
        $PollAnswerAttachments = array();

        $PollAnswerAttachment = new PollAnswerAttachment();

        if (!empty($p_poll_nr)) {
            $where .= "AND fk_poll_nr = $p_poll_nr ";
        }
        if (!empty($p_pollanswer_nr)) {
            $where .= "AND fk_pollanswer_nr = $p_pollanswer_nr ";
        }
        if (!empty($p_attachment_id)) {
            $where .= "AND fk_attachment_id = $p_attachment_id ";
        }

        if (empty($where)) {
            return array();
        }

        $query = "SELECT    fk_poll_nr, fk_pollanswer_nr, fk_attachment_id
                  FROM      {$PollAnswerAttachment->m_dbTableName}
                  WHERE     1 $where
                  ORDER BY  fk_pollanswer_nr";
        $res = $g_ado_db->execute($query);

        while ($row = $res->fetchRow()) {
            $PollAnswerAttachments[] = new PollAnswerAttachment($row['fk_poll_nr'], $row['fk_pollanswer_nr'], $row['fk_attachment_id']);
        }

        return $PollAnswerAttachments;
    }

    /**
     * Get the correspondending Attachment object
     * for an PollAnswerAttachment
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
     * @param int $p_fk_poll_nr
     * @param int $p_parent_nr
     * @param array $p_answers
     * @return Article
     */
    function CreateCopySet($p_poll_nr, $p_language_id, $p_parent_nr)
    {
        $ParentPoll = new Poll($p_language_id, $p_parent_nr);
        $parentAnswers = $ParentPoll->getAnswers();

        foreach ($parentAnswers as $ParentPollAnswer) {
            $TargetPollAnswer = new PollAnswer($p_language_id,
                                               $p_poll_nr,
                                               $ParentPollAnswer->getNumber());
            if ($TargetPollAnswer->exists()) {
                $parentPollAnswerAttachments = $ParentPollAnswer->getPollAnswerAttachments();

                foreach ($parentPollAnswerAttachments as $ParentPollAnswerAttachment) {
                    $TargetPollAnswerAttachment = new PollAnswerAttachment($p_poll_nr,
                                                                           $ParentPollAnswerAttachment->getProperty('fk_pollanswer_nr'),
                                                                           $ParentPollAnswerAttachment->getProperty('fk_attachment_id'));
                    $TargetPollAnswerAttachment->create();
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
     * @return array $pollAnswerAttachmentsList
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
            if (strpos($comparisonOperation['left'], 'poll_nr') !== false) {
                $poll_nr = $comparisonOperation['right'];
            }
            if (strpos($comparisonOperation['left'], 'pollanswer_nr') !== false) {
                $pollanswer_nr = $comparisonOperation['right'];
            }
        }

        $sqlClauseObj = new SQLSelectClause();

        // sets the columns to be fetched
        $tmpPollAnswerAttachment = new PollAnswerAttachment($language_id, $poll_nr);
		$columnNames = $tmpPollAnswerAttachment->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $sqlClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpPollAnswerAttachment->getDbTableName();
        $sqlClauseObj->setTable($mainTblName);
        unset($tmpPollAnswerAttachment);


        if (empty($pollanswer_nr) || empty($poll_nr)) {
            return;
        }

        $sqlClauseObj->addWhere("fk_poll_nr = " . $g_ado_db->escape($poll_nr));
        $sqlClauseObj->addWhere("fk_pollanswer_nr = " . $g_ado_db->escape($pollanswer_nr));

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
        $pollAnswerAttachments = $g_ado_db->Execute($sqlQuery);

        // builds the array of poll objects
        $pollAnswerAttachmentsList = array();
        while ($pollAnswerAttachment = $pollAnswerAttachments->FetchRow()) {
            $pollAnswerAttachment = new Attachment($pollAnswerAttachment['fk_attachment_id']);
            if ($pollAnswerAttachment->exists()) {
                $pollAnswerAttachmentsList[] = $pollAnswerAttachment;
            }
        }

        return $pollAnswerAttachmentsList;
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
        case 'poll_nr':
            $comparisonOperation['left'] = 'poll_nr';
            break;
        case 'pollanswer_nr':
            $comparisonOperation['left'] = 'pollanswer_nr';
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
} // class PollAnswerAttachment

?>
