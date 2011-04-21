<?php
/**
 * @package Campsite
 */
class Interview extends DatabaseObject {
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('interview_id');

    var $m_keyIsAutoIncrement = true;

    var $m_dbTableName = 'plugin_interview_interviews';

    var $m_columnNames = array(
        // int - interview id
        'interview_id',

        // int - language id
        'fk_language_id',

        // int - moderator user id
        'fk_moderator_user_id',

        // int - guest user id
        'fk_guest_user_id',

        // string - title in given language
        'title',

        // boolean - the id of campsite image to access logo and thumbnail
        'fk_image_id',

        // string - short description
        'description_short',

        // string - full description
        'description',

        // datetime - when does interview start
        'interview_begin',

        // datetime - when does interview end
        'interview_end',

        // datetime - questions start
        'questions_begin',

        // datetime - questions end
        'questions_end',

        // int - question max quantity
        'questions_limit',

        // string - draft, published
        'status',

        // string - email sender address
        'invitation_sender',

        // text - email subject
        'invitation_subject',

        // text - template to build message
        'invitation_template_guest',

        // text - template to build message
        'invitation_template_questioneer',

        // datetime
        'guest_invitation_sent',

        // datetime
        'questioneer_invitation_sent',

        // string
        'invitation_password',

        // int - custom list position
        'position',

        // timestamp - last_modified
        'last_modified'
        );


    /**
     * Construct by passing in the primary key to access the interview in
     * the database.
     *
     * @param int $p_interview_id
     *        Not required if creating new interview.
     */
    public function __construct($p_interview_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);

        $this->m_data['interview_id'] = $p_interview_id;

