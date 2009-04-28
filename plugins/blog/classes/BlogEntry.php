<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <sebastian.goebel@web.de>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

class BlogEntry extends DatabaseObject {
    /**
	 * The column names used for the primary key.
	 * @var array
	 */
    var $m_keyColumnNames       = array('entry_id');
    var $m_keyIsAutoIncrement   = true;
    var $m_dbTableName          = 'plugin_blog_entry';
    static $s_dbTableName       = 'plugin_blog_entry';

    var $m_columnNames = array(
        'entry_id',
        'fk_blog_id',
        'fk_language_id',
        'fk_user_id',
        'published',
        'released',
        'status',
        'title',
        'content',
        'fk_mood_id',
        'admin_status',
        'comments_online',
        'comments_offline',
        'feature',
        'last_modified'
    );

    static $m_html_allowed_fields = array('content');

    /**
	 * Construct by passing in the primary key to access the article in
	 * the database.
	 *
	 * @param int $p_languageId
	 * @param int $p_articleNumber
	 *		Not required when creating an article.
	 */
    function BlogEntry($p_entry_id=null, $p_blog_id=null)
    {
        parent::DatabaseObject($this->m_columnNames);

        $this->m_data['entry_id'] = $p_entry_id;

        if ($this->keyValuesExist()) {
            $this->fetch();
            $this->m_data['images'] = BlogImageHelper::GetImagePaths('entry', $p_entry_id, true, true);

        } elseif ($p_blog_id) {
            $this->m_data['fk_blog_id'] = $p_blog_id;
        }
    } // constructor

    function setProperty($p_name, $p_value)
    {
        if ($p_name == 'topics') {
            return $this->setTopics($p_value);   
        }
        
        /*
        if ($p_name == 'admin_status') {
            switch ($p_value) {
                case 'online':
                case 'moderated':
                case 'readonly':
                    parent::setProperty('published', date('Y-m-d H:i:s'));
                break;
                  
                case 'offline':
                case 'pending':
                    parent::setProperty('published', null);
                break;
            }          
        }
        */
        
        $result = parent::setProperty($p_name, $p_value);

        if ($p_name == 'status' || $p_name == 'admin_status') {
            require_once 'Blog.php';
            Blog::TriggerCounters($this->getProperty('fk_blog_id'));
        }
        
        return $result;
    }
    


    /**
	 * A way for internal functions to call the superclass create function.
	 * @param array $p_values
	 */
    function __create($p_values=null) { return parent::create($p_values); }


    function create($p_blog_id, $p_user_id, $p_title=null, $p_content=null, $p_mood_id=null)
    {
        // Create the record
        $values = array(
        'fk_blog_id'    => $p_blog_id,
        'fk_language_id'=> Blog::GetBlogLanguageId($p_blog_id),
        'fk_user_id'    => $p_user_id,
        'title'         => $p_title,
        'content'       => $p_content,
        'fk_mood_id'    => $p_mood_id,
        'published'     => date('Y-m-d H:i:s')
        );

        $success = parent::create($values);

        if (!$success) {
            return false;
        }
        
        // set proper status/adminstatus if blog is not moderated
        // DB default is pending
        if ($this->getBlog()->getProperty('admin_status') == 'online') {
            $this->setProperty('admin_status', 'online');   
        }
        if ($this->getBlog()->getProperty('status') == 'online') {
            $this->setProperty('status', 'online');   
        }

        $this->fetch();

        require_once 'Blog.php';
        Blog::TriggerCounters($p_blog_id);

        return true;
    }


    function delete()
    {
        $entry_id = $this->getProperty('entry_id');
        $blog_id = $this->getProperty('fk_blog_id');
        
        foreach (BlogComment::getComments(array('entry_id' => $this->getProperty('entry_id'))) as $Comment) {
            $Comment->delete();
        }

        parent::delete();

        BlogImageHelper::RemoveImageDerivates('entry', $entry_id);
        BlogentryTopic::OnBlogentryDelete($entry_id);
        Blog::TriggerCounters($blog_id);
    }

    function getData()
    {
        return $this->m_data;
    }
    
    function getSubject()
    {
        return $this->getProperty('title');   
    }
    
    function getBlog()
    {
        static $Blog;
        
        if (!is_object($Bog)) {
            $Blog = new Blog($this->getProperty('fk_blog_id'));
        }
        return $Blog;   
    }

