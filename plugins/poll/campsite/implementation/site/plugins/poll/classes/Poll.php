<?php
/**
 * @package Campsite
 */
class Poll extends DatabaseObject {
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('poll_nr', 'fk_language_id');
    
    var $m_keyIsAutoIncrement = false;

    var $m_dbTableName = 'plugin_poll';

    var $m_columnNames = array(
        // int - poll poll_nr
        'poll_nr',
    
        // int - default language id
        'fk_language_id',
                
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

        // bool - Display Poll after Expiration,
        'is_display_expired',
        
        // bool - Use this language if wanted translation is not available
        'is_used_as_default',
        
        // int - number of votes in this language
        'nr_of_votes',
        
        // int - number of votes overall languages
        'nr_of_votes_overall',
        
        // float - percentage of votes in this language of overall languages
        'percentage_of_votes_overall',
        
        // timestamp - last_modified
        'last_modified'
        );
        
    /**
     * This indicates if poll can just voted once
     *
     * @var unknown_type
     */
    var $m_mode = 'single';

    /**
     * Construct by passing in the primary key to access the poll in
     * the database.
     *
     * @param int $p_language_id
     *        Not required if poll_nr is given.
     * @param int $p_poll_nr
     *        Not required when creating an poll.
     */
    public function Poll($p_language_id = null, $p_poll_nr = null, $p_get_default = false)
    {
        parent::DatabaseObject($this->m_columnNames);
        
        $this->m_data['fk_language_id'] = $p_language_id;
        $this->m_data['poll_nr'] = $p_poll_nr;
        
        if ($this->keyValuesExist()) {
            $this->fetch();
            
            if (!$this->exists() && $p_get_default) {
                // try to fetch the default one  
                
                $this->m_keyColumnNames = array('poll_nr', 'is_used_as_default');
                $this->m_data['is_used_as_default'] = true;
                
                $this->fetch();   
            }
        }
    } // constructor


    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    private function __create($p_values = null) { return parent::create($p_values); }


    /**
     * Generate the next poll number
     *
     * @return int
     */
    protected function generatePollNumber()
    {
        global $g_ado_db;
        
        $query = "SELECT    MAX(poll_nr) + 1 AS number
                  FROM      plugin_poll";
        $result = $g_ado_db->execute($query);
        $row = $result->fetchRow();
        
        if (is_null($row['number'])) {
            return 1;
        }
        return $row['number'];  
    }
    
    
    /**
     * Create an poll in the database.  Use the SET functions to
     * change individual values.
     *
     * @param date $p_date_begin
     * @param date $p_date_end
     * @param int $p_nr_of_answers
     * @param bool $p_is_display_expired
     * @return void
     */
    public function create($p_title, $p_question, $p_date_begin, $p_date_end, $p_nr_of_answers, $p_is_display_expired=false, $p_is_used_as_default=true)
    {
        global $g_ado_db;
        
        if (!strlen($p_title) || !strlen($p_question) || !$p_date_begin || !$p_date_end || !$p_nr_of_answers) {
            return false;   
        }
        
        $this->m_data['poll_nr'] = $this->generatePollNumber();

        // Create the record
        $values = array(
            'date_begin' => $p_date_begin,
            'date_end' => $p_date_end,
            'nr_of_answers' => $p_nr_of_answers,
            'title' => $p_title,
            'question' => $p_question,
            'is_display_expired' => $p_is_display_expired ? 1 : 0        
        );


        $success = parent::create($values);
        if (!$success) {
            return false;
        }

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Poll Id $1 created.', $this->m_data['IdPoll']);
        Log::Message($logtext, null, 31);
        */
        
        return true;
    } // fn create

    /**
     * Create a translation of an poll.
     *
     * @param int $p_languageId
     * @param int $p_userId
     * @param string $p_name
     * @return Article
     */
    public function createTranslation($p_language_id, $p_title = '', $p_question = '', $p_is_used_as_default = false)
    {        
        // Construct the duplicate poll object.  
        $poll_copy = new Poll();      
        $poll_copy->m_data['poll_nr'] = $this->m_data['poll_nr'];
        $poll_copy->m_data['fk_language_id'] = $p_language_id;

        // Create the record
        $values = array(
            'title' => $p_title,
            'question' => $p_question,
            'date_begin' => $this->m_data['date_begin'],
            'date_end' => $this->m_data['date_end'],
            'nr_of_answers' => $this->m_data['nr_of_answers'],
            'is_display_expired' => $this->m_data['is_display_expired'],     
        );

        $success = $poll_copy->__create($values);
        
        if (!$success) {
            return false;
        }
        
        $poll_copy->setAsDefault($p_is_used_as_default);
        
        // create an set of answers
        PollAnswer::CreateTranslationSet($this->m_data['poll_nr'], $this->m_data['fk_language_id'], $p_language_id);

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
            $this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
            $articleCopy->getTitle(), $articleCopy->getLanguageName());
        Log::Message($logtext, null, 31);
        */
        
