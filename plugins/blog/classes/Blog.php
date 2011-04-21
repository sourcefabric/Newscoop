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

class Blog extends DatabaseObject {
    /**
	 * The column names used for the primary key.
	 * @var array
	 */
    var $m_keyColumnNames       = array('blog_id');
    var $m_keyIsAutoIncrement   = true;
    var $m_dbTableName          = 'plugin_blog_blog';
    static $s_dbTableName       = 'plugin_blog_blog';

    var $m_columnNames = array(
        'blog_id',
        'fk_language_id',
        'fk_user_id',
        'title',
        'date',
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
            $this->m_data['images'] = BlogImageHelper::GetImagePaths('blog', $p_blog_id, true, true);
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
            'feature'       => $p_feature,
            'date'     => date('Y-m-d H:i:s')
        );

        $success = parent::create($values);

        if (!$success) {
            return false;
        }

        $this->fetch();
        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        return true;
    }

    function delete()
    {
        $blog_id =  $this->getProperty('blog_id');

        foreach (BlogEntry::getEntries(array('fk_blog_id' => $blog_id)) as $Entry) {
            $Entry->delete();
        }

        parent::delete();
        BlogImageHelper::RemoveImageDerivates('entry', $entry_id);
        BlogTopic::OnBlogDelete($blog_id);
        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
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
                     FROM       {self::$s_dbTableName}
                     WHERE      1 $cond
                     ORDER BY   blog_id DESC";
        return $queryStr;
    }

    function getBlogs($p_cond, $p_currPage=0, $p_perPage=20)
    {
        global $g_ado_db;

        $queryStr = self::BuildQueryStr($p_cond);

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

        $queryStr   = self::BuildQueryStr($p_cond);
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

        $blogs_tbl = self::$s_dbTableName;
        $entries_tbl = BlogEntry::$s_dbTableName;

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
        global $g_user;

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
            if (!in_array($k, self::$m_html_allowed_fields)) {
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

        $languageSelectedObj = new Language($data['fk_language_id']);
        $editorLanguage = !empty($_COOKIE['TOL_Language']) ? $_COOKIE['TOL_Language'] : $languageSelectedObj->getCode();

        $mask = array(
            'f_blog_id'    => array(
                'element'   => 'f_blog_id',
                'type'      => 'hidden',
                'constant'  => $data['blog_id']
            ),
            SecurityToken::SECURITY_TOKEN => array(
            	'element'   => SecurityToken::SECURITY_TOKEN,
            	'type'      => 'hidden',
            	'constant'  => SecurityToken::GetToken()
            ),
            'language' => array(
                    'element'   => 'Blog[fk_language_id]',
                    'type'      => 'select',
                    'label'     => getGS('Language'),
                    'default'   => $data['fk_language_id'],
                    'options'   => $languageList,
                    'required'  => true
            ),
            'title'     => array(
                'element'   => 'Blog[title]',
                'type'      => 'text',
                'label'     => getGS('Title'),
                'default'   => $data['title'],
                'required'  => true
            ),
            'tiny_mce'  => array(
                'element'   => 'tiny_mce',
                'text'      => self::GetEditor('tiny_mce_box', $g_user, $editorLanguage),
                'type'      => 'static'
            ),
            'info'      => array(
                'element'   => 'Blog[info]',
                'type'      => 'textarea',
                'label'     => getGS('Info'),
                'default'   => $data['info'],
                'required'  => true,
                'attributes'=> array('cols' => 86, 'rows' => 16, 'id' => 'tiny_mce_box', 'class' => 'tinymce')
            ),
            'feature'     => array(
                'element'   => 'Blog[feature]',
                'type'      => 'text',
                'label'     => getGS('Feature'),
                'default'   => $data['feature'],
            ),
            'status' => array(
                'element'   => 'Blog[status]',
                'type'      => 'select',
                'label'     => getGS('Status'),
                'default'   => $data['status'],
                'required'  => true,
                'options'   => array(
                    'online'        => getGS('online'),
                    'offline'       => getGS('offline'),
                    'moderated'     => getGS('moderated'),
                    'readonly'      => getGS('read only'),
                ),

            ),
            'admin_status' => array(
                'element'   => 'Blog[admin_status]',
                'type'      => 'select',
                'label'     => getGS('Admin status'),
                'default'   => $data['admin_status'],
                'required'  => true,
                'options'   => array(
                    'online'        => getGS('online'),
                    'offline'       => getGS('offline'),
                    'pending'       => getGS('pending'),
                    'moderated'     => getGS('moderated'),
                    'readonly'      => getGS('read only'),
                ),
            ),
            'owner' => array(
                    'element'   => 'Blog[fk_user_id]',
                    'type'      => 'select',
                    'label'     => getGS('Owner'),
                    'default'   => $data['fk_user_id'],
                    'options'   => $ownerList,
            ),
            'image'     => array(
                'element'   => 'Blog_Image',
                'type'      => 'file',
                'label'     => getGS('Image (.jpg, .png, .gif)'),
            ),
            'image_display'  => array(
                'element'   => 'image_display',
                'text'      => '<img src="'.$data['images']['100x100'].'">',
                'type'  => 'static',
                'groupit'   => true
            ),
            'image_remove' => array(
                'element'   => 'Blog_Image_remove',
                'type'      => 'checkbox',
                'label'     => getGS('Remove this image'),
                'groupit'   => true
            ),
            'image_label'  => array(
                'element'   => 'image_label',
                'text'      => getGS('Remove this image'),
                'type'  => 'static',
                'groupit'   => true
            ),
            'image_group' =>  isset($data['images']['100x100']) ? array(
                'group'     => array('image_display', 'Blog_Image_remove', 'image_label'),

            ) : null,
            'admin_remark'      => array(
                'element'   => 'Blog[admin_remark]',
                'type'      => 'textarea',
                'label'     => getGS('Admin remark'),
                'default'   => $data['admin_remark'],
                'attributes'=> array('cols' => 86, 'rows' => 10)
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
                'attributes'=> array('onclick' => 'tinyMCE.triggerSave(); if (this.form.onsubmit()) this.form.submit()'),
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

        if ($form->validate() && SecurityToken::isValid()){
            $data = $form->getSubmitValues(true);

            foreach ($data['Blog'] as $k => $v) {
                // clean user input
                if (!in_array($k, self::$m_html_allowed_fields)) {
                    $data['Blog'][$k] = htmlspecialchars_array($v);
                }
            }

            if ($data['f_blog_id']) {
                foreach ($data['Blog'] as $k => $v) {
                    $this->setProperty($k, $v);
                }

                if ($data['Blog_Image_remove']) {
                    BlogImageHelper::RemoveImageDerivates('blog', $data['f_blog_id']);
                }
                if ($data['Blog_Image']) {
                    BlogImageHelper::StoreImageDerivates('blog', $data['f_blog_id'], $data['Blog_Image']);
                }

                return true;

            } elseif ($this->create(
                            isset($p_user_id) ? $p_user_id : $data['Blog']['fk_user_id'],
                            $data['Blog']['fk_language_id'],
                            $data['Blog']['title'],
                            $data['Blog']['info'],
                            $data['Blog']['request_text'],
                            $data['Blog']['feature'])) {

                if ($data['Blog']['status']) {
                    $this->setProperty('status', $data['Blog']['status']);
                }
                if ($p_admin && $data['Blog']['admin_status']) {
                    $this->setProperty('admin_status', $data['Blog']['admin_status']);
                }
                if ($p_admin && $data['Blog']['admin_remark']) {
                    $this->setProperty('admin_remark', $data['Blog']['admin_remark']);
                }
                if ($data['Blog_Image']) {
                    BlogImageHelper::StoreImageDerivates('blog', $this->getProperty('blog_id'), $data['BlogEntry_Image']);
                }

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
        $root_id = SystemPref::Get($p_key);
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

    public static function GetMoodList($p_language_id)
    {
        $options = array(0 => '------');

        foreach (Topic::GetTree((int)SystemPref::Get('PLUGIN_BLOG_ROOT_MOOD_ID')) as $path) {
            $currentTopic = camp_array_peek($path, false, -1);
            $name = $currentTopic->getName($p_language_id);

            if (empty($name)) {
                // Backwards compatibility
                $name = $currentTopic->getName(1);
                if (empty($name)) {
                    continue;
                }
            }
            foreach ($path as $topicObj) {
                $name = $topicObj->getName($p_language_id);
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

        return (array)$options;
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
        /*
        if ($p_name == 'admin_status') {
            switch ($p_value) {
                case 'online':
                case 'moderated':
                case 'readonly':
                    parent::setProperty('date', date('Y-m-d H:i:s'));
                break;

                case 'offline':
                case 'pending':
                    parent::setProperty('date', null);
                break;
            }
        }
        */

        if ($p_name == 'topics') {
            $return = $this->setTopics($p_value);
            $CampCache = CampCache::singleton();
            $CampCache->clear('user');
            return $return;
        }

        if ($p_name == 'fk_language_id') {
            $this->onSetLanguage($p_value);
        }

        $return = parent::setProperty($p_name, $p_value);
        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        return $return;
    }

    private function onSetLanguage($p_language_id)
    {
        if ($p_language_id == $this->getLanguageId()) {
            return;
        }

        global $g_ado_db;

        $entryTbl   = BlogEntry::$s_dbTableName;
        $commentTbl = BlogComment::$s_dbTableName;

        $queryStr1 = "UPDATE $entryTbl
                      SET fk_language_id = $p_language_id
                      WHERE fk_blog_id = {$this->getId()}";
        $g_ado_db->Execute($queryStr1);

        $queryStr1 = "UPDATE $commentTbl
                      SET fk_language_id = $p_language_id
                      WHERE fk_blog_id = {$this->getId()}";
        $g_ado_db->Execute($queryStr1);

        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
    }

    /**
     * @param array p_dbColumns
     * @param object p_user The User object
     * @param int p_editorLanguage The current or selected language
     *
     * @return void
     */
    public static function GetEditor($p_box_id, $p_user, $p_editorLanguage)
    {
    	global $Campsite;

    	$stylesheetFile = '/admin/articles/article_stylesheet.css';

    	/** STEP 2 ********************************************************
    	 * Now, what are the plugins you will be using in the editors
    	 * on this page.  List all the plugins you will need, even if not
    	 * all the editors will use all the plugins.
    	 ******************************************************************/
    	$plugins = array();
    	if ($p_user->hasPermission('EditorCopyCutPaste')) {
    	    $plugins[] = 'paste';
    	}
    	if ($p_user->hasPermission('EditorFindReplace')) {
    	  $plugins[] = 'searchreplace';
    	}
    	if ($p_user->hasPermission('EditorEnlarge')) {
    	    $plugins[] = 'fullscreen';
    	}
    	if ($p_user->hasPermission('EditorTable')) {
    	    $plugins[] = 'table';
    	}
    	if ($p_user->hasPermission('EditorLink')) {
    	    $plugins[] = 'campsiteinternallink';
    	}
    	$plugins_list = implode(",", $plugins);

    	$statusbar_location = "none";
    	if ($p_user->hasPermission('EditorStatusBar')) {
    	    $statusbar_location = "bottom";
    	}

    	/** STEP 3 ********************************************************
    	 * We create a default configuration to be used by all the editors.
    	 * If you wish to configure some of the editors differently this
    	 * will be done in step 4.
    	 ******************************************************************/
    	$toolbar1 = array();
    	if ($p_user->hasPermission('EditorBold')) {
    	    $toolbar1[] = "bold";
    	}
    	if ($p_user->hasPermission('EditorItalic')) {
    	    $toolbar1[] = "italic";
    	}
    	if ($p_user->hasPermission('EditorUnderline')) {
    	    $toolbar1[] = "underline";
    	}
    	if ($p_user->hasPermission('EditorStrikethrough')) {
    	    $toolbar1[] = "strikethrough";
    	}
    	if ($p_user->hasPermission('EditorTextAlignment')) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "justifyleft";
    	    $toolbar1[] = "justifycenter";
    	    $toolbar1[] = "justifyright";
    	    $toolbar1[] = "justifyfull";
    	}
    	if ($p_user->hasPermission('EditorIndent')) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "outdent";
    	    $toolbar1[] = "indent";
    	    $toolbar1[] = "blockquote";
    	}
    	if ($p_user->hasPermission('EditorCopyCutPaste')) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "copy";
    	    $toolbar1[] = "cut";
    	    $toolbar1[] = "paste";
    	    $toolbar1[] = "pasteword";
    	}
    	if ($p_user->hasPermission('EditorUndoRedo')) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "undo";
    	    $toolbar1[] = "redo";
    	}
    	if ($p_user->hasPermission('EditorTextDirection')) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "ltr";
    	    $toolbar1[] = "rtl";
    	    $toolbar1[] = "charmap";
    	}
    	if ($p_user->hasPermission('EditorLink')) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "campsiteinternallink";
    	    $toolbar1[] = "link";
    	}
    	if ($p_user->hasPermission('EditorSubhead')) {
    	    #$toolbar1[] = "campsite-subhead";
    	}
    	if ($p_user->hasPermission('EditorImage')) {
    	    $toolbar1[] = "image";
    	}
    	if ($p_user->hasPermission('EditorSourceView')) {
    	    $toolbar1[] = "code";
    	}
    	if ($p_user->hasPermission('EditorEnlarge')) {
    	    $toolbar1[] = "fullscreen";
    	}
    	if ($p_user->hasPermission('EditorHorizontalRule')) {
    	    $toolbar1[] = "hr";
    	}
    	if ($p_user->hasPermission('EditorFontColor')) {
    	    $toolbar1[] = "forecolor";
    	    $toolbar1[] = "backcolor";
    	}
    	if ($p_user->hasPermission('EditorSubscript')) {
    	    $toolbar1[] = "sub";
    	}
    	if ($p_user->hasPermission('EditorSuperscript')) {
    	    $toolbar1[] = "sup";
    	}
    	if ($p_user->hasPermission('EditorFindReplace')) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "search";
    	    $toolbar1[] = "replace";
    	}

    	$toolbar2 = array();
    	// Slice up the first toolbar if it is too long.
    	if (count($toolbar1) > 31) {
    		$toolbar2 = array_splice($toolbar1, 31);
    	}

    	// This is to put the bulleted and numbered list controls
    	// on the most appropriate line of the toolbar.
    	if ($p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber') && count($toolbar1) < 19) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "bullist";
    	    $toolbar1[] = "numlist";
    	} elseif ($p_user->hasPermission('EditorListBullet') && !$p_user->hasPermission('EditorListNumber') && count($toolbar1) < 31) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "bullist";
    	} elseif (!$p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber') && count($toolbar1) < 20) {
    	    $toolbar1[] = "|";
    	    $toolbar1[] = "numlist";
    	} else {
    	    $hasSeparator = false;
    	    if ($p_user->hasPermission('EditorListBullet')) {
    	        $toolbar2[] = "|";
    	        $toolbar2[] = "bullist";
    		$hasSeparator = true;
    	    }
    	    if ($p_user->hasPermission('EditorListNumber')) {
    	        if (!$hasSeparator) {
    		    $toolbar2[] = "|";
    		}
    	        $toolbar2[] = "numlist";
    	    }
    	}

    	if ($p_user->hasPermission('EditorFontFace')) {
    	    $toolbar2[] = "|";
    	    $toolbar2[] = "styleselect";
    	    $toolbar2[] = "formatselect";
    	    $toolbar2[] = "fontselect";
    	}
    	if ($p_user->hasPermission('EditorFontSize')) {
    	    $toolbar2[] = "fontsizeselect";
    	}

    	if ($p_user->hasPermission('EditorTable')) {
    	    $toolbar3[] = "tablecontrols";
    	}

    	$theme_buttons1 = (count($toolbar1) > 0) ? implode(',', $toolbar1) : '';
    	$theme_buttons2 = (count($toolbar2) > 0) ? implode(',', $toolbar2) : '';
    	$theme_buttons3 = (count($toolbar3) > 0) ? implode(',', $toolbar3) : '';

        $localeFile = $Campsite['CAMPSITE_DIR'] . '/js/tinymce/langs/' . $p_editorLanguage . '.js';
        if (!file_exists($localeFile)) {
            $p_editorLanguage = 'en';
        }

    	ob_start();
    ?>

    <!-- TinyMCE -->
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/tinymce/jquery.tinymce.js"></script>
    <script type="text/javascript">
    function CampsiteSubhead(ed) {
        element = ed.dom.getParent(ed.selection.getNode(), 'span');
        if (element && ed.dom.getAttrib(element, 'class') == 'campsite_subhead') {
    	return false;
        } else {
            html = ed.selection.getContent({format : 'text'});
    	ed.selection.setContent('<span class="campsite_subhead">' + html + '</span>');
        }
    }

    $().ready(function() {
        $('textarea.tinymce').tinymce({
            // Location of TinyMCE script
            script_url : '<?php echo $Campsite['WEBSITE_URL']; ?>/js/tinymce/tiny_mce.js',

            // General options
            language : "<?php p($p_editorLanguage); ?>",
            theme : "advanced",
            plugins : "<?php p($plugins_list); ?>",
            forced_root_block : "",
            relative_urls : false,

            // Theme options
            theme_advanced_buttons1 : "<?php p($theme_buttons1); ?>",
            theme_advanced_buttons2 : "<?php p($theme_buttons2); ?>",
            theme_advanced_buttons3 : "<?php p($theme_buttons3); ?>",

            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_resizing : false,
            theme_advanced_statusbar_location: "<?php p($statusbar_location); ?>",

            // Example content CSS (should be your site CSS)
            content_css : "<?php echo $stylesheetFile; ?>",

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "lists/template_list.js",
            external_link_list_url : "lists/link_list.js",
            external_image_list_url : "lists/image_list.js",
            media_external_list_url : "lists/media_list.js",

            // paste options
            paste_use_dialog: false,
            paste_auto_cleanup_on_paste: true,
            paste_convert_headers_to_strong: true,
            paste_remove_spans: true,
            paste_remove_styles: true,

            // not escaping greek characters
            entity_encoding: 'raw',

            setup : function(ed) {
                ed.onInit.add(function(){ed.controlManager.setDisabled('pasteword', true);});
                ed.onNodeChange.add(function(){ed.controlManager.setDisabled('pasteword', true);});

                ed.onKeyUp.add(function(ed, l) {
                    var idx = ed.id.lastIndexOf('_');
                    var buttonId = ed.id.substr(0, idx);
                    buttonEnable('save_' + buttonId);
                });
            }
        });
    });
    </script>
    <!-- /TinyMCE -->
        <?php
        $output = ob_get_clean();
        return $output;
    } // fn editor_load_tinymce


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
                . $g_ado_db->escape($comparisonOperation['right']) . "' ";
                $selectClauseObj->addWhere($whereCondition);
            }
        }

        if (count($hasTopics) > 0) {
            if ($matchAllTopics) {
                foreach ($hasTopics as $topicId) {
                    $sqlQuery = self::BuildTopicSelectClause(array($topicId));
                    $whereCondition = "plugin_blog_blog.blog_id IN (\n$sqlQuery        )";
                    $selectClauseObj->addWhere($whereCondition);
                }
            } else {
                $sqlQuery = self::BuildTopicSelectClause($hasTopics);
                $whereCondition = "plugin_blog_blog.blog_id IN (\n$sqlQuery        )";
                $selectClauseObj->addWhere($whereCondition);
            }
        }
        if (count($hasNotTopics) > 0) {
            $sqlQuery = self::BuildTopicSelectClause($hasNotTopics, true);
            $whereCondition = "plugin_blog_blog.blog_id IN (\n$sqlQuery        )";
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
        $conditionOperation = array();

        $leftOperand = strtolower($p_param->getLeftOperand());
        $conditionOperation['left'] = BlogsList::$s_parameters[$leftOperand]['field'];

        switch ($leftOperand) {

        case 'feature':
            $conditionOperation['symbol'] = 'LIKE';
            $conditionOperation['right'] = '%'.$p_param->getRightOperand().'%';
            break;
        case 'matchalltopics':
            $conditionOperation['symbol'] = '=';
            $conditionOperation['right'] = 'true';
            break;
        case 'topic':
            $conditionOperation['right'] = (string)$p_param->getRightOperand();
            break;
        default:
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

    /**
     * Returns a select query for obtaining the blogs that have the given topics
     *
     * @param array $p_TopicIds
     * @param array $p_typeAttributes
     * @param bool $p_negate
     * @return string
     */
    private static function BuildTopicSelectClause(array $p_TopicIds, $p_negate = false)
    {
        $notCondition = $p_negate ? ' NOT' : '';
        $selectClause = '        SELECT fk_blog_id FROM '.BlogTopic::$$s_dbTableName.' WHERE fk_topic_id'
                      . "$notCondition IN (" . implode(', ', $p_TopicIds) . ")\n";

        return $selectClause;
    }
}
?>
