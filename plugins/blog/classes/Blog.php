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

class Blog extends DatabaseObject {
    /**
	 * The column names used for the primary key.
	 * @var array
	 */
    var $m_keyColumnNames       = array('blog_id');
    var $m_keyIsAutoIncrement   = true;
    var $m_dbTableName          = 'plugin_blog_blog';

    var $m_columnNames = array(
        'blog_id',
        'fk_language_id',
        'fk_user_id',
        'title',
        'published',
        'info',
        'status',
        'admin_status',
        'admin_remark',
        'request_text',
        'entries_online',
        'entries_offline',
        'comments_online',
        'comments_offline',
        'feature',
        'last_modified'
    );

    static $m_html_allowed_fields = array('info');
    static $m_html_allowed_tags = '<strong><em><u><a><img><p>';

    /**
	 * Construct by passing in the primary key to access the article in
	 * the database.
	 *
	 * @param int $p_languageId
	 * @param int $p_articleNumber
	 *		Not required when creating an article.
	 */
    function Blog($p_blog_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['blog_id'] = $p_blog_id;
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
    } // constructor


    /**
	 * A way for internal functions to call the superclass create function.
	 * @param array $p_values
	 */
    function __create($p_values = null) { return parent::create($p_values); }


    function create($p_user_id, $p_language_id, $p_title, $p_info, $p_request_text, $p_feature)
    {
        // Create the record
        $values = array(
            'fk_user_id'    => $p_user_id,
            'fk_language_id'=> $p_language_id,
            'title'         => $p_title,
            'info'          => $p_info,
            'request_text'  => $p_request_text,
            'feature'       => $p_feature
        );

        $success = parent::create($values);

        if (!$success) {
            return false;
        }

        $this->fetch();
        
        return true;
    }

    function delete()
    {
        foreach (BlogEntry::getEntries(array('fk_blog_id' => $this->getProperty('blog_id'))) as $Entry) {
            $Entry->delete();
        }

        parent::delete();
    }

    function getData()
    {
        return $this->m_data;
    }
    
        
    function getSubject()
    {
        return $this->getProperty('title');   
    }

    private static function BuildQueryStr($p_cond)
    {
        $instance = new Blog();

        if (array_key_exists('fk_user_id', $p_cond)) {
            $cond .= " AND fk_user_id = {$p_cond['fk_user_id']}";
        }
        if (array_key_exists('status', $p_cond)) {
            $cond .= " AND status = '{$p_cond['status']}'";
        }
        if (array_key_exists('admin_status', $p_cond)) {
            $cond .= " AND admin_status = '{$p_cond['admin_status']}'";
        }

        $queryStr = "SELECT     blog_id
                     FROM       {$instance->m_dbTableName}
                     WHERE      1 $cond
                     ORDER BY   blog_id DESC";
        return $queryStr;
    }

    function getBlogs($p_cond, $p_currPage=0, $p_perPage=20)
    {
        global $g_ado_db;

        $queryStr = Blog::BuildQueryStr($p_cond);

        $query = $g_ado_db->SelectLimit($queryStr, $p_perPage, ($p_currPage-1) * $p_perPage);
        $blogs = array();

        while ($row = $query->FetchRow()) {
            $tmpBlog = new Blog($row['blog_id']);
            $blogs[] = $tmpBlog;
        }

        return $blogs;
    }

    function countBlogs($p_cond=array())
    {
        global $g_ado_db;

        $queryStr   = Blog::BuildQueryStr($p_cond);
        $query      = $g_ado_db->Execute($queryStr); #

        return $query->RecordCount();
    }

    function getBlogEntrys()
    {
        $BlogEntry = new BlogEntry(array('blog_id' => $this->getProperty('blog_id')));

        return $BlogEntry->getEntrys();
    }

    static function TriggerCounters($p_blog_id)
    {
        global $g_ado_db;
        
        $Blog = new Blog();
        $blogs_tbl = $Blog->m_dbTableName;
        
        $BlogEntry = new BlogEntry();
        $entries_tbl = $BlogEntry->m_dbTableName;

        $queryStr = "UPDATE $blogs_tbl
                     SET    entries_online = 
                        (SELECT COUNT(entry_id) 
                         FROM   $entries_tbl
                         WHERE  fk_blog_id = $p_blog_id AND (status = 'online' AND admin_status = 'online')),
                            entries_offline = 
                        (SELECT COUNT(entry_id) 
                         FROM   $entries_tbl
                         WHERE  fk_blog_id = $p_blog_id AND (status != 'online' OR admin_status != 'online')),
                            comments_online =
                        (SELECT SUM(comments_online) 
                         FROM   $entries_tbl
                         WHERE  fk_blog_id = $p_blog_id),
                            comments_offline = 
                        (SELECT SUM(comments_offline) 
                         FROM   $entries_tbl
                         WHERE  fk_blog_id = $p_blog_id)
                     WHERE  blog_id = $p_blog_id";  
        $g_ado_db->Execute($queryStr);
    }