        return $poll_copy;
    } // fn createTranslation


    /**
     * Delete poll from database.  This will
     * only delete one specific translation of the poll.
     *
     * @return boolean
     */
    public function delete()
    {       
        // Delete from plugin_poll_answer table
        PollAnswer::OnPollDelete($this->m_data['poll_nr'], $this->m_data['fk_language_id']);

        // Delete from plugin_poll_article table
        
        // Delete from plugin_poll_section table
        
        // Delete from plugin_poll_issue table
        
        // Delete from plugin_poll_publication table
        
        // Delete from plugin_poll_main table
        // note: first set votes to null, to recalculate statistics

        $this->setProperty('nr_of_votes', 0);
        $this->setProperty('nr_of_votes_overall', 0);
        $this->triggerStatistics();
        
        // finally delete the poll
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
        return $deleted;
    } // fn delete


    /**
     * Return an array of poll objects, one for each
     * type of language the article is written in.
     *
     * @param int $p_articleNumber
     *         Optional.  Use this if you call this function statically.
     *
     * @return array
     */
    public function getTranslations($p_poll_nr = null)
    {
        global $g_ado_db;
        
        $poll = array();
        
        if (!is_null($p_poll_nr)) {
            $poll_nr = $p_poll_nr;
        } elseif (isset($this)) {
            $poll_nr = $this->m_data['poll_nr'];
        } else {
            return array();
        }
         
        $query = "SELECT    poll_nr, fk_language_id 
                  FROM      plugin_poll 
                  WHERE     poll_nr = $poll_nr
                  ORDER BY  fk_language_id";
        $result = $g_ado_db->execute($query);
        
        while ($row = $result->FetchRow()) { 
            $polls[] = new Poll($row['fk_language_id'], $row['poll_nr']);   
        }
        
        return $polls;
    } // fn getTranslations


    /**
     * Construct query to recive polls from database
     *
     * @param int $p_language
     * @return string
     */
    static private function GetQuery($p_language = null, $p_orderBy = null)
    {   
        switch ($p_orderBy) {
            case 'title':
                $orderBy = 'title ASC, poll_nr DESC, fk_language_id ASC'; 
            break;
            
            case 'begin':
                $orderBy = 'date_begin, poll_nr DESC, fk_language_id ASC';
            break;   
            
            case 'end':
                $orderBy = 'date_end, poll_nr DESC, fk_language_id ASC';
            break;
            
            default:
                $orderBy = 'poll_nr DESC, fk_language_id ASC';
            break;   
        }
        
        if (!empty($p_language)) {
            $query = "SELECT    poll_nr, fk_language_id  
                      FROM      plugin_poll
                      WHERE     fk_language_id = $p_language
                      ORDER BY  $orderBy";  
        } else {
            $query = "SELECT    poll_nr, fk_language_id
                      FROM      plugin_poll
                      ORDER BY  $orderBy";
        }
        
        return $query;
    }
    
    /**
     * Get an array of poll objects 
     * You need to specify the language
     *
     * @param unknown_type $p_language_id
     * @param unknown_type $p_offset
     * @param unknown_type $p_limit
     * @return array
     */
    static public function GetPolls($p_constraints = array(), $p_item = null, $p_offset = 0, $p_limit = 20, $p_orderBy = null, $p_filter = null)
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
	    	    
	    $order = array($p_orderBy => 'ASC');    

	    
        return Poll::GetList($constraints, $p_item, $order, $p_offset, $p_limit, &$p_count);
        
        
        
        global $g_ado_db;
        
        $query = Poll::GetQuery($p_language_id, $p_orderBy, $p_orderBy);
        
        $res = $g_ado_db->SelectLimit($query, $p_limit, $p_offset);		
		$polls = array();
		
		while ($row = $res->FetchRow()) { 
		    $tmp_poll = new Poll($row['fk_language_id'], $row['poll_nr']);
            $polls[] = $tmp_poll;  
		}
		
		return $polls;
    }

    
    /**
     * Get the count for available polls
     *
     * @return int
     */
    public function countPolls($p_language_id = null)
    {
        global $g_ado_db;;
        
        $query   = Poll::getQuery($p_language_id); 
        $res     = $g_ado_db->Execute($query);
        
        return $res->RecordCount();  
    }
    
        
    /**
     * Get answer object for this poll by given number
     *
     * @param unknown_type $p_nr_answer
     * @return object
     */
    public function getAnswer($p_nr_answer)
    {
        $answer = new PollAnswer($this->m_data['fk_language_id'], $this->m_data['poll_nr'], $p_nr_answer);
        return $answer;   
    }
    
    /**
     * Get array of answer objects for an poll
     *
     * @return array
     */
    public function getAnswers()
    {
        return PollAnswer::getAnswers($this->m_data['poll_nr'], $this->m_data['fk_language_id']);   
    }
    
    /**
     * Get the poll number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->getProperty('poll_nr');   
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
    
    /**
     * Mark poll as being default translation
     *
     * @param unknown_type $p_status
     */
    public function setAsDefault($p_status)
    {
        if ($p_status == true) {
            foreach ($this->getTranslations() as $translation) {
                $translation->setProperty('is_used_as_default', false);   
            }
        } 
        $this->setProperty('is_used_as_default', $p_status);    
    }
    
    /**
     * Update the statistic information in database
     * for all translations and all their answers
     *
     * @param int $p_poll_nr
     */
    public function triggerStatistics($p_poll_nr = null)
    {
        if (!is_null($p_poll_nr)) {
            $poll = new Poll(null, $p_poll_nr);;   
        } elseif (isset($this)) {
            $poll = $this;   
        }   
            
        $votes = array();
        $nr_of_votes = array();
        $nr_of_votes_overall = 0;
        
        foreach ($poll->getTranslations() as $translation) {
            foreach ($translation->getAnswers() as $answer) {
                $votes[$translation->getLanguageId()][$answer->getProperty('nr_answer')] = $answer->getProperty('nr_of_votes');
                $nr_of_votes[$translation->getLanguageId()] += $answer->getProperty('nr_of_votes'); 
                $nr_of_votes_overall += $answer->getProperty('nr_of_votes'); 
            } 
        }
        
        if ($nr_of_votes_overall) {
            foreach ($poll->getTranslations() as $translation) {
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
    public static function GetList($p_parameters, $p_item = null, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;
        
        if (!is_array($p_parameters)) {
            return null;
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

                    $whereCondition = $comparisonOperation['left'] . ' '
                    . $comparisonOperation['symbol'] . " '"
                    . $comparisonOperation['right'] . "' ";
                    $selectClauseObj->addWhere($whereCondition);
          
            } else {
                $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $comparisonOperation['right'] . "' ";
                $selectClauseObj->addWhere($whereCondition);
            }
        }
        
        // sets the columns to be fetched
        $tmpPoll = new Poll();
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
                $tmpAssignObj = new PollPublication();
                $assignTblName = $tmpAssignObj->getDbTableName();
                $join = "LEFT JOIN `$assignTblName` AS j 
                            ON 
                            j.fk_poll_nr = `$mainTblName`.poll_nr
                            AND j.fk_publication_id = '$assign_publication_id'";
                $selectClauseObj->addJoin($join);
                $selectClauseObj->addWhere('j.fk_poll_nr IS NOT NULL');
                $selectClauseObj->setDistinct('plugin_poll.poll_nr');
            break;
            
            case 'issue':
                if (empty($assign_publication_id) || empty($assign_issue_nr)) {
                    return;   
                }
                
                $tmpAssignObj = new PollIssue();
                $assignTblName = $tmpAssignObj->getDbTableName();
                
                $join = "LEFT JOIN $assignTblName AS j 
                            ON 
                            j.fk_poll_nr = `$mainTblName`.poll_nr 
                            AND j.fk_issue_nr = '$assign_issue_nr' 
                            AND j.fk_publication_id = '$assign_publication_id'";
                
                if (isset($language_id)) {
                    $join .= " AND j.fk_issue_language_id = '$language_id'";    
                }
                
                $selectClauseObj->addJoin($join);
                $selectClauseObj->addWhere('j.fk_poll_nr IS NOT NULL');
                $selectClauseObj->setDistinct('plugin_poll.poll_nr');
            break;  
            
            case 'section':
                if (empty($assign_publication_id) || empty($assign_issue_nr) || empty($assign_section_nr)) {
                    return;   
                }
                
                $tmpAssignObj = new PollSection();
                $assignTblName = $tmpAssignObj->getDbTableName();
                
                $join = "LEFT JOIN `$assignTblName` AS j 
                            ON 
                            j.fk_poll_nr = `$mainTblName`.poll_nr 
                            AND j.fk_section_nr = '$assign_section_nr'                     
                            AND j.fk_issue_nr = '$assign_issue_nr' 
                            AND j.fk_publication_id = '$assign_publication_id'";
                                
                if (isset($language_id)) {
                    $join .= " AND j.fk_section_language_id = '$language_id'";    
                }
                
                $selectClauseObj->addJoin($join);
                $selectClauseObj->addWhere('j.fk_poll_nr IS NOT NULL');
                $selectClauseObj->setDistinct('plugin_poll.poll_nr');
            break;
            
            case 'article':
                if (empty($assign_article_nr)) {
                    return;   
                }
                
                $tmpAssignObj = new PollArticle();
                $assignTblName = $tmpAssignObj->getDbTableName();
                
                $join = "LEFT JOIN `$assignTblName` AS j 
                            ON 
                            j.fk_poll_nr = `$mainTblName`.poll_nr 
                            AND j.fk_article_nr = '$assign_article_nr'";
                
                if (isset($language_id)) {
                    $join .= " AND j.fk_article_language_id = '$language_id'";    
                }
                
                $selectClauseObj->addJoin($join);
                $selectClauseObj->addWhere('j.fk_poll_nr IS NOT NULL');
                $selectClauseObj->setDistinct('plugin_poll.poll_nr');
            break;
        }
        
        if (is_array($p_order)) {
            $order = Poll::ProcessListOrder($p_order);
            // sets the order condition if any
            foreach ($order as $orderField=>$orderDirection) {
                $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
            }
        }
        
        $sqlQuery = $selectClauseObj->buildQuery();
        
        // count all available results
        $countRes = $g_ado_db->Execute($sqlQuery);
        $p_count = $countRes->recordCount();
        
        //get the wanted rows
        $pollRes = $g_ado_db->SelectLimit($sqlQuery, $p_limit, $p_start);
        
        // builds the array of poll objects
        $pollsList = array();
        while ($poll = $pollRes->FetchRow()) {
            $pollObj = new Poll($poll['fk_language_id'], $poll['poll_nr']);
            if ($pollObj->exists()) {
                $pollsList[] = $pollObj;
            }
        }

        return $pollsList;
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
    private static function ProcessListParameters($p_param)
    {
        $comparisonOperation = array();

        $comparisonOperation['left'] = PollsList::$s_parameters[strtolower($p_param->getLeftOperand())]['field'];

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
                    $dbField = 'poll_nr';
                    break;
                case 'byname':
                    $dbField = 'title';
                    break;
                case 'bybegin':
                    $dbField = 'date_begin';
                    break;
                case 'byend':
                    $dbField = 'date_end';
                    break;
                case 'byvotes':
                    $dbField = 'nr_of_votes';
                    break;
                default:
                    $dbField = 'poll_nr';
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }
       
    /**
     * Return if this poll can be voted
     * (must be in start-end interval, 
     * and not been voted before by same client)
     *
     * @return boolean
     */
    public function isVotable()
    {
        if (strtotime($this->m_data['date_begin']) > strtotime(date('Y-m-d'))) {
            return false;   
        }
        if (strtotime($this->m_data['date_end']) < strtotime(date('Y-m-d')) + 60*60*24) {
            return false;   
        }
        if ($this->m_mode == 'single' && $this->hasUserVoted()) {
            return false;   
        }
        
        return true;   
    }
    
    /**
     * Mark the poll as voted by client
     *
     */
    protected function setUserHasVoted()
    {
        $key = 'poll_'.$this->m_data['fk_language_id'].'_'.$this->m_data['poll_nr'];
        $_SESSION[$key] = true;
        
        preg_match('/([0-9a-zA-Z]+\.[a-zA-Z]+)$/', $_SERVER['SERVER_NAME'], $hostname);
        setcookie($key, 1, time()+60*60*24*365, '/', $hostname[0]);       
    }
    
    /**
     * Return if client vote this poll already
     *
     * @return boolean
     */
    public function hasUserVoted()
    {
        $key = 'poll_'.$this->m_data['fk_language_id'].'_'.$this->m_data['poll_nr'];
        if (array_key_exists($key, $_SESSION)) {
            return true;   
        }
        if (array_key_exists($key, $_COOKIE)) {
            return true;   
        }
        return false;
    }
    
    /**
     * Register vote(s) submitted by client
     *
     */
    public static function registerVoting()
    {   
        $poll_language_id = Input::Get('f_poll_language_id', 'int');
        $poll_nr = Input::Get('f_poll_nr', 'int');   
        $nr_answer = Input::Get('f_pollanswer_nr', 'int');
        
        $Poll = new Poll($poll_language_id, $poll_nr);
                                   
        if ($Poll->isVotable()) {
            $PollAnswer = $Poll->getAnswer($nr_answer);
            
            if ($PollAnswer->exists()) {
                $PollAnswer->vote();
                $Poll->setUserHasVoted();
            }
        }
    }

} // class Poll

?>
