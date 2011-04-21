<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <sebastian.goebel@web.de>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

class BlogComment extends DatabaseObject {
    /**
	 * The column names used for the primary key.
	 * @var array
	 */
    var $m_keyColumnNames       = array('comment_id');
    var $m_keyIsAutoIncrement   = true;
    var $m_dbTableName          = 'plugin_blog_comment';
    static $s_dbTableName       = 'plugin_blog_comment';

    var $m_columnNames = array(
        'comment_id',
        'fk_entry_id',
        'fk_blog_id',
        'fk_language_id',
        'fk_user_id',
        'user_name',
        'user_email',
        'date',
        'status',
        'title',
        'content',
        'fk_mood_id',
        'admin_status',
        'feature',
        'last_modified'
    );

    static $m_html_allowed_fields = array();

    /**
	 * Construct by passing in the primary key to access the article in
	 * the database.
	 *
	 * @param int $p_languageId
	 * @param int $p_articleNumber
	 *		Not required when creating an article.
	 */
    function BlogComment($p_comment_id=null, $p_entry_id=null)
    {
        parent::DatabaseObject($this->m_columnNames);

        $this->m_data['comment_id'] = $p_comment_id;

        if ($this->keyValuesExist()) {
            $this->fetch();
        } elseif ($p_entry_id) {
            $this->m_data['fk_entry_id'] = $p_entry_id;
        }
    } // constructor

    function setProperty($p_name, $p_value)
    {
        $result = parent::setProperty($p_name, $p_value);

        if ($p_name == 'status' || $p_name == 'admin_status') {
            BlogEntry::TriggerCounters($this->getProperty('fk_entry_id'));
        }

        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        return $result;
    }

    function getProperty($p_name)
    {
        switch ($p_name) {
            case 'content':
                global $Campsite;
                $content = preg_replace('!(../)*js/tinymce/plugins/emotions/img/!', $Campsite['WEBSITE_URL'].'/js/tinymce/plugins/emotions/img/', parent::getProperty($p_name));
                return $content;
            break;

            default:
                return parent::getProperty($p_name);
            break;
        }
    }


    /**
	 * A way for internal functions to call the superclass create function.
	 * @param array $p_values
	 */
    function __create($p_values=null) { return parent::create($p_values); }