    private function getFormMask($p_owner=false, $p_admin=false)
    {
        $data = $this->getData();
        
        foreach (User::GetUsers() as $User) {
            if (1 || $User->hasPermission('PLUGIN_BLOG_USER')) {
                $ownerList[$User->getUserId()] = "{$User->getRealName()} ({$User->getUserName()})";
            }
        }
        asort($ownerList);
        
        $languageList = array('' => getGS("---Select language---"));
        foreach (Language::GetLanguages() as $Language) {
            $languageList[$Language->getLanguageId()] = $Language->getNativeName();   
        }
        asort($languageList);

        foreach ($data as $k => $v) {
            // clean user input
            if (!in_array($k, Blog::$m_html_allowed_fields)) {
                $data[$k] = camp_html_entity_decode_array($v);
            }
        }
        
        // load possible topic list
        foreach ($this->GetTopicTreeFlat() as $topicId => $topicName) {
            $topics[$topicId]  = $topicName;   
        }
        
        // get the topics used
        foreach ($this->getTopics() as $Topic) {            
            $active_topics[$Topic->getTopicId()] = $Topic->getName($this->getLanguageId());
        }

        $mask = array(
            'f_blog_id'    => array(
                'element'   => 'f_blog_id',
                'type'      => 'hidden',
                'constant'  => $data['blog_id']
            ),
            'language' => array(
                    'element'   => 'Blog[fk_language_id]',
                    'type'      => 'select',
                    'label'     => 'Language',
                    'default'   => $data['fk_language_id'],
                    'options'   => $languageList,
                    'required'  => true
            ),   
            'title'     => array(
                'element'   => 'Blog[title]',
                'type'      => 'text',
                'label'     => 'Title',
                'default'   => $data['title'],
                'required'  => true
            ),
            'tiny_mce'  => array(
                'element'   => 'tiny_mce',
                'text'      => '<script language="javascript" type="text/javascript" src="/javascript/tinymce/tiny_mce.js"></script>'.
                               '<script language="javascript" type="text/javascript" src="/javascript/tiny_mce/tiny_mce.js"></script>'.
                '<script language="javascript" type="text/javascript">'.
                '     tinyMCE.init({'.
                '     	mode : "exact",'.
                '        elements : "tiny_mce_box",'.
                '        theme : "advanced",'.
                '        plugins : "emotions, paste", '.
                '        paste_auto_cleanup_on_paste : true, '.
                '        theme_advanced_buttons1 : "bold, italic, underline, undo, redo, link, emotions", '.
                '        theme_advanced_buttons2 : "", '.
                '        theme_advanced_buttons3 : "" '.
                '     });'.
                '</script>',
                'type'      => 'static'
            ),
            'info'      => array(
                'element'   => 'Blog[info]',
                'type'      => 'textarea',
                'label'     => 'Info',
                'default'   => $data['info'],
                'required'  => true,
                'attributes'=> array('cols' => 60, 'rows' => 8, 'id' => 'tiny_mce_box')
            ),
            'feature'     => array(
                'element'   => 'Blog[feature]',
                'type'      => 'text',
                'label'     => 'Feature',
                'default'   => $data['feature'],
            ),
            'status' => array(
                'element'   => 'Blog[status]',
                'type'      => 'select',
                'label'     => 'Status',
                'default'   => $data['status'],
                'required'  => true,
                'options'   => array(
                    'online'        => 'online',
                    'offline'       => 'offline',
                    'moderated'     => 'moderated',
                    'readonly'      => 'read only',
                ),
                
            ),
            'admin_status' => array(
                'element'   => 'Blog[admin_status]',
                'type'      => 'select',
                'label'     => 'Admin Status',
                'default'   => $data['admin_status'],
                'required'  => true,
                'options'   => array(
                    'online'        => 'online',
                    'offline'       => 'offline',
                    'pending'       => 'pending',
                    'moderated'     => 'moderated',
                    'readonly'      => 'read only',
                ),
            ),
            'owner' => array(
                    'element'   => 'Blog[fk_user_id]',
                    'type'      => 'select',
                    'label'     => 'Owner',
                    'default'   => $data['fk_user_id'],
                    'options'   => $ownerList,
            ),        
            'admin_remark'      => array(
                'element'   => 'Blog[admin_remark]',
                'type'      => 'textarea',
                'label'     => 'Admin Remark',
                'default'   => $data['admin_remark'],
                'attributes'=> array('cols' => 60, 'rows' => 8)
            ),
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

        $mask = $this->getFormMask($p_owner, $p_admin);

        $form = new html_QuickForm('blog', 'post', $p_target, null, null, true);
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
        $mask = $this->getFormMask($p_admin);
        $form = new html_QuickForm('blog', 'post', '', null, null, true);
        FormProcessor::parseArr2Form($form, $mask);

        if ($form->validate()){
            $data = $form->getSubmitValues();

            foreach ($data['Blog'] as $k => $v) {
                // clean user input
                if (in_array($k, Blog::$m_html_allowed_fields)) {
                    $data['Blog'][$k] = strip_tags($v, Blog::$m_html_allowed_tags);
                } else {
                    $data['Blog'][$k] = htmlspecialchars_array($v);
                }
            }

            if ($data['f_blog_id']) {
                foreach ($data['Blog'] as $k => $v) {
                    $this->setProperty($k, $v);
                }
                return true;
            } elseif ($this->create(
                            isset($p_user_id) ? $p_user_id : $data['Blog']['fk_user_id'], 
                            $data['Blog']['fk_language_id'],
                            $data['Blog']['title'], 
                            $data['Blog']['info'], 
                            $data['Blog']['request_text'],
                            $data['Blog']['feature'])
                        ) {
                if ($p_owner && $data['Blog']['status'])         $this->setProperty('status',   $data['Blog']['status']);
                if ($p_admin && $data['Blog']['admin_status'])   $this->setProperty('admin_status',   $data['Blog']['admin_status']);
                if ($p_admin && $data['Blog']['admin_remark'])   $this->setProperty('admin_remark',   $data['Blog']['admin_remark']);

                return true;
            }
        }
        return false;

    }
    
    /**
     * Get the blog identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->getProperty('blog_id');   
    }
    
    /**
     * get the blog language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->getProperty('fk_language_id');
    }
      
    public static function GetBlogLanguageId($p_blog_id)
    {
        $tmpBlog= new Blog($p_blog_id);
        return $tmpBlog->getProperty('fk_language_id');
    }
    
    static public function GetTopicTree($p_key = 'PLUGIN_BLOG_ROOT_TOPIC_ID')
    {
        $root_id = SystemPref::Get($key);
        $tree = Topic::GetTree((int)$root_id);  
        
        return (array) $tree;
    }
    
    static public function GetTopicTreeFlat($p_key = 'PLUGIN_BLOG_ROOT_TOPIC_ID')
    {       
        foreach (self::GetTopicTree($p_key) as $branch) {
             $flat[] = end($branch)->getTopicId();
        }
        
        return (array) $flat;
    }
    
    public function setTopics(array $p_topics=array()) 
    {
        // store the topics
        $allowed_topics = self::GetTopicTreeFlat();
        
        BlogTopic::DeleteBlogTopics($this->getId());
        
        foreach ($p_topics as $topic_id) {
            if (in_array($topic_id, $allowed_topics, true)) {
                $BlogTopic = new BlogTopic($this->m_data['blog_id'], $topic_id);
                $BlogTopic->create();
            }
        }
    }
    
    public function getTopics() 
    {   
        foreach (BlogTopic::getAssignments($this->m_data['blog_id']) as $BlogTopic) {
            $topics[] = $BlogTopic->getTopic();      
        }
        return (array) $topics;
    }
    
    public static function getMoodList()
    {
        foreach (Topic::GetTree((int)SystemPref::Get('PLUGIN_BLOG_ROOT_MOOD_ID')) as $path) {
            $currentTopic = camp_array_peek($path, false, -1);
            $name = $currentTopic->getName($language_id);
            
            if (empty($name)) {
                // Backwards compatibility
                $name = $currentTopic->getName(1);
                if (empty($name)) {
                    continue;
                }
            }
            foreach ($path as $topicObj) {
                $name = $topicObj->getName($language_id);
                if (empty($name)) {
                    $name = $topicObj->getName(1);
                    if (empty($name)) {
                        $name = "-----";
                    }
                }
                $value = htmlspecialchars($name);
            }
            $selected = $currentTopic->getTopicId() == SystemPref::Get('PLUGIN_BLOG_ROOT_MOOD_ID') ? 'selected' : '';
            $options[$currentTopic->getTopicId()] = $value;
        }
        
        return $options;
    }
    
    /**
     * If we modify the admin status, 
     * the publish date is modified too.
     *
     * @param string $p_name
     * @param sring $p_value
     */
    function setProperty($p_name, $p_value)
    {
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
        
        if ($p_name == 'topics') {
            return $this->setTopics($p_value);   
        }
        
        return parent::setProperty($p_name, $p_value);
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
            if (empty($comparisonOperation)) {
                continue;
            }
            
            $whereCondition = $comparisonOperation['left'] . ' '
            . $comparisonOperation['symbol'] . " '"
            . $comparisonOperation['right'] . "' ";
            $selectClauseObj->addWhere($whereCondition);
        }
        
        // sets the columns to be fetched
        $tmpBlog = new Blog();
		$columnNames = $tmpBlog->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpBlog->getDbTableName();
        $selectClauseObj->setTable($mainTblName);
        unset($tmpBlog);
                
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
        $blogRes = $g_ado_db->SelectLimit($sqlQuery, $p_limit, $p_start);
        
        // builds the array of blog objects
        $blogsList = array();
        while ($blog = $blogRes->FetchRow()) {
            $blogObj = new Blog($blog['blog_id']);
            if ($blogObj->exists()) {
                $blogsList[] = $blogObj;
            }
        }

        return $blogsList;
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

        $comparisonOperation['left'] = BlogsList::$s_parameters[strtolower($p_param->getLeftOperand())]['field'];

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
            $dbField = BlogsList::$s_parameters[substr($field, 2)]['field'];

            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        if (count($order) == 0) {
            $order['blog_id'] = 'asc';
        }
        return $order;
    } // fn ProcessListOrder
}
?>
