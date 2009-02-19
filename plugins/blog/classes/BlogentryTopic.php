<?php
/**
 * @package Campsite
 */
class BlogentryTopic extends DatabaseObject {
    /**
     * The column names used for the primary key.
     *
     * @var array
     */
    var $m_keyColumnNames = array('fk_entry_id', 'fk_topic_id');

    /**
     * Table name
     *
     * @var string
     */
    var $m_dbTableName = 'plugin_blog_entry_topic';

    /**
     * All column names in the table
      *
     * @var array
     */
    var $m_columnNames = array(
        // int - blogentry id
        'fk_entry_id',

        // int - topic id
        'fk_topic_id'
        );

    /**
     * Construct by passing in the primary key to access the 
     * blogentry <-> topic relations
     *
     * @param int $p_blogentry_id
     * @param int $p_topic_id
     */
    function BlogentryTopic($p_blogentry_id = null, $p_topic_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_entry_id'] = $p_blogentry_id;
        $this->m_data['fk_topic_id'] = $p_topic_id;
        
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
     * Create an link blogentry <-> topic record in the database.
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
        return $deleted;
    } // fn delete
    
    public static function DeleteBlogentryTopics($p_blogentry_id)
    {
        global $g_ado_db;
        
        $BlogentryTopic = new BlogentryTopic();

        $query = "DELETE FROM {$BlogentryTopic->m_dbTableName}
                  WHERE fk_entry_id = $p_blogentry_id";
         
        return $g_ado_db->execute($query);  
    }
    
    /**
     * Called when blogentry is deleted
     *
     * @param int $p_blogentry_id
     */
    public static function OnBlogentryDelete($p_blogentry_id)
    {    
        foreach (BlogentryTopic::GetAssignments($p_blogentry_id) as $record) {
            $record->delete();   
        }   
    }
    
    /**
     * Call this if an topic is deleted
     *
     * @param int $p_topic_id
     */
    public static function OnTopicDelete($p_topic_id)
    {      
        foreach (BlogentryTopic::GetAssignments(null, $p_topic_id) as $record) {
            $record->delete();   
        }   
    }
    
    /**
     * Get array of relations between topic and blogentry
     * You have to set param $p_topic_id,
     * or $p_blogentry_id
     *
     * @param int $p_topic_id
     * @param int $p_blogentry_id
     * @return array(object BlogentryTopic, object BlogentryTopic, ...)
     */
    public static function GetAssignments($p_blogentry_id = null, $p_topic_id = null, $p_offset = 0, $p_limit = 10, $p_orderStr = null)
    {
        global $g_ado_db;
        $records = array();
        
        $BlogentryTopic = new BlogentryTopic();
        
        if (!empty($p_blogentry_id)) {
            $where .= "AND fk_entry_id = $p_blogentry_id ";   
        }
        if (!empty($p_topic_id)) {
            $where .= "AND fk_topic_id = $p_topic_id ";   
        }
        
        if (empty($where)) {
            return array();   
        }
        
        $query = "SELECT    *
                  FROM      {$BlogentryTopic->m_dbTableName}
                  WHERE     1 $where
                  ORDER BY  fk_entry_id DESC";
        
        $res = $g_ado_db->selectLimit($query, $p_limit == 0 ? -1 : $p_limit, $p_offset);
        
        while ($row = $res->fetchRow()) {
            $records[] = new BlogentryTopic($row['fk_entry_id'], $row['fk_topic_id']);      
        } 
        
        return $records;    
    }
    
    /**
     * Get the responding topic object of an record
     *
     * @return object
     */
    public function getTopic()
    {
        $Topic = new Topic($this->m_data['fk_topic_id']);
        
        return $Topic;   
    }
    
    /**
     * Get the TopicId
     *
     * @return int
     */
    public function getTopicId()
    {
        return $this->m_data['fk_topic_id'];   
    }
       
    /**
     * Get the responding blogentry object for an record
     *
     * @return object
     */
    public function getBlogentry($p_language_id)
    {
        $BlogEntry = new BlogEntry($p_language_id, $this->m_data['fk_entry_id']); 
        
        return $BlogEntry;  
    }
       
} // class BlogentryTopic

?>