    function _buildQueryStr($p_cond, $p_checkParent)
    {
        $blogs_tbl = Blog::$s_dbTableName;
        $entries_tbl = self::$s_dbTableName;
        
        if (array_key_exists('fk_blog_id', $p_cond)) {
            $cond .= " AND e.fk_blog_id = {$p_cond['fk_blog_id']}";
        }

        if (array_key_exists('fk_user_id', $p_cond)) {
            $cond .= " AND e.fk_user_id = {$p_cond['fk_user_id']}";
        }

        if (array_key_exists('status', $p_cond)) {
            $cond .= self::_buildSubQuery($p_cond, 'status', $p_checkParent);
        }

        if (array_key_exists('admin_status', $p_cond)) {
            $cond .= self::_buildSubQuery($p_cond, 'admin_status', $p_checkParent);
        }

        if (array_key_exists('GROUP BY', $p_cond)) {
            $groupby = 'GROUP BY '.$p_cond['GROUP BY'];
        }

        if (array_key_exists('SELECT', $p_cond)) {
            $select = 'SELECT '.$p_cond['SELECT'];
        } else {
            $select = 'SELECT e.entry_id';
        }

        $queryStr = "$select
                     FROM       $entries_tbl AS e, 
                                $blogs_tbl  AS b
                     WHERE      e.fk_blog_id = b.blog_id 
                                $cond
                     $groupby
                     ORDER BY   entry_id DESC";
        return $queryStr;
    }

    function _buildSubQuery($p_cond, $p_key, $p_checkParent)
    {
        if (is_array($p_cond[$p_key])) {

            foreach ($p_cond[$p_key] as $v) {
                $subcond .= "e.{$p_key} = '{$v}' OR ";
                if ($p_checkParent) $subcond2 .= "b.{$p_key} = '{$v}' OR ";
            }

            $cond .= " AND (".substr($subcond, 0, -3).")";
            if (subcond2) $cond .=  " AND (".substr($subcond2, 0, -3).")";

        } else {
            $cond .= " AND e.{$p_key} = '{$p_cond[$p_key]}'";
            if ($p_checkParent) $cond .= " AND b.{$p_key} = '{$p_cond[$p_key]}'";
        }

        return $cond;
    }

    function getEntries($p_cond=array(), $p_currPage=0, $p_perPage=20, $p_checkParent=false)
    {
        global $g_ado_db;

        $queryStr   = self::_buildQueryStr($p_cond, $p_checkParent);
        $query      = $g_ado_db->SelectLimit($queryStr, $p_perPage, ($p_currPage-1) * $p_perPage);
        $entries    = array();

        while ($row = $query->FetchRow()) {
            $tmpEntry = new BlogEntry($row['entry_id']);
            $entries[] = $tmpEntry;
        }

        return $entries;
    }

    function countEntries($p_cond=array(), $p_checkParent=false)
    {
        global $g_ado_db;

        $queryStr = self::_buildQueryStr($p_cond, $p_checkParent);

        $query = $g_ado_db->Execute($queryStr);

        return $query->RecordCount();
    }

    static function TriggerCounters($p_entry_id)
    {
        global $g_ado_db;
        
        if (!$p_entry_id) {
            return false;   
        }
        
        $entryTbl = self::$s_dbTableName;
        $commentTbl  = BlogComment::$s_dbTableName;
        
        $queryStr = "UPDATE $entryTbl
                     SET    comments_online = 
                        (SELECT COUNT(comment_id) 
                         FROM   $commentTbl
                         WHERE  fk_entry_id = $p_entry_id AND (status = 'online' AND admin_status = 'online')),
                            comments_offline = 
                        (SELECT COUNT(comment_id) 
                         FROM   $commentTbl
                         WHERE  fk_entry_id = $p_entry_id AND (status != 'online' OR admin_status != 'online'))
                     WHERE  entry_id = $p_entry_id";  
        $g_ado_db->Execute($queryStr);
        
        Blog::TriggerCounters(self::GetBlogId($p_entry_id));
    }

    static public function GetBlogId($p_entry_id)
    {
        $tmpEntry = new BlogEntry($p_entry_id);
        return $tmpEntry->getProperty('fk_blog_id');
    }
    
