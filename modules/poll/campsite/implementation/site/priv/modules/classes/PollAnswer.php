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

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/Language.php');

/**
 * @package Campsite
 */
class PollAnswer extends DatabaseObject {
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('fk_poll_nr', 'fk_language_id', 'nr_answer');

    var $m_dbTableName = 'mod_poll_answer';

    var $m_columnNames = array(
        // int - poll id
        'fk_poll_nr',

        // int - language id
        'fk_language_id',

        // int - nr of answer
        'nr_answer',

        // string - the literal answer
        'answer',
        
        // int - number of votes for this answer
        'nr_of_votes',
        
        // timestamp - last_modified
        'last_modified'
        );

    /**
     * Construct by passing in the primary key to access the poll answer in
     * the database.
     *
     * @param int $p_IdLanguage
     * @param int $p_id
     */
    function PollAnswer($p_fk_language_id, $p_fk_poll_nr, $p_nr_answer)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_language_id'] = $p_fk_language_id;
        $this->m_data['fk_poll_nr'] = $p_fk_poll_nr;
        $this->m_data['nr_answer'] = $p_nr_answer;
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
     * Create an poll answer in the database.  Use the SET functions to
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
            return;
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
     * Create a translation of an answer set.
     *
     * @param int $p_languageId
     * @param int $p_userId
     * @param string $p_name
     * @return Article
     */
    function CreateTranslationSet($p_fk_poll_nr, $p_source_language_id, $p_target_language_id)
    {
        // Construct the duplicate PollQuestion object.
        foreach (PollAnswer::getAnswers($p_fk_poll_nr, $p_source_language_id) as $answer) {
            $answer_copy = new PollAnswer($p_target_language_id, $p_fk_poll_nr, $answer->getNumber());
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
        
        return $pollAnswerCopy;
    } // fn createTranslation


    /**
     * Delete poll from database.  This will
     * only delete one specific translation of the poll question.
     *
     * @return boolean
     */
    function delete()
    {        
        // Delete from mod_poll_question table
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
    
    public function OnPollDelete($p_fk_poll_nr, $p_fk_language_id)
    {
        foreach (PollAnswer::getAnswers($p_fk_poll_nr, $p_fk_language_id) as $answer) {
            $answer->delete();   
        }   
    }
    
    public function getAnswers($p_fk_poll_nr = null, $p_fk_language_id = null)
    {
        global $g_ado_db;
        $answers = array();
               
        if ($p_fk_poll_nr && $p_fk_language_id) {
            $fk_poll_nr = $p_fk_poll_nr;
            $fk_language_id = $p_fk_language_id;   
        } elseif (isset($this)) {
            $fk_poll_nr = $this->m_data['fk_poll_nr']; 
            $fk_language_id = $this->m_data['fk_language_id'];      
        }
        
        if (!$fk_poll_nr || !$fk_language_id) {
            return array();   
        }
        
        $query = "SELECT    nr_answer
                  FROM      mod_poll_answer
                  WHERE     fk_poll_nr = $fk_poll_nr
                        AND fk_language_id = $fk_language_id
                  ORDER BY  nr_answer";
        
        $res = $g_ado_db->Execute($query);
        
        while ($row = $res->fetchRow()) {
            $answers[] =& new PollAnswer($fk_language_id, $fk_poll_nr, $row['nr_answer']);      
        } 
        
        return $answers;    
    }
    
    public static function SyncNrOfAnswers($p_fk_language_id, $p_fk_poll_nr)
    {
        global $g_ado_db;
        
        $poll = new Poll($p_fk_language_id, $p_fk_poll_nr);
        $nr_of_answers = $poll->getProperty('nr_of_answers');
        
        $query = "DELETE FROM   mod_poll_answer
                  WHERE         fk_poll_nr = $p_fk_poll_nr
                            AND fk_language_id = $p_fk_language_id
                            AND nr_answer > $nr_of_answers";
        $g_ado_db->execute($query);  
    }
    
    public function getNumber()
    {
        return $this->m_data['nr_answer'];   
    }

} // class PollQuestion

?>