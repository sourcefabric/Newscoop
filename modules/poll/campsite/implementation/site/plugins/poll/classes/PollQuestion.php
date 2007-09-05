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
class PollQuestion extends DatabaseObject {
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('fk_poll_id', 'fk_language_id');

    var $m_dbTableName = 'mod_poll_question';

    var $m_columnNames = array(
        // int - poll id
        'fk_poll_id',

        // int - language id
        'fk_language_id',

        // string - title in given language
        'title',

        // string - question in given language
        'question',
        
        // timestamp - last_modified
        'last_modified'
        );

    /**
     * Construct by passing in the primary key to access the poll in
     * the database.
     *
     * @param int $p_IdLanguage
     * @param int $p_id
     */
    function PollQuestion($p_fk_language_id, $p_fk_poll_id)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_language_id'] = $p_fk_language_id;
        $this->m_data['fk_poll_id'] = $p_fk_poll_id;
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
     * Create an poll in the database.  Use the SET functions to
     * change individual values.
     *
     * @param string $p_fk_default_language_id
     * @param date $p_date_begin
     * @param date $p_date_end
     * @param int $p_nr_of_answers
     * @param bool $p_is_show_after_expiration
     * @return void
     */
    function create($p_title, $p_question)
    {
        global $g_ado_db;
        
        if (!strlen($p_title) || !strlen($p_question)) {
            return false;   
        }

        // Create the record
        $values = array(
            'title' => $p_title,
            'question' => $p_question,      
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
     * Create a translation of an poll.
     *
     * @param int $p_languageId
     * @param int $p_userId
     * @param string $p_name
     * @return Article
     */
    function createTranslation($p_fk_language_id, $p_title, $p_question)
    {
        // Construct the duplicate PollQuestion object.
        $pollQuestionCopy =& new PollQuestion($p_fk_language_id, $this->m_data['poll_id']);
        $pollQuestionCopy->create($p_title, $p_question);

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
            $this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
            $articleCopy->getTitle(), $articleCopy->getLanguageName());
        Log::Message($logtext, null, 31);
        */
        
        return $pollQuestionCopy;
    } // fn createTranslation


    /**
     * Delete poll from database.  This will
     * only delete one specific translation of the poll question.
     *
     * @return boolean
     */
    function delete()
    {    
        require_once ('PollAnswer.php');
          
        // delete from mod_poll_answer table
        PollAnswer::OnPollQuestionDelete($this->m_data['fk_poll_id'], $this->m_data['fk_language_id']);      
           
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
    
    public function OnPollDelete($p_poll_id)
    {
        foreach (PollQuestion::getTranslations($p_poll_id) as $question) {
            $question->delete();   
        }   
    }
    
    public function getTranslations($p_poll_id = null)
    {
        global $g_ado_db;
        $translations = array();
               
        if ($p_poll_id) {
            $poll_id = $p_poll_id;   
        }
        
        if ($this->m_data['id']) {
            $poll_id = $this->m_data['id'];       
        }
        
        if (!$poll_id) {
            return false;   
        }
        
        $query = "SELECT    fk_language_id
                  FROM      mod_poll_question
                  WHERE     fk_poll_id = $poll_id
                  ORDER BY  fk_language_id";
        
        $res = $g_ado_db->Execute($query);
        
        while ($row = $res->fetchRow()) {
            $translations[] =& new PollQuestion($row['fk_language_id'], $poll_id);      
        } 
        
        return $translations;    
    }
    
    public function getAnswers()
    {
        require_once ('PollAnswer.php');
        
        return PollAnswer::getAnswers($this->m_data['fk_poll_id'], $this->m_data['fk_language_id']);   
    }

} // class PollQuestion

?>