<?php
/**
 * @package Campsite
 */
class PollIssue extends DatabaseObject {
    /**
     * The column names used for the primary key.
     *
     * @var array
     */
    var $m_keyColumnNames = array(
        'fk_poll_nr',
        'fk_issue_nr', 
        'fk_issue_language_id',
        'fk_publication_id'
    );

    /**
     * Table name
     *
     * @var string
     */
    var $m_dbTableName = 'plugin_poll_issue';

    /**
     * All column names in the table
      *
     * @var array
     */
    var $m_columnNames = array(
        // int - poll id
        'fk_poll_nr',

        // int - issue number
        'fk_issue_nr',
        
        // int - issue language id
        'fk_issue_language_id',
        
        // int - publication id
        'fk_publication_id'
        );

    /**
     * Construct by passing in the primary key to access the 
     * poll <-> publication relations
     *
     * @param int $p_poll_nr
     * @param int $p_issue_language_id
     * @param int $p_issue_nr
     * @param int $p_publication_id
     */
    function PollIssue($p_poll_nr = null, $p_issue_language_id = null, $p_issue_nr = null, $p_publication_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_poll_nr'] = $p_poll_nr;
        $this->m_data['fk_issue_language_id'] = $p_issue_language_id;
        $this->m_data['fk_issue_nr'] = $p_issue_nr;
        $this->m_data['fk_publication_id'] = $p_publication_id;
        
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
     * Called when poll is deleted
     *
     * @param int $p_poll_nr
     */
    public static function OnPollDelete($p_poll_nr)
    {    
        if (count(Poll::getTranslations($p_poll_nr)) > 1) {
            return;    
        }
          
        foreach (PollIssue::getAssignments($p_poll_nr) as $record) {
            $record->delete();   
        }   
    }
    
    /**
     * Call this if an publication is deleted
     *
     * @param int $p_issue_nr
     */
    public static function OnIssueDelete($p_issue_language_id, $p_issue_nr, $p_publication_id)
    {      
        foreach (PollIssue::getAssignments(null, $p_issue_language_id, $p_issue_nr, $p_publication_id) as $record) {
            $record->delete();   
        }   
    }
    
    /**
     * Get array of relations between publication and poll
     * You have to set param $p_issue_nr,
     * or booth $p_poll_nr and $p_poll_language_id
     *
     * @param int $p_issue_nr
     * @param int $p_poll_nr
     * @param int $p_poll_language_id
     * @return array(object PollIssue, object PollIssue, ...)
     */
    public static function getAssignments($p_poll_nr = null, 
                                            $p_issue_language_id = null, $p_issue_nr = null, 
                                            $p_publication_id = null, 
                                            $p_offset = 0, $p_limit = 10, $p_orderStr = null)
    {
        global $g_ado_db;
        $records = array();
        
        $PollIssue = new PollIssue();
        $where = '';
        
        if (!empty($p_poll_nr)) {
            $where .= "AND fk_poll_nr = $p_poll_nr ";   
        }    
        if (!empty($p_issue_language_id)) {
            $where .= "AND fk_issue_language_id = $p_issue_language_id ";   
        }    
        if (!empty($p_issue_nr)) {
            $where .= "AND fk_issue_nr = $p_issue_nr ";   
        }    
        if (!empty($p_publication_id)) {
            $where .= "AND fk_publication_id = $p_publication_id ";   
        }  
              
        if (empty($where)) {
            return array();   
        }  
              
        $query = "SELECT    *
                  FROM      {$PollIssue->m_dbTableName}
                  WHERE     1 $where
                  ORDER BY  fk_poll_nr DESC";
 
        $res = $g_ado_db->selectLimit($query, $p_limit == 0 ? -1 : $p_limit, $p_offset);
        
        while ($row = $res->fetchRow()) {
            $records[] = new PollIssue($row['fk_poll_nr'], $row['fk_issue_language_id'], $row['fk_issue_nr'], $row['fk_publication_id']);      
        } 
        
        return $records;    
    }
    
    /**
     * Get the responding publication object of an record
     *
     * @return object
     */
    public function getIssue()
    {
        $Issue = new Issue($this->m_data['fk_issue_language_id'], $this->m_data['fk_issue_nr']);
        
        return $Issue;   
    }
    
    /**
     * Get the IssueNr
     *
     * @return int
     */
    public function getIssueNr()
    {
        return $this->m_data['fk_issue_nr'];   
    }
       
    /**
     * Get the responding poll object for an record
     *
     * @return object
     */
    public function getPoll($p_language_id)
    {
        $poll = new Poll($p_language_id, $this->m_data['fk_poll_nr']); 
        
        return $poll;  
    }
    
    /**
     * Get the poll number
     *
     * @return int
     */
    public function getPollNumber()
    {
        return $this->m_data['fk_poll_nr'];   
    }

} // class PollIssue

?>
