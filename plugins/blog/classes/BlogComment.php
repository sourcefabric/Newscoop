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

class BlogComment extends DatabaseObject {
    /**
	 * The column names used for the primary key.
	 * @var array
	 */
    var $m_keyColumnNames       = array('comment_id');
    var $m_keyIsAutoIncrement   = true;
    var $m_dbTableName          = 'plugin_blog_comment';

    var $m_columnNames = array(
        'comment_id',
        'fk_entry_id',
        'fk_blog_id',
        'fk_user_id',
        'user_name',
        'user_email',
        'published',
        'status',
        'title',
        'content',
        'mood',
        'admin_status',
        'feature'
    );
    
    static $m_html_allowed_fields = array('content');
    static $m_html_allowed_tags = '<strong><em><u><a><img><p>';

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
        parent::setProperty($p_name, $p_value); 
    
        if ($p_name == 'status' || $p_name == 'admin_status') {
            BlogEntry::triggerCounter($this->getProperty('fk_entry_id'));   
        }   
    }


    /**
	 * A way for internal functions to call the superclass create function.
	 * @param array $p_values
	 */
    function __create($p_values=null) { return parent::create($p_values); }
    
    
    function create($p_entry_id, $p_user_id, $p_user_name, $p_user_email, $p_title=null, $p_content=null, $p_mood=null)
    {
        global $Campsite;

		// Create the record
		$values = array(
		  'fk_entry_id'   => $p_entry_id,
		  'fk_user_id'    => $p_user_id,
		  'user_name'   => $p_poster_name,
		  'user_email'  => $p_poster_email,
		  'title'         => $p_title,
		  'content'       => $p_content,
		  'mood'          => $p_mood, 
		);

		$success = parent::create($values);
		
		if (!$success) {
			return false;
		}

		$this->fetch();
		
		BlogEntry::triggerCounter($p_entry_id);

        return true; 
    }
    
    function delete()
    {
        parent::delete();
       
        BlogEntry::triggerCounter($this->getProperty('fk_entry_id'));   
    }
    
    function getData()
    {
        global $Campsite;
        
        return $this->m_data;    
    }
    
    function _buildQueryStr($p_cond, $p_checkParent, $p_order=null)
    {        
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
         if (array_key_exists('mood', $p_cond) && strlen($p_cond['mood'])) {
            $cond .= " AND c.mood IN ({$p_cond['mood']})";    
        }
        
        $queryStr = "SELECT     c.comment_id
                     FROM       mod_blog_comments AS c, 
                                mod_blog_entries  AS e,
                                mod_blog_blogs    AS b
                     WHERE      c.fk_entry_id = e.fk_entry_id     AND
                                e.IdBlog  = b.IdBlog 
                                $cond ";
        if (strlen($p_order)) {
            $queryStr .= "ORDER BY   comment_id $p_order";
        }
            
        return $queryStr;
    }
    
    function getComments($p_cond=array(), $p_currPage=0, $p_perPage=10, $p_checkParent=false, $p_order='ASC')
    {
        global $Campsite;
        
        $queryStr   = BlogComment::_buildQueryStr($p_cond, $p_checkParent, $p_order);       
        $query      = $Campsite['db']->SelectLimit($queryStr, $p_perPage, ($p_currPage-1) * $p_perPage);		
		$comments   = array();
		
		while ($row = $query->FetchRow()) { 
		    $tmpComment =& new BlogComment($row['comment_id']);
		    $comments[] = $tmpComment;
		}
		return $comments;
    }
    
    function countComments($p_cond=array(), $p_checkParent=false)
    {
        global $Campsite;
        
        $queryStr   = BlogComment::_buildQueryStr($p_cond, $p_checkParent); 
        $query      = $Campsite['db']->Execute($queryStr); #
        
        return $query->RecordCount();  
    }
    
        
    function getEntryId($p_comment_id)
    {
        $tmpComment =& new BlogComment($p_comment_id);
        return $tmpComment->getProperty('fk_entry_id');           
    }
    
    function _getFormMask($p_admin=false, $p_owner=false)
    {
        $data = $this->getData();

        foreach ($data as $k => $v) {
            // clean user input
            if (!in_array($k, BlogComment::$m_html_allowed_fields)) { 
                $data[$k] = html_entity_decode_array($v);
            }
        }
        
        $mask = array(
            'action'    => array(
                'element'   => 'action',
                'type'      => 'hidden',
                'constant'  => $data['comment_id'] ? 'comment_edit' : 'comment_create'
            ),
            'comment_id'    => $data['comment_id'] ? array(
                'element'   => 'comment_id',
                'type'      => 'hidden',
                'constant'  => $data['comment_id'],
                'required'  => true           
            ) : null,            
            'fk_entry_id'    => $data['fk_entry_id'] ? array(
                'element'   => 'fk_entry_id',
                'type'      => 'hidden',
                'constant'  => $data['fk_entry_id'],
                'required'  => true           
            ) : null,
            'page'      => $_REQUEST['page'] ? array(
                'element'   => 'page',
                'type'      => 'hidden',
                'constant'  => $_REQUEST['page']
            ) : null,
            'tiny_mce'  => array(
                'element'   => 'tiny_mce',
                'text'      => '<script language="javascript" type="text/javascript" src="/phpwrapper/tiny_mce/tiny_mce.js"></script>'.
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
            'title'     => array(
                'element'   => 'BlogComment[title]',
                'type'      => 'text',
                'label'     => 'Titel',
                'default'   => $data['title'],
                'required'  => true            
            ),
            'content'      => array(
                'element'   => 'BlogComment[content]',
                'type'      => 'textarea',
                'label'     => 'Kommentar',
                'default'   => $data['content'],
                'required'  => true,
                'attributes'=> array('cols' => 40, 'rows' => 5, 'id' => 'tiny_mce_box')            
            ),  
            /*          
            'mood'      => array(
                'element'   => 'BlogComment[mood]',
                'type'      => 'checkbox_multi',
                'label'     => 'mood',
                'default'   => explode(', ', $data['mood']),
                'options'   => $this->_getmoodList()      
            )
            */
        );
        
        if ($p_owner && $data['comment_id']) {
            $mask += array(            
                'status' => array(
                    'element'   => 'BlogComment[status]',
                    'type'      => 'select',
                    'label'     => 'status',
                    'default'   => $data['status'],
                    'options'   => array(
                                    'online'    => 'online',
                                    'offline'   => 'offline'
                                   ),
                    'required'  => true            
                )
            );
        };
        
        if ($p_admin) {
            $mask += array(            
                'admin_status' => array(
                    'element'   => 'BlogComment[admin_status]',
                    'type'      => 'select',
                    'label'     => 'Admin status',
                    'default'   => $data['admin_status'],
                    'options'   => array(
                                    'online'    => 'online',
                                    'offline'   => 'offline',
                                   ),
                    'required'  => true            
                )
            );
        };
        
        $mask += array(
            /*
            $p_admin ? null : 'captcha_image' => array(
                'element'       => 'captcha_image',
                'type'          => 'image',
                'src'           => '/look/img/captcha/0f60a7c97b199d88d028c8f483e.jpg',
                'attributes'    => array('onclick' => 'return false')
            ),
            $p_admin ? null : 'captcha'       => array(
                'element'       => 'captcha',
                'type'          => 'text',
                'label'         => 'Code:',
                'required'      => true,
                'requiredmsg'   => 'Bitte die Zeichenfolge auf dem Bild in das darunterliegende Feld eingeben.',
                'attributes'    => array('class' => 'verschicken'),
            ),
            */
            'reset'     => array(
                'element'   => 'reset',
                'type'      => 'reset',
                'label'     => 'Zurücksetzen',
                'groupit'   => true
            ),
            'xsubmit'     => array(
                'element'   => 'xsubmit',
                'type'      => 'button',
                'label'     => 'Abschicken',
                'attributes'=> array('onclick' => 'this.form.submit()'),
                'groupit'   => true
            ), 
            'cancel'     => array(
                'element'   => 'cancel',
                'type'      => 'button',
                'label'     => 'Cancel',
                'attributes' => array('onClick' => 'history.back()'),
                'groupit'   => true
            ),
            'buttons'   => array(
                'group'     => array('xsubmit', 'reset')
            )       
        );
        
        return $mask;   
    }
    
    function getForm($p_target, $p_add_hidden_vars=array(), $p_admin=false, $p_owner=false, $p_html=false)
    {
        require_once 'HTML/QuickForm.php';
        require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
              
        $mask = $this->_getFormMask($p_admin, $p_owner);

        foreach ($p_add_hidden_vars as $k => $v) {       
            $mask[] = array(
                'element'   => $k,
                'type'      => 'hidden',
                'constant'  => $v
            );   
        } 
        
        $form =& new html_QuickForm('blog_comment', 'post', $p_target, null, null, true);
        parseArr2Form(&$form, &$mask); 
        
        if ($p_html) {
            return $form->toHTML();    
        } else {
            require_once 'HTML/QuickForm/Renderer/Array.php';
            
            $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);
            
            return $renderer->toArray();
        } 
    }
    
    function store($p_admin=false, $p_owner=false, $p_user_id)
    {
        require_once 'HTML/QuickForm.php';
        require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
              
        $mask = $this->_getFormMask($p_admin, $p_owner);
        #mergePostParams(&$mask);
      
        $form =& new html_QuickForm('blog_comment', 'post', '', null, null, true); 
        parseArr2Form(&$form, &$mask); 
        
        if ($form->validate()) {
            $data = $form->getSubmitValues();
            
            foreach ($data['BlogComment'] as $k => $v) {
                // clean user input
                if (in_array($k, BlogComment::$m_html_allowed_fields)) { 
                    $data['BlogComment'][$k] = strip_tags($v, Blog::$m_html_allowed_tags);
                } else {
                    $data['BlogComment'][$k] = htmlspecialchars($v);
                }
            }
            
            if ($data['comment_id']) {
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
                BlogEntry::triggerCounter(BlogComment::getEntryId($data['comment_id']));
                return true;
                
            } else {
                if (is_array($data['BlogComment']['mood'])) {
                    unset ($string);
                    foreach($data['BlogComment']['mood'] as $key => $value) {
                        if ($value) {
                            $string .= "$key, ";   
                        }    
                    }
                    $mood = substr($string, 0, -2);
                }
                if ($this->create($data['fk_entry_id'], $p_user_id, $data['BlogComment']['title'], $data['BlogComment']['content'], $mood)) {
                    if ($p_owner && $data['BlogComment']['status'])         $this->setProperty('status', $data['BlogComment']['status']);
                    if ($p_admin && $data['BlogComment']['admin_status'])    $this->setProperty('admin_status', $data['BlogComment']['admin_status']);
                    BlogEntry::triggerCounter($this->getProperty('fk_entry_id'));  
                      
                    return true;    
                }
                return false; 
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
     * Get the blog identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->getProperty('comment_id');   
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