    function _getFormMask($p_admin)
    {
        global $g_user;
        
        $data = $this->getData();

        foreach ($data as $k => $v) {
            // clean user input
            if (!in_array($k, self::$m_html_allowed_fields)) {
                $data[$k] = camp_html_entity_decode_array($v);
            }
        }

        $mask = array(
            'f_entry_id'    => array(
                'element'   => 'f_entry_id',
                'type'      => 'hidden',
                'constant'  => $data['entry_id']
            ),
            'f_blog_id'    => array(
                'element'   => 'f_blog_id',
                'type'      => 'hidden',
                'constant'  => $data['fk_blog_id']
            ),
            'tiny_mce'  => array(
                'element'   => 'tiny_mce',
                'text'      => Blog::GetEditor('tiny_mce_box', $g_user, camp_session_get('TOL_Language', $data['fk_language_id'])),
                'type'  => 'static'
            ),
            'title'     => array(
                'element'   => 'BlogEntry[title]',
                'type'      => 'text',
                'label'     => 'Title',
                'default'   => $data['title'],
                'required'  => true
            ),
            'content'      => array(
                'element'   => 'BlogEntry[content]',
                'type'      => 'textarea',
                'label'     => 'Content',
                'default'   => $data['content'],
                'required'  => true,
                'attributes'=> array('cols' => 86, 'rows' => 16, 'id' => 'tiny_mce_box')
            ),
            'status' => array(
                'element'   => 'BlogEntry[status]',
                'type'      => 'select',
                'label'     => 'status',
                'default'   => $data['status'],
                'options'   => array(
                    'online'    => 'online',
                    'offline'   => 'offline'
                ),
                'required'  => true
            ),
            'admin_status' => array(
                'element'   => 'BlogEntry[admin_status]',
                'type'      => 'select',
                'label'     => 'Admin status',
                'default'   => $data['admin_status'],
                'options'   => array(
                    'online'    => 'online',
                    'offline'   => 'offline',
                    'pending'   => 'pending',
                ),
                'required'  => true
            ),
            'mood'      => array(
                'element'   => 'BlogEntry[fk_mood_id]',
                'type'      => 'radio',
                'label'     => 'mood',
                'default'   => $data['fk_mood_id'],
                'options'   => Blog::GetMoodList(!empty($data['fk_laguage_id']) ? $data['fk_laguage_id'] : Blog::GetBlogLanguageId($data['fk_blog_id']))
            ),
            'image'     => array(
                'element'   => 'BlogEntry_Image',
                'type'      => 'file',
                'label'     => 'Image (.jpg, .png, .gif)',
            ),
            'image_display'  => array(
                'element'   => 'image_display',
                'text'      => '<img src="'.$data['images']['100x100'].'">',
                'type'  => 'static',
                'groupit'   => true
            ),
            'image_remove' => array(
                'element'   => 'BlogEntry_Image_remove',
                'type'      => 'checkbox',
                'label'     => 'Remove this Image',
                'groupit'   => true
            ),
            'image_label'  => array(
                'element'   => 'image_label',
                'text'      => 'Remove this image',
                'type'  => 'static',
                'groupit'   => true
            ),
            'image_group' =>  isset($data['images']['100x100']) ? array(
                'group'     => array('image_display', 'BlogEntry_Image_remove', 'image_label'),
            
            ) : null,
            'reset'     => array(
                'element'   => 'reset',
                'type'      => 'reset',
                'label'     => 'Reset',
                'groupit'   => true
            ),
            'xsubmit'     => array(
                'element'   => 'xsubmit',
                'type'      => 'button',
                'label'     => 'Submit',
                'attributes'=> array('onclick' => 'tinyMCE.triggerSave(); if (this.form.onsubmit()) this.form.submit()'),
                'groupit'   => true
            ),
            'cancel'     => array(
                'element'   => 'cancel',
                'type'      => 'button',
                'label'     => 'Cancel',
                'attributes' => array('onClick' => 'window.close()'),
                'groupit'   => true
            ),
            'buttons'   => array(
                'group'     => array('cancel', 'reset', 'xsubmit')
            )
        );

        return $mask;
    }

    function getForm($p_target, $p_admin, $p_html=true)
    {
        require_once 'HTML/QuickForm.php';
        $mask = $this->_getFormMask($p_admin);
        $form = new html_QuickForm('blog_entry', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form($form, $mask);

        if ($p_html) {
            return $form->toHTML();
        } else {
            require_once 'HTML/QuickForm/Renderer/Array.php';

            $renderer = new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);

            return $renderer->toArray();
        }
    }

