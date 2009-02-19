<?php
/**
 * @package Campsite
 */
class BlogTopic extends DatabaseObject {
    /**
     * The column names used for the primary key.
     *
     * @var array
     */
    var $m_keyColumnNames = array('fk_blog_id', 'fk_topic_id');

    /**
     * Table name
     *
     * @var string
     */
    var $m_dbTableName = 'plugin_blog_topic';

    /**
     * All column names in the table
      *
     * @var array
     */
    var $m_columnNames = array(
        // int - blog id
        'fk_blog_id',

        // int - topic id
        'fk_topic_id'
        );

    /**
     * Construct by passing in the primary key to access the 
     * blog <-> topic relations
     *
     * @param int $p_blog_id
     * @param int $p_topic_id
     */
    function BlogTopic($p_blog_id = null, $p_topic_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_blog_id'] = $p_blog_id;
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
     * Create an link blog <-> topic record in the database.
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
    
    public static function DeleteBlogTopics($p_blog_id)
    {
        global $g_ado_db;
        
        $BlogTopic = new BlogTopic();

        $query = "DELETE FROM {$BlogTopic->m_dbTableName}
                  WHERE fk_blog_id = $p_blog_id";
         
        return $g_ado_db->execute($query);  
    }
    
    /**
     * Called when blog is deleted
     *
     * @param int $p_blog_id
     */
    public static function OnBlogDelete($p_blog_id)
    {    
        foreach (BlogTopic::GetAssignments($p_blog_id) as $record) {
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
        foreach (BlogTopic::GetAssignments(null, $p_topic_id) as $record) {
            $record->delete();   
        }   
    }
    
    /**
     * Get array of relations between topic and blog
     * You have to set param $p_topic_id,
     * or $p_blog_id
     *
     * @param int $p_topic_id
     * @param int $p_blog_id
     * @return array(object BlogTopic, object BlogTopic, ...)
     */
    public static function GetAssignments($p_blog_id = null, $p_topic_id = null, $p_offset = 0, $p_limit = 10, $p_orderStr = null)
    {
        global $g_ado_db;
        $records = array();
        
        $BlogTopic = new BlogTopic();
        
        if (!empty($p_blog_id)) {
            $where .= "AND fk_blog_id = $p_blog_id ";   
        }
        if (!empty($p_topic_id)) {
            $where .= "AND fk_topic_id = $p_topic_id ";   
        }
        
        if (empty($where)) {
            return array();   
        }
        
        $query = "SELECT    *
                  FROM      {$BlogTopic->m_dbTableName}
                  WHERE     1 $where
                  ORDER BY  fk_blog_id DESC";
        
        $res = $g_ado_db->selectLimit($query, $p_limit == 0 ? -1 : $p_limit, $p_offset);
        
        while ($row = $res->fetchRow()) {
            $records[] = new BlogTopic($row['fk_blog_id'], $row['fk_topic_id']);      
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
     * Get the responding blog object for an record
     *
     * @return object
     */
    public function getBlog($p_language_id)
    {
        $blog = new Blog($p_language_id, $this->m_data['fk_blog_id']); 
        
        return $blog;  
    }
       
} // class BlogTopic

?>