    function create($p_entry_id, $p_user_id, $p_user_name, $p_user_email, $p_title=null, $p_content=null, $p_mood_id=null)
    {
		// Create the record
		$values = array(
		  'fk_entry_id'   => $p_entry_id,
		  'fk_blog_id'    => BlogEntry::GetBlogId($p_entry_id),
		  'fk_language_id'=> BlogEntry::GetEntryLanguageId($p_entry_id),
		  'fk_user_id'    => $p_user_id,
		  'user_name'     => $p_user_name,
		  'user_email'    => $p_user_email,
		  'title'         => $p_title,
		  'content'       => $p_content,
		  'fk_mood_id'    => $p_mood_id,
		  'date'     => date('Y-m-d H:i:s')
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
        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        return true;
    }

    function delete()
    {
        $entry_id = $this->getProperty('fk_entry_id');
        parent::delete();
        BlogEntry::TriggerCounters($entry_id);
        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
    }

    function _buildQueryStr($p_cond, $p_checkParent, $p_order=null)
    {
        $blogs_tbl      = Blog::$s_dbTableName;
        $entries_tbl    = BlogEntry::$s_dbTableName;
        $comments_tbl   = BlogComment::$s_dbTableName;

        if (array_key_exists('fk_entry_id', $p_cond)) {
            $cond .= " AND c.fk_entry_id = {$p_cond['fk_entry_id']}";
        }
        if (array_key_exists('status', $p_cond)) {
            $cond .= " AND c.status = '{$p_cond['status']}'";
            if ($p_checkParent) $cond .= " AND b.status = '{$p_cond['status']}' AND e.status =  '{$p_cond['status']}'";
        }
        if (array_key_exists('admin_status', $p_cond)) {
            $cond .= " AND c.admin_status = '{$p_cond['admin_status']}'";
            if ($p_checkParent) $cond .= " AND b.admin_status = '{$p_cond['status']}' AND e.admin_status =  '{$p_cond['status']}'";
        }
         if (array_key_exists('fk_mood_id', $p_cond) && strlen($p_cond['fk_mood_id'])) {
            $cond .= " AND c.fk_mood_id IN ({$p_cond['fk_mood_id']})";
        }

        $queryStr = "SELECT     c.comment_id
                     FROM       $comments_tbl AS c,
                                $entries_tbl  AS e,
                                $blogs_tbl    AS b
                     WHERE      c.fk_entry_id = e.entry_id     AND
                                e.fk_blog_id  = b.blog_id
                                $cond ";
        if (strlen($p_order)) {
            $queryStr .= "ORDER BY   comment_id $p_order";
        }

        return $queryStr;
    }

    function getComments($p_cond=array(), $p_currPage=0, $p_perPage=10, $p_checkParent=false, $p_order='ASC')
    {
        global $g_ado_db;

        $queryStr   = BlogComment::_buildQueryStr($p_cond, $p_checkParent, $p_order);
        $query      = $g_ado_db->SelectLimit($queryStr, $p_perPage, ($p_currPage-1) * $p_perPage);
		$comments   = array();

		while ($row = $query->FetchRow()) {
		    $tmpComment = new BlogComment($row['comment_id']);
		    $comments[] = $tmpComment;
		}
		return $comments;
    }

    function countComments($p_cond=array(), $p_checkParent=false)
    {
        global $g_ado_db;

        $queryStr   = BlogComment::_buildQueryStr($p_cond, $p_checkParent);
        $query      = $g_ado_db->Execute($queryStr); #

        return $query->RecordCount();
    }


    static function GetEntryId($p_comment_id)
    {
        $tmpComment = new BlogComment($p_comment_id);
        return $tmpComment->getProperty('fk_entry_id');
    }

    function getBlog()
    {
        static $Blog;

        if (!is_object($Bog)) {
            $Blog = new Blog($this->getProperty('fk_blog_id'));
        }
        return $Blog;
    }

    function getEntry()
    {
        static $Entry;

        if (!is_object($Entry)) {
            $Entry = new $Entry($this->getProperty('fk_entry_id'));
        }
        return $Entry;
    }

    static function GetBlogId($p_comment_id)
    {
        $tmpComment = new BlogComment($p_comment_id);
        return $tmpComment->getProperty('fk_blog_id');
    }

    function _getFormMask($p_admin=false, $p_owner=false)
    {
        $mask = array(
            'f_comment_id' => array(
                'element'   => 'f_comment_id',
                'type'      => 'hidden',
                'constant'  => $this->getProperty('comment_id'),
            ),
            'f_entry_id' => array(
                'element'   => 'f_entry_id',
                'type'      => 'hidden',
                'constant'  => $this->getProperty('fk_entry_id'),
            ),
            SecurityToken::SECURITY_TOKEN => array(
            	'element'   => SecurityToken::SECURITY_TOKEN,
            	'type'      => 'hidden',
            	'constant'  => SecurityToken::GetToken()
            ),
            /*
            'tiny_mce'  => array(
                'element'   => 'tiny_mce',
                'text'      => '<script language="javascript" type="text/javascript" src="/js/tinymce/tiny_mce.js"></script>'.
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
            */
            'title'     => array(
                'element'   => 'BlogComment[title]',
                'type'      => 'text',
                'label'     => getGS('Title'),
                'default'   => html_entity_decode($this->getProperty('title'))
            ),
            'user_name'     => array(
                'element'   => 'BlogComment[user_name]',
                'type'      => 'text',
                'label'     => getGS('Poster name'),
                'default'   => html_entity_decode($this->getProperty('user_name'))
            ),
            'user_email'     => array(
                'element'   => 'BlogComment[user_email]',
                'type'      => 'text',
                'label'     => getGS('EMail'),
                'default'   => html_entity_decode($this->getProperty('user_email'))
            ),
            'content'      => array(
                'element'   => 'BlogComment[content]',
                'type'      => 'textarea',
                'label'     => getGS('Comment'),
                'default'   => $this->getProperty('content'),
                'required'  => true,
                'attributes'=> array('cols' => 60, 'rows' => 8, 'id' => 'tiny_mce_box')
            ),
            'mood'      => array(
                'element'   => 'BlogComment[fk_mood_id]',
                'type'      => 'select',
                'label'     => getGS('Mood'),
                'default'   => $this->getProperty('fk_mood_id'),
                'options'   => Blog::GetMoodList($this->getProperty('fk_language_id') ?
                                    $this->getProperty('fk_language_id') :
                                    BlogEntry::GetEntryLanguageId($this->getProperty('fk_entry_id')))
            ),
            'status' => array(
                'element'   => 'BlogComment[status]',
                'type'      => 'select',
                'label'     => getGS('Status'),
                'default'   => $this->getProperty('status'),
                'options'   => array(
                                'online'    => getGS('online'),
                                'offline'   => getGS('offline'),
                                'pending'   => getGS('pending')
                               ),
                'required'  => true
            ),
            'admin_status' => array(
                'element'   => 'BlogComment[admin_status]',
                'type'      => 'select',
                'label'     => getGS('Admin status'),
                'default'   => $this->getProperty('admin_status'),
                'options'   => array(
                                'pending'   => getGS('pending'),
                                'online'    => getGS('online'),
                                'offline'   => getGS('offline'),
                               ),
                'required'  => true
            ),
            'reset'     => array(
                'element'   => 'reset',
                'type'      => 'reset',
                'label'     => getGS('Reset'),
                'groupit'   => true
            ),
            'xsubmit'     => array(
                'element'   => 'xsubmit',
                'type'      => 'button',
                'label'     => getGS('Submit'),
                'attributes'=> array('onclick' => 'if (this.form.onsubmit()) this.form.submit()'),
                'groupit'   => true
            ),
            'cancel'     => array(
                'element'   => 'cancel',
                'type'      => 'button',
                'label'     => getGS('Cancel'),
                'attributes' => array('onClick' => 'window.close()'),
                'groupit'   => true
            ),
            'buttons'   => array(
                'group'     => array('cancel', 'reset', 'xsubmit')
            )
        );

        return $mask;
    }

    function getForm($p_target, $p_admin=true, $p_html=true)
    {
        require_once 'HTML/QuickForm.php';

        $mask = $this->_getFormMask($p_admin, $p_owner);

        $form = new html_QuickForm('blog_comment', 'post', $p_target, null, null, true);
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
        $mask = $this->_getFormMask($p_admin, $p_owner);
        $form = new html_QuickForm('blog_comment', 'post', '', null, null, true);
        FormProcessor::parseArr2Form($form, $mask);

        if ($form->validate() && SecurityToken::isValid()) {
            $data = $form->getSubmitValues();

            foreach ($data['BlogComment'] as $k => $v) {
                // clean user input
                if (!in_array($k, BlogComment::$m_html_allowed_fields)) {
                    $data['BlogComment'][$k] = htmlspecialchars_array($v);
                }
            }

            if ($data['f_comment_id']) {
                foreach ($data['BlogComment'] as $k => $v) {
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
                BlogEntry::TriggerCounters(BlogComment::GetEntryId($data['comment_id']));
                return true;

            } elseif ($this->create(
                            $data['f_entry_id'],
                            $p_user_id,
                            $data['BlogComment']['user_name'],
                            $data['BlogComment']['user_email'],
                            $data['BlogComment']['title'],
                            $data['BlogComment']['content'],
                            $data['BlogComment']['fk_mood_id'])) {

                // admin and owner can override status setting
                if ($p_admin && $data['BlogComment']['admin_status']) {
                    $this->setProperty('admin_status', $data['BlogComment']['admin_status']);
                }
                if ($p_owner && $data['BlogComment']['status']) {
                    $this->setProperty('status', $data['BlogComment']['status']);
                }

                BlogEntry::TriggerCounters($this->getProperty('fk_entry_id'));
                return true;
            }
        }
        return false;

    }

    function _getTagList()
    {
        return array('a' => 'film', 'b' => 'poesie', 'm' => 'multimedia');
    }

    function _getmoodList()
    {
        return array('a' => 'happy', 'b' => 'sad');
    }

    /**
     * Get the blogcomment identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->getProperty('comment_id');
    }

    /**
     * Get the blogcomment language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->getProperty('fk_language_id');
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
            . $g_ado_db->escape($comparisonOperation['right']) . "' ";
            $selectClauseObj->addWhere($whereCondition);
        }

        // sets the columns to be fetched
        $tmpBlogComment = new BlogComment();
		$columnNames = $tmpBlogComment->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpBlogComment->getDbTableName();
        $selectClauseObj->setTable($mainTblName);
        unset($tmpBlogComment);

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
        $blogCommentRes = $g_ado_db->SelectLimit($sqlQuery, $p_limit, $p_start);

        // builds the array of blogComment objects
        $blogCommentsList = array();
        while ($blogComment = $blogCommentRes->FetchRow()) {
            $blogCommentObj = new BlogComment($blogComment['comment_id']);
            if ($blogCommentObj->exists()) {
                $blogCommentsList[] = $blogCommentObj;
            }
        }

        return $blogCommentsList;
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

        $comparisonOperation['left'] = BlogCommentsList::$s_parameters[strtolower($p_param->getLeftOperand())]['field'];

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
            $dbField = BlogCommentsList::$s_parameters[substr($field, 2)]['field'];

            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        if (count($order) == 0) {
            $order['fk_entry_id'] = 'asc';
            $order['comment_id'] = 'asc';
        }
        return $order;
    }
}
?>