    function store($p_admin, $p_user_id=null)
    {
        require_once 'HTML/QuickForm.php';

        $mask = $this->_getFormMask($p_admin);
        $form = new html_QuickForm('blog_entry', 'post', '', null, null, true);
        FormProcessor::parseArr2Form($form, $mask);

        if ($form->validate()){
            $data = $form->getSubmitValues(true);

            foreach ($data['BlogEntry'] as $k => $v) {
                // clean user input
                if (!in_array($k, self::$m_html_allowed_fields)) {
                    $data['BlogEntry'][$k] = htmlspecialchars_array($v);
                }
            }

            if ($data['f_entry_id']) {
                foreach ($data['BlogEntry'] as $k => $v) {
                    if (is_array($v)) {
                        foreach($v as $key => $value) {
                            if ($value) {
                                $string .= "$key, ";
                            }
                        }
                        $v = substr($string, 0, -2);
                        unset ($string);

                    }
                    $this->setProperty($k, $v);
                }

                if ($data['BlogEntry_Image_remove']) {
                   BlogImageHelper::RemoveImageDerivates('entry', $data['f_entry_id']);
                }
                if ($data['BlogEntry_Image']) {
                    BlogImageHelper::StoreImageDerivates('entry', $data['f_entry_id'], $data['BlogEntry_Image']);
                }
                
                Blog::TriggerCounters(self::GetBlogId($data['f_entry_id']));

                return true;

            } elseif ($this->create(
                            $data['f_blog_id'], 
                            $p_user_id, 
                            $data['BlogEntry']['title'], 
                            $data['BlogEntry']['content'], 
                            $data['f_mood_id'])) {
                                
                // admin and owner can override status setting
                if ($data['BlogEntry']['status']) {
                    $this->setProperty('status', $data['BlogEntry']['status']);
                }
                if ($p_admin && $data['BlogEntry']['admin_status']) {
                    $this->setProperty('admin_status', $data['BlogEntry']['admin_status']);
                }
                
                if ($data['BlogEntry_Image']) {
                    BlogImageHelper::StoreImageDerivates('entry', $this->getProperty('entry_id'), $data['BlogEntry_Image']);
                }
                
                Blog::TriggerCounters($this->getProperty('fk_blog_id'));

                return true;
            }
        }
        return false;

    }

    function setis_onfrontpage()
    {
        if ($OldEntry = self::getis_onfrontpageEntry()) {
            $OldEntry->setProperty('is_onfrontpage', 0);
        }

        $this->setProperty('is_onfrontpage', 1);
    }

    function unsetis_onfrontpage()
    {
        $this->setProperty('is_onfrontpage', 0);
    }