        if ($this->keyValuesExist()) {
            $this->fetch();
        }
    } // constructor


    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    private function __create($p_values = null) { return parent::create($p_values); }


    /**
     * Create an interview in the database. Use the SET functions to
     * change individual values.
     *
     * @param date $p_date_begin
     * @param date $p_date_end
     * @param int $p_nr_of_answers
     * @param bool $p_is_show_after_expiration
     * @return void
     */
    public function create($p_fk_language_id, $p_fk_moderator_user_id, $p_fk_guest_user_id,
                           $p_title, $p_fk_image_id,
                           $p_description_short, $p_description,
                           $p_interview_begin, $p_interview_end,
                           $p_questions_begin, $p_questions_end,
                           $p_questions_limit,
                           $p_status = 'draft')
        {
        global $g_ado_db;

        /*
        if (!strlen($p_title) || !strlen($p_question) || !$p_date_begin || !$p_date_end || !$p_nr_of_answers) {
            return false;
        }
        */

        // Create the record
        $values = array(
            'fk_language_id' => $p_fk_language_id,
            'fk_moderator_user_id' => $p_fk_moderator_user_id,
            'fk_guest_user_id' => $p_fk_guest_user_id,
            'title' => $p_title,
            'fk_image_id' => $p_fk_image_id,
            'description_short' => $p_description_short,
            'description' => $p_description,
            'interview_begin' => $p_interview_begin,
            'interview_end' => $p_interview_end,
            'questions_begin' => $p_questions_begin,
            'questions_end' => $p_questions_end,
            'questions_limit' => $p_questions_limit,
            'status' => $p_status
        );


        $success = parent::create($values);
        if (!$success) {
            return false;
        }

        $query = "  SELECT  MAX(position) + 1 AS next
                    FROM    {$this->m_dbTableName}";
        $max = $g_ado_db->getRow($query);

        // Set position
        $query = "  UPDATE  {$this->m_dbTableName}
                    SET     position = {$max['next']}
                    WHERE   interview_id = {$this->m_data['interview_id']}";
        $res = $g_ado_db->execute($query);

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Interview Id $1 created.', $this->m_data['IdInterview']);
        Log::Message($logtext, null, 31);
        */
        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        return true;
    } // fn create


    /**
     * Change the iterview position in the order sequence
     * relative to its current position.
     *
     * @param string $p_direction -
     *         Can be "up" or "down".  "Up" means towards the beginning of the list,
     *         and "down" means towards the end of the list.
     *
     * @param int $p_spacesToMove -
     *        The number of spaces to move the item.
     *
     * @return boolean
     */
    function positionRelative($p_direction, $p_spacesToMove = 1)
    {
        global $g_ado_db;

        // Get the item that is in the final position where this
        // interview will be moved to.
        $compareOperator = ($p_direction == 'up') ? '<' : '>';
        $order = ($p_direction == 'up') ? 'desc' : 'asc';
        $queryStr = "   SELECT  position
                        FROM    {$this->m_dbTableName}
                        WHERE   position $compareOperator {$this->m_data['position']}
                        ORDER BY position $order
                        LIMIT ".($p_spacesToMove-1).", 1";
        $destRow = $g_ado_db->GetRow($queryStr);

        // Shift all items one space between the source and destination item.
        $operator = ($p_direction == 'up') ? '+' : '-';
        $minItemOrder = min($destRow['position'], $this->m_data['position']);
        $maxItemOrder = max($destRow['position'], $this->m_data['position']);
        $queryStr2 = "  UPDATE  {$this->m_dbTableName}
                        SET     position = position $operator 1
                        WHERE   position >= $minItemOrder
                                AND position <= $maxItemOrder";
        $g_ado_db->Execute($queryStr2);

        // Change position of this item to the destination position.
        $queryStr3 = "  UPDATE  {$this->m_dbTableName}
                        SET     position = {$destRow['position']}
                        WHERE   interview_id = {$this->m_data['interview_id']}";
        $g_ado_db->Execute($queryStr3);

        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        $this->fetch();
        return true;
    } // fn positionRelative


    /**
     * Move the interview to the given position (i.e. reorder the interview).
     * @param int $p_moveToPosition
     * @return boolean
     */
    function positionAbsolute($p_moveToPosition = 1)
    {
        global $g_ado_db;
        // Get the item that is in the location we are moving
        // this one to.
        $queryStr = "   SELECT  position, interview_id
                        FROM    {$this->m_dbTableName}
                        ORDER BY position ASC
                        LIMIT   ".($p_moveToPosition - 1).', 1';
        $destRow = $g_ado_db->GetRow($queryStr);
        if (!$destRow) {
            return false;
        }
        if ($destRow['position'] == $this->m_data['position']) {
            // Move the destination down one.
            $destItem = new Interview($destRow['interview_id']);
            $destItem->positionRelative("down", 1);
            return true;
        }
        if ($destRow['position'] > $this->m_data['position']) {
            $operator = '-';
        } else {
            $operator = '+';
        }
        // Reorder all the other items
        $minItemOrder = min($destRow['position'], $this->m_data['position']);
        $maxItemOrder = max($destRow['position'], $this->m_data['position']);
        $queryStr = "   UPDATE  {$this->m_dbTableName}
                        SET     position = position $operator 1
                        WHERE   position >= $minItemOrder
                                AND position <= $maxItemOrder";
        $g_ado_db->Execute($queryStr);

        // Reposition this item.
        $queryStr = "   UPDATE  {$this->m_dbTableName}
                        SET     position = {$destRow['position']}
                        WHERE   interview_id = {$this->m_data['interview_id']}";
        $g_ado_db->Execute($queryStr);

        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        $this->fetch();
        return true;
    } // fn positionAbsolute


    /**
     * Delete interview from database.
     *
     * @return boolean
     */
    public function delete()
    {
        global $g_ado_db;

        // Delete from InterviewItems table
        InterviewItem::OnInterviewDelete($this->m_data['interview_id']);

        // reduce order of all following items minus 1
        $currItemOrder = $this->getProperty('position');
        $queryStr = "   UPDATE  {$this->m_dbTableName}
                        SET     position = position - 1
                        WHERE   position > $currItemOrder";
        $g_ado_db->Execute($queryStr);

        // finally delete from main table
        $deleted = parent::delete();

        /*
        if ($deleted) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('Interview #$1: "$2" ($3) deleted.',
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
     * Overload setProperty() to clear cache on updates.
     *
     * @param string $p_name
     * @param string $p_value
     */
    public function setProperty($p_name, $p_value)
    {
        $return = parent::setProperty($p_name, $p_value);
        $CampCache = CampCache::singleton();
        $CampCache->clear('user');
        return $return;
    }

    /**
     * Construct query to recive interviews from database
     *
     * @param int $p_fk_language
     * @return string
     */
    static private function GetQuery($p_fk_language = null, array $p_order_by = null)
    {
        $Interview = new Interview();

        if (!empty($p_fk_language)) {
            $query = "SELECT    interview_id
                      FROM      {$Interview->m_dbTableName}
                      WHERE     fk_language_id = $p_fk_language ";
        } else {
            $query = "SELECT    interview_id
                      FROM      {$Interview->m_dbTableName} ";
        }

        if (!count($p_order_by)) {
            $p_order_by = array('interview_id' => 'DESC');
        }

        foreach ($p_order_by as $col => $dir) {
            if (in_array($col, $Interview->m_columnNames)) {
                $order .= "$col $dir , ";
            }
        }

        $query .= "ORDER BY ".substr($order, 0, -2);

        return $query;
    }

    /**
     * Get an array of interview objects
     * You may specify the language
     *
     * @param unknown_type $p_fk_language_id
     * @param unknown_type $p_offset
     * @param unknown_type $p_limit
     * @return array
     */
    static public function GetInterviews($p_fk_language_id = null, $p_offset = null, $p_limit = null, array $p_order_by = null)
    {
        global $g_ado_db;

        if (empty($p_offset)) {
            $p_offset = 0;
        }

        if (empty($p_limit)) {
            $p_limit = 20;
        }

        $query = self::GetQuery($p_fk_language_id, $p_order_by);

        $res = $g_ado_db->SelectLimit($query, $p_limit, $p_offset);
		$interviews = array();

		while ($row = $res->FetchRow()) {
		    $tmp_interview = new Interview($row['interview_id']);
            $interviews[] = $tmp_interview;
		}

		return $interviews;
    }


    /**
     * Get the count for available interviews
     *
     * @return int
     */
    public static function countInterviews()
    {
        global $g_ado_db;;

        $query   = self::getQuery();
        $res     = $g_ado_db->Execute($query);

        return $res->RecordCount();
    }


    /**
     * Get answer object for this interview by given number
     *
     * @param unknown_type $p_nr_answer
     * @return object
     */
    public function getInterviewItems()
    {
        $InterviewItems = InterviewItem::GetInterviewItems($this->m_data['interview_id']);

        return $InterviewItems;
    }

    /**
     * Get the interview id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getProperty('interview_id');
    }

    /**
     * Get the name/title
     *
     * @return string
     */
    public function getName()
    {
        return $this->getProperty('title');
    }

    /**
     * Get the name/title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getProperty('title');
    }

    /**
     * Get the language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->getProperty('fk_language_id');
    }

    /**
     * Get the english language name
     *
     * @return string
     */
    public function getLanguageName()
    {
        $language = new Language($this->m_data['fk_language_id']);

        return $language->getName();
    }

   public function getForm($p_target='index.php', $p_add_hidden_vars=array(), $p_html=false)
    {
        require_once 'HTML/QuickForm.php';

        $mask = self::getFormMask();

        if (is_array($p_add_hidden_vars)) {
            foreach ($p_add_hidden_vars as $k => $v) {
                $mask[] = array(
                    'element'   => $k,
                    'type'      => 'hidden',
                    'constant'  => $v
                );
            }
        }

        $form = new html_QuickForm('interview', 'post', $p_target, null, null, true);
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

    private function getFormMask()
    {
        global $Campsite;

        $data = $this->m_data;
        $exists = $this->exists();

        if (!empty($data['fk_image_id'])) {
            $Image = new Image($data['fk_image_id']);
            $image_description = $Image->getDescription();
        }

        $mask = array(
            array(
                'element'   => 'action',
                'type'      => 'hidden',
                'constant'  => 'interview_edit'
            ),
            SecurityToken::SECURITY_TOKEN => array(
            	'element'   => SecurityToken::SECURITY_TOKEN,
            	'type'      => 'hidden',
            	'constant'  => SecurityToken::GetToken()
            ),
            $exists ? array(
                    'element'   => 'f_interview_id',
                    'type'      => 'hidden',
                    'constant'  => $data['interview_id']
            ) : null,
            array(
                'element'   => 'f_language_id',
                'type'      => 'select',
                'label'     => getGS('Language'),
                'default'   => $data['fk_language_id'],
                'options'   => self::GetCampLanguagesList(),
                'required'  => true,
            ),
            array(
                'element'   => 'f_moderator_user_id',
                'type'      => 'select',
                'label'     => getGS('Moderator'),
                'default'   => $data['fk_moderator_user_id'],
                'options'   => self::getUsersHavePermission('plugin_interview_moderator'),
                'required'  => true,
                'attributes' => array('onChange' => 'activate_fields("moderator")')
            ),
            /*
            array(
                'element'   => 'f_moderator_new_user_login',
                'type'      => 'text',
                'label'     => getGS('Create new Moderator'),
                'default'   => $data['f_moderator_new_user_login'],
                'attributes' => $_REQUEST['f_moderator_user_id'] == '__new__' ? null : array('disabled'),
            ),
            array(
                'element'   => 'f_moderator_new_user_email',
                'type'      => 'text',
                'label'     => getGS('New Moderator Email'),
                'default'   => $data['f_moderator_new_user_email'],
                'attributes' => $_REQUEST['f_moderator_user_id'] == '__new__' ? null : array('disabled'),
            ),
            */
            array(
                'element'   => 'f_guest_user_id',
                'type'      => 'select',
                'label'     => getGS('Guest'),
                'default'   => $data['fk_guest_user_id'],
                'options'   =>  array('' => getGS('Please select:'))
                                + self::getUsersHavePermission('plugin_interview_guest')
                                + array('__new__' => getGS('Create new one...')),
                'required'  => true,
                'attributes' => array('onChange' => 'activate_fields("guest")')
            ),
            array(
                'element'   => 'f_guest_new_user_login',
                'type'      => 'text',
                'label'     => getGS('Guest Login'),
                'default'   => $data['f_guest_new_user_login'],
                'attributes' => $_REQUEST['f_guest_user_id'] == '__new__' ? null : array('disabled'),
            ),
            array(
                'element'   => 'f_guest_new_user_email',
                'type'      => 'text',
                'label'     => getGS('Guest Email'),
                'default'   => $data['f_guest_new_user_email'],
                'attributes' => $_REQUEST['f_guest_user_id'] == '__new__' ? null : array('disabled'),
            ),
            array(
                'element'   => 'f_title',
                'type'      => 'text',
                'label'     => getGS('Title'),
                'default'   => $data['title'],
                'required'  => true,
            ),
            array(
                'element'   => 'f_image',
                'type'      => 'file',
                'label'     => getGS('Image')
            ),
            array(
                'element'   => 'f_image_description',
                'type'      => 'text',
                'label'     => getGS('Image Description'),
                'default'   => $image_description
            ),
            array(
                'element'   => 'f_image_delete',
                'type'      => 'checkbox',
                'label'     => getGS('Delete Image')
            ),
            array(
                'element'   => 'f_description_short',
                'type'      => 'text',
                'label'     => getGS('Short Description'),
                'default'   => $data['description_short'],
                'required'  => true,
            ),
            array(
                'element'   => 'f_description',
                'type'      => 'textarea',
                'label'     => getGS('Description'),
                'default'   => $data['description'],
                'required'  => true,
                'attributes'=> array('cols' => 40, 'rows' => 5)
            ),
            array(
                'element'   => 'f_interview_begin',
                'type'      => 'text',
                'label'     => getGS('Interview Begin'),
                'default'   => substr($data['interview_begin'], 0, 16),
                'required'  => true,
                'attributes' => array(
                    'id' => 'f_interview_begin',
                    'size' => 17,
                    'maxlength' => 20,
                    'class' => 'datetime',
                ),
            ),
            array(
                'element'   => 'f_interview_end',
                'type'      => 'text',
                'label'     => getGS('Interview End'),
                'default'   => substr($data['interview_end'], 0, 16),
                'required'  => true,
                'attributes' => array(
                    'id' => 'f_interview_end',
                    'size' => 17,
                    'maxlength' => 20,
                    'class' => 'datetime',
                ),
            ),
            array(
                'element'   => 'f_questions_begin',
                'type'      => 'text',
                'label'     => getGS('Questions Begin'),
                'default'   => substr($data['questions_begin'], 0, 16),
                'required'  => true,
                'attributes' => array(
                    'id' => 'f_questions_begin',
                    'size' => 17,
                    'maxlength' => 20,
                    'class' => 'datetime',
                ),
            ),
            array(
                'element'   => 'f_questions_end',
                'type'      => 'text',
                'label'     => getGS('Questions End'),
                'default'   => substr($data['questions_end'], 0, 16),
                'required'  => true,
                'attributes' => array(
                    'id' => 'f_questions_end',
                    'size' => 17,
                    'maxlength' => 20,
                    'class' => 'datetime',
                ),
            ),
            array(
                'element'   => 'f_questions_limit',
                'type'      => 'text',
                'label'     => getGS('Questions Limit'),
                'default'   => $data['questions_limit']
            ),
            array(
                'element'   => 'f_status',
                'type'      => 'select',
                'options'   => array(
                    'draft'     => getGS('draft'),
                    'pending'   => getGS('pending'),
                    'published' => getGS('published'),
                    'rejected'   => getGS('rejected')
                ),
                'label'     => getGS('Status'),
                'default'   => $data['status']
            ),
            array(
                'element'   => 'f_reset',
                'type'      => 'reset',
                'label'     => getGS('Reset'),
                'groupit'   => true
            ),
            array(
                'element'   => 'f_submit',
                'type'      => 'submit',
                'label'     => getGS('Save'),
                'groupit'   => true
            ),
            array(
                'element'   => 'f_cancel',
                'type'      => 'button',
                'label'     => getGS('Cancel'),
                'attributes' => array('onClick' => 'window.close()'),
                'groupit'   => true
            ),
            array(
                'group'     => array('f_reset', 'f_cancel', 'f_submit')
            )
        );

        return $mask;
    }

    public function store($p_user_id = null)
    {
        require_once 'HTML/QuickForm.php';

        $mask = self::getFormMask($p_owner, $p_admin);
        $form = new html_QuickForm('interview', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form($form, $mask);

        if ($form->validate() && SecurityToken::isValid()) {
            $data = $form->getSubmitValues();

            $image_id = $this->getProperty('fk_image_id');

            if ($data['f_image_delete'] && $image_id) {
                $Image = new Image($this->getProperty('fk_image_id'));
                $Image->delete();
                $image_id = null;
            } else {
                $file = $form->getElementValue('f_image');
                if (strlen($file['name'])) {
                    $attributes = array(
                        'Description' => strlen($data['f_image_description']) ? $data['f_image_description'] : $file['name'],
                    );
                    $Image = Image::OnImageUpload($file, $attributes, $p_user_id, !empty($image_id) ? $image_id : null);
                    if (is_a($Image, 'Image')) {
                        $image_id = $Image->getProperty('Id');
                    } else {
                        return false;
                    }
                }
            }

            // may have to create new user account for guest
            foreach (array('guest') as $type) {
                if ($data['f_'.$type.'_user_id'] == '__new__') {
                    global $ADMIN_DIR;
                    require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/users_common.php");

                    $passwd = substr(sha1(rand()), 0, 10);

                    $fieldValues = array(
                        'UName' => $data['f_'.$type.'_new_user_login'],
                        'Name'  => $data['f_'.$type.'_new_user_login'].' (interview guest)',
                        'EMail' => $data['f_'.$type.'_new_user_email'],
                        'passwd' => $passwd,
                        'Reader' => 'N'
                    );
                    // create user
                    $editUser = new User();
                    $phorumUser = new Phorum_user();

                    if ($phorumUser->UserNameExists($fieldValues['UName']) || User::UserNameExists($fieldValues['UName'])) {
                        return false;
                    }

                    if (!$editUser->create($fieldValues)) {
                        return false;
                    }

                	$editUser->setUserType('Staff');
                	$editUser->setPermission('plugin_interview_'.$type, true);

                	$phorumUser->create($fieldValues['UName'], $passwd, $fieldValues['EMail'], $editUser->getUserId());

                    $userid[$type] = $editUser->getUserId();
                } else {
                     $userid[$type] = $data['f_'.$type.'_user_id'];
                }
            }

            if ($this->exists()) {
                // edit existing interview
                $this->setProperty('fk_language_id', $data['f_language_id']);
                $this->setProperty('title', $data['f_title']);
                $this->setProperty('fk_image_id', $image_id);
                $this->setProperty('description_short', $data['f_description_short']);
                $this->setProperty('description', $data['f_description']);
                $this->setProperty('interview_begin', $data['f_interview_begin']);
                $this->setProperty('interview_end', $data['f_interview_end']);
                $this->setProperty('questions_begin', $data['f_questions_begin']);
                $this->setProperty('questions_end', $data['f_questions_end']);
                $this->setProperty('questions_limit', $data['f_questions_limit']);
                $this->setProperty('status', $data['f_status']);
                $this->setProperty('fk_moderator_user_id', $data['f_moderator_user_id']);
                $this->setProperty('fk_guest_user_id', $userid['guest']);

                if (strlen($passwd)) {
                    $this->setProperty('invitation_password', $passwd);
                }

                return true;

            } else {
                // create new interview
                $created = $this->create(
                    $data['f_language_id'],
                    $data['f_moderator_user_id'],
                    $userid['guest'],
                    $data['f_title'],
                    $image_id,
                    $data['f_description_short'],
                    $data['f_description'],
                    $data['f_interview_begin'],
                    $data['f_interview_end'],
                    $data['f_questions_begin'],
                    $data['f_questions_end'],
                    $data['f_questions_limit'],
                    $data['f_status']
                );

                if (strlen($passwd)) {
                    $this->setProperty('invitation_password', $passwd);
                }

                return $created;
            }
        }
        return false;
    }

    public function getInvitationForm($p_target='index.php', $p_add_hidden_vars=array(), $p_html=false, $p_userid)
    {
        require_once 'HTML/QuickForm.php';

        $mask = self::getInvitationFormMask($_REQUEST['f_preview'], $p_userid);

        if (is_array($p_add_hidden_vars)) {
            foreach ($p_add_hidden_vars as $k => $v) {
                $mask[] = array(
                    'element'   => $k,
                    'type'      => 'hidden',
                    'constant'  => $v
                );
            }
        }

        $form = new html_QuickForm('invitation', 'post', $p_target, null, null, true);
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


    function smarty_get_guest_template($p_void, &$tpl_source, &$smarty_obj)
    {
        $tpl_source = $this->getProperty('invitation_template_guest');

        return true;
    }

    function smarty_get_questioneer_template($p_void, &$tpl_source, &$smarty_obj)
    {
        $tpl_source = $this->getProperty('invitation_template_questioneer');

        return true;
    }

    function smarty_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
    {
        $tpl_timestamp = time();
        return true;
    }

    function smarty_get_secure($tpl_name, &$smarty_obj)
    {
        return true;
    }

    function smarty_get_trusted($tpl_name, &$smarty_obj)
    {

    }

    private function smarty_parse_inviation_template(MetaInterview $p_metainterview, MetaUser $p_metauser, $p_type)
    {
        $smarty = CampTemplate::singleton();
        $campsite = new stdClass();
        $campsite->interview = $p_metainterview;
        $campsite->user = $p_metauser;
        $campsite->WEBSITE_URL = $GLOBALS['Campsite']['WEBSITE_URL'];
        $smarty->assign_by_ref('campsite', $campsite);

        $smarty->register_resource("interview",
            array(
                array(&$this, "smarty_get_{$p_type}_template"),
                array(&$this, "smarty_get_timestamp"),
                array(&$this, "smarty_get_secure"),
                array(&$this, "smarty_get_trusted")
            )
        );

        // need to deactivate the error reporting for parsing the template which could have errors
        $old_error_handler = set_error_handler(array('Interview' , 'my_error_handler'));
        $old_error_level = error_reporting();
        $parsed = $smarty->fetch("interview:void");
        set_error_handler($old_error_handler, $old_error_level);

        return $parsed;
    }

    public function my_error_handler()
    {
        // do nothing
    }

    private function getInvitationFormMask($p_preview = false, &$p_userid = null)
    {
        global $Campsite;

        $data = $this->m_data;

        if ($p_preview) {

            $MetaInterview = new MetaInterview($this->getId());
            $MetaUser = new MetaUser($p_userid);
            $guest_text = $this->smarty_parse_inviation_template($MetaInterview, $MetaUser, 'guest');
            $questioneer_text = $this->smarty_parse_inviation_template($MetaInterview, $MetaUser, 'questioneer');
        }

        $mask = array(
            array(
                    'element'   => 'f_interview_id',
                    'type'      => 'hidden',
                    'constant'  => $data['interview_id']
            ),
            SecurityToken::SECURITY_TOKEN => array(
            	'element'   => SecurityToken::SECURITY_TOKEN,
            	'type'      => 'hidden',
            	'constant'  => SecurityToken::GetToken()
            ),
            isset($p_preview) ?
                array(
                    'element'   => 'f_sender',
                    'type'      => 'text',
                    'label'     => getGS('Sender'),
                    'default'   => $data['invitation_sender'],
                    'attributes'=> array('disabled' => true, 'readonly' => true),
                ) : null,
            isset($p_preview) ?
                array(
                    'element'   => 'f_subject',
                    'type'      => 'text',
                    'label'     => getGS('Subject'),
                    'default'   => $data['invitation_subject'],
                    'attributes'=> array('disabled' => true, 'readonly' => true),
                ) : null,
            isset($p_preview) ?
                array(
                    'element'   => 'f_invitation_preview_guest',
                    'type'      => 'static',
                    'label'     => getGS('Guest preview text'),
                    'default'   => $guest_text,
                    'attributes'=> array('cols' => 70, 'rows' => 12, 'disabled' => true, 'readonly' => true),
                ) : null,
            isset($p_preview) ?
                array(
                    'element'   => 'f_invitation_preview_questioneer',
                    'type'      => 'static',
                    'label'     => getGS('Questioneer preview text'),
                    'default'   => $questioneer_text,
                    'attributes'=> array('cols' => 70, 'rows' => 12, 'disabled' => true, 'readonly' => true),
                ) : null,
            isset($p_preview) ? null :
                array(
                    'element'   => 'f_invitation_sender',
                    'type'      => 'text',
                    'label'     => getGS('Sender'),
                    'default'   => $data['invitation_sender'],
                    'required'  => true
                ),
            isset($p_preview) ? null :
                array(
                    'element'   => 'f_invitation_subject',
                    'type'      => 'text',
                    'label'     => getGS('Subject'),
                    'default'   => $data['invitation_subject'],
                    'required'  => true
                ),
            isset($p_preview) ? null :
                array(
                'element'   => 'tiny_mce',
                'text'      => '<script language="javascript" type="text/javascript" src="' . $Campsite['WEBSITE_URL'] . '/js/tinymce/tiny_mce.js"></script>'.
                               '<script language="javascript" type="text/javascript">'.
                               '     tinyMCE.init({'.
                               '     	mode : "exact",'.
                               '        elements : "f_invitation_template_guest, f_invitation_template_questioneer",'.
                               '        entity_encoding : "raw",'.
                               '        relative_urls : false,'.
                               '        convert_urls : false,'.
                               '        theme : "advanced",'.
                               '        plugins : "emotions, paste", '.
                               '        paste_auto_cleanup_on_paste : true, '.
                               '        theme_advanced_buttons1 : "bold, italic, underline, undo, redo, link", '.
                               '        theme_advanced_buttons2 : "", '.
                               '        theme_advanced_buttons3 : "" '.
                               '     });'.
                               '</script>',
                'type'      => 'static'
            ),
            isset($p_preview) ? null :
                array(
                    'element'   => 'f_invitation_template_guest',
                    'type'      => 'textarea',
                    'label'     => getGS('Invitation Template for Guest').'<br><a href="">Help</a>',
                    'default'   => $data['invitation_template_guest'],
                    'required'  => true,
                    'attributes'=> array('cols' => 70, 'rows' => 12, 'id' => 'f_invitation_template_guest'),
                ),
           isset($p_preview) ? null : array(
                    'element'   => 'f_invitation_template_questioneer',
                    'type'      => 'textarea',
                    'label'     => getGS('Invitation Template for Questioneer').'<br><a href="">Help</a>',
                    'default'   => $data['invitation_template_questioneer'],
                    'required'  => true,
                    'attributes'=> array('cols' => 70, 'rows' => 12, 'id' => 'f_invitation_template_questioneer'),
                ),
            $this->getProperty('guest_invitation_sent') !== null ?
                array(
                    'element'   => 'f_warning',
                    'type'      => 'static',
                    'text'  => '<font color="red"><b>'.getGS('Invitation to interview guest has already been sent at $1', $this->getProperty('guest_invitation_sent')).'</b></font>'
                ) : null,
            $this->getProperty('questioneer_invitation_sent') !== null ?
                array(
                    'element'   => 'f_warning',
                    'type'      => 'static',
                    'text'  => '<font color="red"><b>'.getGS('Invitations to questioneers has already been sent at $1', $this->getProperty('questioneer_invitation_sent')).'</b></font>'
                ) : null,
            array(
                'element'   => 'f_reset',
                'type'      => 'reset',
                'label'     => getGS('Reset'),
                'groupit'   => true
            ),
            array(
                'element'   => 'f_edit',
                'type'      => 'button',
                'label'     => getGS('Edit'),
                'attributes' => array('onClick' => 'location.href="?f_interview_id='.$this->getId().'"'),
                'groupit'   => true
            ),
            array(
                'element'   => 'f_preview',
                'type'      => 'submit',
                'label'     => getGS('Preview'),
                'groupit'   => true
            ),
            array(
                'element'   => 'f_invite_now',
                'type'      => 'submit',
                'label'     => getGS('Invite Now'),
                'groupit'   => true
            ),
            array(
                'element'   => 'f_cancel',
                'type'      => 'button',
                'label'     => getGS('Cancel'),
                'attributes' => array('onClick' => 'window.close()'),
                'groupit'   => true
            ),
            isset($p_preview) ?
                array(
                    'group'     => array('f_cancel', 'f_edit', 'f_invite_now')
                )
                :
                array(
                    'group'     => array('f_cancel', 'f_reset', 'f_preview')
                )
        );

        return $mask;
    }

    public function storeInvitation()
    {
        require_once 'HTML/QuickForm.php';

        $mask = self::getInvitationFormMask();
        $form = new html_QuickForm('invitation', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form($form, $mask);

        if ($form->validate() && SecurityToken::isValid()) {
            $data = $form->getSubmitValues();

            $data['f_invitation_template_guest'] = preg_replace_callback('/(%7B%7B.*%7D%7D)/u', create_function('$input', 'return urldecode($input[0]);'), $data['f_invitation_template_guest']);
            $data['f_invitation_template_guest'] = preg_replace_callback('/{{[^}]*}}/', create_function('$input', 'return html_entity_decode($input[0]);'), $data['f_invitation_template_guest']);
            $data['f_invitation_template_questioneer'] = preg_replace_callback('/(%7B%7B.*%7D%7D)/u', create_function('$input', 'return urldecode($input[0]);'), $data['f_invitation_template_questioneer']);
            $data['f_invitation_template_questioneer'] = preg_replace_callback('/{{[^}]*}}/', create_function('$input', 'return html_entity_decode($input[0]);'), $data['f_invitation_template_questioneer']);

            $this->setProperty('invitation_sender', $data['f_invitation_sender']);
            $this->setProperty('invitation_subject', $data['f_invitation_subject']);
            $this->setProperty('invitation_template_guest', $data['f_invitation_template_guest']);
            $this->setProperty('invitation_template_questioneer', $data['f_invitation_template_questioneer']);

            return true;
        }
    }

    public function getQuestioneersWantInvitation()
    {
        $Questioneers = array();

        foreach (User::GetUsers() as $User) {
            if ($User->hasPermission('plugin_interview_notify')) {
                $Questioneer[] = $User;
            }
        }
        return $Questioneer;
    }

    public function sendGuestInvitation()
    {
        $MetaInterview = new MetaInterview($this->getId());

        $headers = array(
            'From' => $this->getProperty('invitation_sender'),
            'Subject' =>  $this->getProperty('invitation_subject')
        );

        // invite the guest
        $MetaUser = new MetaUser($this->getProperty('fk_guest_user_id'));

        $parsed = $this->smarty_parse_inviation_template($MetaInterview, $MetaUser, 'guest');
        $parsed = str_replace("\r\n",  "\n", $parsed);
        #$parsed = str_replace("\n",  "\r\n", $parsed);

        CampMail::MailMime($MetaUser->email, null, $parsed, $headers);

        $this->setProperty('guest_invitation_sent', strftime('%Y-%m-%d %H:%M:%S'));
    }

    public function sendQuestioneerInvitation()
    {
        $MetaInterview = new MetaInterview($this->getId());

        $headers = array(
            'From' => $this->getProperty('invitation_sender'),
            'Subject' =>  $this->getProperty('invitation_subject')
        );

        // invite questioneers
        foreach (self::getQuestioneersWantInvitation() as $Questioneer) {
            $MetaUser = new MetaUser($Questioneer->getUserId());

            $parsed = $this->smarty_parse_inviation_template($MetaInterview, $MetaUser, 'questioneer');
            $parsed = str_replace("\r\n",  "\n", $parsed);
            #$parsed = str_replace("\n",  "\r\n", $parsed);

            CampMail::MailMime($Questioneer->getEmail(), null, $parsed, $headers);
        }
        $this->setProperty('questioneer_invitation_sent', strftime('%Y-%m-%d %H:%M:%S'));
    }

    /////////////////// Special template engine methods below here /////////////////////////////

    /**
     * Gets an issue list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string item
     *    An indentifier which assignment should be used (publication/issue/section/article)
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
    public static function GetList($p_parameters, $p_item = null, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count)
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
        $tmpInterview = new Interview();
		$columnNames = $tmpInterview->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpInterview->getDbTableName();
        $selectClauseObj->setTable($mainTblName);
        unset($tmpInterview);

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
        $interviewRes = $g_ado_db->SelectLimit($sqlQuery, $p_limit, $p_start);

        // builds the array of interview objects
        $interviewsList = array();
        while ($interview = $interviewRes->FetchRow()) {
            $interviewObj = new Interview($interview['interview_id']);
            if ($interviewObj->exists()) {
                $interviewsList[] = $interviewObj;
            }
        }

        return $interviewsList;
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

        $comparisonOperation['left'] = InterviewsList::$s_parameters[strtolower($p_param->getLeftOperand())]['field'];

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
            $dbField = null;
            switch (strtolower($field)) {
                case 'byidentifier':
                    $dbField = 'interview_id';
                    break;
                case 'byname':
                    $dbField = 'title';
                    break;
                case 'bytitle':
                    $dbField = 'title';
                    break;
                case 'byquestions_begin':
                    $dbField = 'questions_begin';
                    break;
                case 'byquestions_end':
                    $dbField = 'questions_end';
                    break;
                case 'byinterview_begin':
                    $dbField = 'interview_begin';
                    break;
                case 'byinterview_end':
                    $dbField = 'interview_end';
                    break;
                case 'bymoderator':
                    $dbField = 'fk_moderator_user_id';
                    break;
                case 'byguest':
                    $dbField = 'fk_guest_user_id';
                    break;
                case 'bystatus':
                    $dbField = 'status';
                    break;
                case 'byorder':
                case 'byposition':
                    $dbField = 'position';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }

    public function getUsersHavePermission($p_permission)
    {
        $users = array();

        foreach (User::getUsers() as $User) {
            if ($User->hasPermission($p_permission)) {
                $users[$User->m_data['Id']] = $User->m_data['Name'];
            }
        }
        return $users;

    }

    public static function GetCampLanguagesList()
    {
        foreach (Language::GetLanguages() as $Language) {
            $languageList[$Language->getLanguageId()] = $Language->getNativeName();
        }
        asort($languageList);
        return $languageList;
    }

} // class Interview

?>
