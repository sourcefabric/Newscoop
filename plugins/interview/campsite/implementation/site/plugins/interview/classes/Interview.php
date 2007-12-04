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

    var $m_dbTableName = 'plugin_onlineinterview_interviews';

    var $m_columnNames = array(
        // int - interview id
        'interview_id',
    
        // int - language id
        'fk_language_id',

        // int - moderator user id
        'fk_moderator_user_id',
        
        // int - invitee user id
        'fk_invitee_user_id',
             
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
        
        // timestamp - last_modified
        'last_modified'
        );

    /**
     * This static variable stores an User object 
     * currently looped in Interview::invite().
     * In each loop it have to assigned here, but 
     * retrived in MetaInterview class. Thatswhy it's static.
     *
     * @var object
     */
    static $current_questioneer = null;

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
    public function create($p_fk_language_id, $p_fk_moderator_user_id, $p_fk_invitee_user_id,
                           $p_title, $p_fk_image_id, 
                           $p_description_short, $p_description,
                           $p_interview_begin, $p_interview_end,
                           $p_questions_begin, $p_questions_end, $p_questions_limit,
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
            'fk_invitee_user_id' => $p_fk_invitee_user_id,
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

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Interview Id $1 created.', $this->m_data['IdInterview']);
        Log::Message($logtext, null, 31);
        */
        
        return true;
    } // fn create


    /**
     * Delete interview from database.
     *
     * @return boolean
     */
    public function delete()
    {       
        // Delete from answers table
        #InterviewAnswer::OnInterviewDelete($this->m_data['interview_id']);
        
        // Delete from questions table
        #InterviewQuestion::OnInterviewDelete($this->m_data['interview_id']);
        
        // finally delete from main table
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
        
        $query = Interview::GetQuery($p_fk_language_id, $p_order_by);
        
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
        
        $query   = Interview::getQuery(); 
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
              
        $mask = Interview::getFormMask();

        if (is_array($p_add_hidden_vars)) {
            foreach ($p_add_hidden_vars as $k => $v) {       
                $mask[] = array(
                    'element'   => $k,
                    'type'      => 'hidden',
                    'constant'  => $v
                );   
            } 
        }
        
        $form =& new html_QuickForm('blog', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form(&$form, &$mask); 
        
        if ($p_html) {
            return $form->toHTML();    
        } else {
            require_once 'HTML/QuickForm/Renderer/Array.php';
            
            $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);
            
            return $renderer->toArray();
        } 
    }
    
    private function getFormMask()
    {
        $data = $this->m_data;
        $exists =  $this->exists();
        
        $mask = array(
            'action'    => array(
                'element'   => 'action',
                'type'      => 'hidden',
                'constant'  => $exists ? 'interview_edit' : 'interview_create'
            ),
            'interview_id'  => $exists ? array(
                    'element'   => 'interview_id',
                    'type'      => 'hidden',
                    'constant'  => $data['interview_id']
            ) : null,
            'fk_language_id' => array(
                'element'   => 'interview[fk_language_id]',
                'type'      => 'select',
                'label'     => 'fk_language_id',
                'default'   => $data['fk_language_id'],
                'options'   => array(1 => 'en', 5 => 'de'),
                'required'  => true,
            ),
            'fk_moderator_user_id' => array(
                'element'   => 'interview[fk_moderator_user_id]',
                'type'      => 'select',
                'label'     => 'fk_moderator_user_id',
                'default'   => $data['fk_moderator_user_id'],
                'options'   => Interview::getUserByRole('ManagePub'),
                'required'  => true,
            ),
            'fk_invitee_user_id' => array(
                'element'   => 'interview[fk_invitee_user_id]',
                'type'      => 'select',
                'label'     => 'fk_invitee_user_id',
                'default'   => $data['fk_invitee_user_id'],
                'options'   => Interview::getUserByRole('ManagePub'),
                'required'  => true,
            ),
            'title' => array(
                'element'   => 'interview[title]',
                'type'      => 'text',
                'label'     => 'title',
                'default'   => $data['title'],
                'required'  => true,
            ),
            'image' => array(
                'element'   => 'interview[image]',
                'type'      => 'file',
                'label'     => 'image'
            ),
            'image_delete' => array(
                'element'   => 'interview[image_delete]',
                'type'      => 'checkbox',
                'label'     => 'image_delete'
            ),             
            'description_short' => array(
                'element'   => 'interview[description_short]',
                'type'      => 'text',
                'label'     => 'description_short',
                'default'   => $data['description_short'],
                'required'  => true,
            ),
            'description' => array(
                'element'   => 'interview[description]',
                'type'      => 'textarea',
                'label'     => 'description',
                'default'   => $data['description'],
                'required'  => true,
                'attributes'=> array('cols' => 40, 'rows' => 5) 
            ),
            'interview_begin' => array(
                'element'   => 'interview[interview_begin]',
                'type'      => 'date',
                'label'     => 'interview_begin',
                'constant'  => $data['interview_begin'],
                'options'   => array('format' => 'Y-m-d H:i:s'),
                'required'  => true            
            ),
            'interview_end' => array(
                'element'   => 'interview[interview_end]',
                'type'      => 'date',
                'label'     => 'interview_end',
                'constant'  => $data['interview_end'],
                'options'   => array('format' => 'Y-m-d H:i:s'),
                'required'  => true            
            ),
             'questions_begin' => array(
                'element'   => 'interview[questions_begin]',
                'type'      => 'date',
                'label'     => 'questions_begin',
                'constant'  => $data['questions_begin'],
                'options'   => array('format' => 'Y-m-d H:i:s'),
                'required'  => true            
            ),
            'questions_end' => array(
                'element'   => 'interview[questions_end]',
                'type'      => 'date',
                'label'     => 'questions_end',
                'constant'  => $data['questions_end'],
                'options'   => array('format' => 'Y-m-d H:i:s'),
                'required'  => true            
            ),
            'questions_limit' => array(
                'element'   => 'interview[questions_limit]',
                'type'      => 'text',
                'label'     => 'questions_limit',
                'default'   => $data['questions_limit']
            ),
            'status' => array(
                'element'   => 'interview[status]',
                'type'      => 'select',
                'options'   => array('draft' => 'Draft', 'published' => 'Publised'),
                'label'     => 'status',
                'default'   => $data['status']
            ),
            'reset'     => array(
                'element'   => 'reset',
                'type'      => 'reset',
                'label'     => 'Zurücksetzen',
                'groupit'   => true
            ),
            'save'     => array(
                'element'   => 'save',
                'type'      => 'button',
                'label'     => 'Save',
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
                'group'     => array('save', 'reset')
            )       
        );
        
        return $mask;   
    }
    
    public function store($p_user_id = null)
    {
        require_once 'HTML/QuickForm.php';
              
        $mask = Interview::getFormMask($p_owner, $p_admin);        
        $form = new html_QuickForm('blog', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form(&$form, &$mask);   
           
        if ($form->validate()) {
            $submit = $form->getSubmitValues();
            $data = $submit['interview'];
             
            $image_id = $this->getProperty('fk_image_id');
            
            if ($data['image_delete'] && $image_id) {
                $Image = new Image($this->getProperty('fk_image_id'));
                $Image->delete();
                $image_id = null;    
            } else {
                $file = $form->getElementValue('interview[image]');
                if ($file['name'] != '') {
                    $attributes = array(
                        'Description' => 'Interview Image (in use, do not delete!)',
                    );
                    $Image = Image::OnImageUpload($file, $attributes, $p_user_id, $image_id);
                    if (is_a($Image, 'Image')) {
                        $image_id = $Image->getProperty('Id');   
                    } else {
                        return false;    
                    }
                }
            }
            
            $interview_begin = "{$data['interview_begin']['Y']}-{$data['interview_begin']['m']}-{$data['interview_begin']['d']} ".
                               "{$data['interview_begin']['H']}:{$data['interview_begin']['i']}:{$data['interview_begin']['s']}";
                               
            $interview_end   = "{$data['interview_end']['Y']}-{$data['interview_end']['m']}-{$data['interview_end']['d']} ".
                               "{$data['interview_end']['H']}:{$data['interview_end']['i']}:{$data['interview_end']['s']}";
                               
            $questions_begin = "{$data['questions_begin']['Y']}-{$data['questions_begin']['m']}-{$data['questions_begin']['d']} ".
                               "{$data['questions_begin']['H']}:{$data['questions_begin']['i']}:{$data['questions_begin']['s']}";
                               
            $questions_end   = "{$data['questions_end']['Y']}-{$data['questions_end']['m']}-{$data['questions_end']['d']} ".
                               "{$data['questions_end']['H']}:{$data['questions_end']['i']}:{$data['questions_end']['s']}";
            
            if ($this->exists()) {
                // edit existing interview    
                $this->setProperty('fk_language_id', $data['fk_language_id']);
                $this->setProperty('fk_moderator_user_id', $data['fk_moderator_user_id']);
                $this->setProperty('fk_invitee_user_id', $data['fk_invitee_user_id']);
                $this->setProperty('title', $data['title']);
                $this->setProperty('fk_image_id', $image_id);
                $this->setProperty('description_short', $data['description_short']);
                $this->setProperty('description', $data['description']);
                $this->setProperty('interview_begin', $interview_begin);
                $this->setProperty('interview_end', $interview_end);
                $this->setProperty('questions_begin', $questions_begin);
                $this->setProperty('questions_end', $questions_end);
                $this->setProperty('questions_limit', $questions_limit);
                #$this->setProperty('status', $data['status']);
                
                return true;
            } else {
                // create new interview
                return $this->create($data['fk_language_id'], $data['fk_moderator_user_id'], $data['fk_invitee_user_id'],
                              $data['title'], $image_id, $data['description_short'], $data['description'],
                              $interview_begin, $interview_end, $questions_begin, $questions_end, $questions_limit);   
                
            }            
        }
        return false;
    }
    
    public function addQuestionner()
    {
            
        
    }
    
    public function addGuest()
    {
        
    }
    
    public function getQuestioneersWantInvitation()
    {
        $Questioneers = array();
        
        foreach (User::GetUsers() as $User) {
            if ($User->hasPermission('NOTIFY_NEW_INTERVIEW')) {
                $Questioneer[] = $User; 
            }
        }
        return $Questioneer;  
    }
    
    public function send_invitations()
    {
        $camp_template = CampTemplate::singleton();
       
        foreach (self::getQuestioneersWantInvitation() as $Questioneer) {
            $this->current_questioneer = $Questioneer;
            $content = $camp_template->fetch('interview-invitation.tpl');
            $subject = $camp_template->get_template_vars('subject');
            $sender = $camp_template->get_template_vars('sender');
            
            mail($Questioneer->getEmail(), $subject, $content, "From: $sender");
        }                    
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
            $order = Interview::ProcessListOrder($p_order);
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
                case 'bynumber':
                    $dbField = 'interview_nr';
                    break;
                case 'byname':
                    $dbField = 'title';
                    break;
                case 'bybegin':
                    $dbField = 'date_begin';
                    break;
                case 'byend':
                    $dbField = 'date_end';
                    break;
                case 'byvotes':
                    $dbField = 'nr_of_votes';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }
       
    public function getUserByRole($p_role)
    {
        $users = array();
        
        foreach (User::getUsers() as $User) {
            if ($User->hasPermission($p_role)) {
                $users[$User->m_data['Id']] = $User->m_data['Name'];   
            }       
        }
        return $users;
               
    }

} // class Interview

?>