    static function getis_onfrontpageEntry()
    {
        global $g_ado_db;

        $tblName = self::$s_dbTableName;

        $query = "SELECT    entry_id
                  FROM      `{$tblName}`
                  WHERE     is_onfrontpage = 1
                  LIMIT     0, 1";
        $res = $g_ado_db->execute($query);

        if ($row = $res->fetchRow()) {
            $Entry = new BlogEntry($row['entry_id']);
            return $Entry;
        }
        return false;
    }
    
    
    /**
     * Get the blogentry identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->getProperty('entry_id');   
    }
    
    /**
     * get the blogentry language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->getProperty('fk_language_id');
    } 
    
    public static function GetEntryLanguageId($p_entry_id)
    {
        $tmpEntry = new BlogEntry($p_entry_id);
        return $tmpEntry->getProperty('fk_language_id');
    }
    
    public function setTopics(array $p_topics=array()) 
    {
        // store the topics
        $allowed_topics = Blog::GetTopicTreeFlat();
        
        BlogentryTopic::DeleteBlogentryTopics($this->getId());
        
        foreach ($p_topics as $topic_id) {
            if (in_array($topic_id, $allowed_topics, true)) {
                $BlogentryTopic = new BlogentryTopic($this->m_data['entry_id'], $topic_id);
                $BlogentryTopic->create();
            }
        }
    }
    
    public function getTopics() 
    {   
        foreach (BlogentryTopic::getAssignments($this->m_data['entry_id']) as $BlogentryTopic) {
            $topics[] = $BlogentryTopic->getTopic();      
        }
        return (array) $topics;
    }
    
    
    /////////////////// Special template engine methods below here /////////////////////////////
    
    /**
     * Gets an blog list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
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
    public static function GetList($p_parameters, $p_order = null, $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;
        
        if (!is_array($p_parameters)) {
            return null;
        }
        
        // adodb::selectLimit() interpretes -1 as unlimited
        if ($p_limit == 0) {
            $p_limit = -1;   
        }
        
        $selectClauseObj = new SQLSelectClause();

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            $leftOperand = strtolower($comparisonOperation['left']);
            
            if ($leftOperand == 'matchalltopics') {
                // set the matchAllTopics flag
                $matchAllTopics = true;
                
            } elseif ($leftOperand == 'topic') {
                // add the topic to the list of match/do not match topics depending
                // on the operator
                if ($comparisonOperation['symbol'] == '=') {
                    $hasTopics[] = $comparisonOperation['right'];
                } else {
                    $hasNotTopics[] = $comparisonOperation['right'];
                }
            } else {
                $comparisonOperation = self::ProcessListParameters($param);
                if (empty($comparisonOperation)) {
                    continue;
                }
                
                $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $comparisonOperation['right'] . "' ";
                $selectClauseObj->addWhere($whereCondition);   
            }
        }
        
        if (count($hasTopics) > 0) {
            if ($matchAllTopics) {
                foreach ($hasTopics as $topicId) {
                    $sqlQuery = self::BuildTopicSelectClause(array($topicId));
                    $whereCondition = "plugin_blog_entry.entry_id IN (\n$sqlQuery        )";
                    $selectClauseObj->addWhere($whereCondition);
                }
            } else {
                $sqlQuery = self::BuildTopicSelectClause($hasTopics);
                $whereCondition = "plugin_blog_entry.entry_id IN (\n$sqlQuery        )";
                $selectClauseObj->addWhere($whereCondition);
            }
        }
        if (count($hasNotTopics) > 0) {
            $sqlQuery = self::BuildTopicSelectClause($hasNotTopics, true);
            $whereCondition = "plugin_blog_entry.entry_id IN (\n$sqlQuery        )";
            $selectClauseObj->addWhere($whereCondition);
        }
        
        // sets the columns to be fetched
        $tmpBlogEntry = new BlogEntry();
        $columnNames = $tmpBlogEntry->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpBlogEntry->getDbTableName();
        $selectClauseObj->setTable($mainTblName);
        unset($tmpBlogEntry);
                
        if (is_array($p_order)) {
            $order = self::ProcessListOrder($p_order);
            // sets the order condition if any
            foreach ($order as $orderField=>$orderDirection) {
                $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
            }
        }
       
        $sqlQuery = $selectClauseObj->buildQuery();
        
        // count all available results
        $countRes = $g_ado_db->Execute($sqlQuery);
        $p_count = $countRes->recordCount();
        
        //get tlimited rows
        $blogEntryRes = $g_ado_db->SelectLimit($sqlQuery, $p_limit, $p_start);
        
        // builds the array of blog objects
        $blogEntriesList = array();
        while ($blogEntry = $blogEntryRes->FetchRow()) {
            $blogEntryObj = new BlogEntry($blogEntry['entry_id']);
            if ($blogEntryObj->exists()) {
                $blogEntriesList[] = $blogEntryObj;
            }
        }

        return $blogEntriesList;
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
        $conditionOperation = array();

        $leftOperand = strtolower($p_param->getLeftOperand());
        
        switch ($leftOperand) {
            
        case 'feature':
            $conditionOperation['symbol'] = 'LIKE';
            $conditionOperation['right'] = '%'.$p_param->getRightOperand().'%';
            break;
            
        case 'matchalltopics':
            $conditionOperation['left'] = $leftOperand;
            $conditionOperation['symbol'] = '=';
            $conditionOperation['right'] = 'true';
            break;
            
        case 'topic':
            $conditionOperation['left'] = $leftOperand;
            $conditionOperation['right'] = (string)$p_param->getRightOperand();
            break;

        default:
            $conditionOperation['left'] = BlogEntriesList::$s_parameters[$leftOperand]['field'];
            $conditionOperation['right'] = (string)$p_param->getRightOperand();
            break;
        }

        if (!isset($conditionOperation['symbol'])) {
            $operatorObj = $p_param->getOperator();
            $conditionOperation['symbol'] = $operatorObj->getSymbol('sql');
        }

        return $conditionOperation;
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
            $dbField = BlogEntriesList::$s_parameters[substr($field, 2)]['field'];

            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        if (count($order) == 0) {
            $order['fk_blog_id'] = 'asc';
            $order['entry_id'] = 'asc';  
        }
        return $order;
    }
    
    /**
     * Returns a select query for obtaining the entries that have the given topics
     *
     * @param array $p_TopicIds
     * @param array $p_typeAttributes
     * @param bool $p_negate
     * @return string
     */
    private static function BuildTopicSelectClause(array $p_TopicIds, $p_negate = false)
    {
        $notCondition = $p_negate ? ' NOT' : '';
        $selectClause = '        SELECT fk_entry_id FROM '.BlogentryTopic::$s_dbTableName.' WHERE fk_topic_id'
                      . "$notCondition IN (" . implode(', ', $p_TopicIds) . ")\n";
                      
        return $selectClause;
    }
}
?>