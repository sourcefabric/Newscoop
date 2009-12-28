<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');


define('ACTION_INTERVIEW_ERR_NO_LANGUAGE', 'ACTION_INTERVIEW_ERR_NO_LANGUAGE');
define('ACTION_INTERVIEW_ERR_NO_TITLE', 'ACTION_INTERVIEW_ERR_NO_TITLE');
define('ACTION_INTERVIEW_ERR_NO_DESCRIPTION', 'ACTION_INTERVIEW_ERR_NO_DESCRIPTION');
define('ACTION_INTERVIEW_ERR_NO_DESCRIPTION_SHORT', 'ACTION_INTERVIEW_ERR_NO_DESCRIPTION_SHORT');
define('ACTION_INTERVIEW_ERR_NO_MODERATOR', 'ACTION_INTERVIEW_ERR_NO_MODERATOR');
define('ACTION_INTERVIEW_ERR_NO_GUEST', 'ACTION_INTERVIEW_ERR_NO_GUEST');
define('ACTION_INTERVIEW_ERR_NO_INTERVIEW_BEGIN', 'ACTION_INTERVIEW_ERR_NO_INTERVIEW_BEGIN');
define('ACTION_INTERVIEW_ERR_NO_INTERVIEW_END', 'ACTION_INTERVIEW_ERR_NO_INTERVIEW_END');
define('ACTION_INTERVIEW_ERR_NO_QUESTIONS_BEGIN', 'ACTION_INTERVIEW_ERR_NO_QUESTIONS_BEGIN');
define('ACTION_INTERVIEW_ERR_NO_QUESTIONS_END', 'ACTION_INTERVIEW_ERR_NO_QUESTIONS_END');
define('ACTION_INTERVIEW_ERR_NO_PERMISSION', 'ACTION_INTERVIEW_ERR_NO_PERMISSION');

class MetaActionInterview extends MetaAction
{
    private $m_interview;


    /**
     * Reads the input parameters and sets up the interview action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_name = 'interview';
        $this->m_defined = true;
        
        if (!strlen($p_input['f_interview_language_id'])) {
            $this->m_error = new PEAR_Error('An interview language was not selected.',
            ACTION_INTERVIEW_ERR_NO_LANGUAGE);
            return;
        }
        $this->m_properties['language_id'] = $p_input['f_interview_language_id'];
        
        if (!strlen($p_input['f_interview_title'])) {
            $this->m_error = new PEAR_Error('An interview title was not set.',
            ACTION_INTERVIEW_ERR_NO_TITLE);
            return;
        }
        $this->m_properties['title'] = $p_input['f_interview_title'];
        
        if (!isset($p_input['f_interview_description'])) {
            $this->m_error = new PEAR_Error('An description was not set.',
            ACTION_INTERVIEW_ERR_NO_DESCRIPTION);
            return;
        }
        $this->m_properties['description'] = $p_input['f_interview_description'];
        
        if (!strlen($p_input['f_interview_description_short'])) {
            $this->m_error = new PEAR_Error('An short description was not set.',
            ACTION_INTERVIEW_ERR_NO_DESCRIPTION_SHORT);
            return;
        }
        $this->m_properties['description_short'] = $p_input['f_interview_description_short'];

        if (!isset($p_input['f_interview_moderator_user_id'])) {
            $this->m_error = new PEAR_Error('An interview moderator was not selected.',
            ACTION_INTERVIEW_ERR_NO_MODERATOR);
            return;
        }
        $this->m_properties['moderator_user_id'] = $p_input['f_interview_moderator_user_id'];
        
        if (!isset($p_input['f_interview_guest_user_id'])) {
            $this->m_error = new PEAR_Error('An interview guest was not selected.',
            ACTION_INTERVIEW_ERR_NO_GUEST);
            return;
        }
        $this->m_properties['guest_user_id'] = $p_input['f_interview_guest_user_id'];
        
        if (strlen($p_input['f_interview_interview_begin']) != 10) {
            $this->m_error = new PEAR_Error('An interview begin was not set.',
            ACTION_INTERVIEW_ERR_NO_INTERVIEW_BEGIN);
            return;
        }
        $this->m_properties['interview_begin'] = $p_input['f_interview_interview_begin'];
        
        if (strlen($p_input['f_interview_interview_end']) != 10) {
            $this->m_error = new PEAR_Error('An interview end was not set.',
            ACTION_INTERVIEW_ERR_NO_INTERVIEW_END);
            return;
        }
        $this->m_properties['interview_end'] = $p_input['f_interview_interview_end'];
        
        if (strlen($p_input['f_interview_questions_begin']) != 10) {
            $this->m_error = new PEAR_Error('An questions begin was not set.',
            ACTION_INTERVIEW_ERR_NO_QUESTIONS_BEGIN);
            return;
        }
        $this->m_properties['questions_begin'] = $p_input['f_interview_questions_begin'];

        if (strlen($p_input['f_interview_questions_end']) != 10) {
            $this->m_error = new PEAR_Error('An questions end was not set.',
            ACTION_INTERVIEW_ERR_NO_QUESTIONS_END);
            return;
        }
        $this->m_properties['questions_end'] = $p_input['f_interview_questions_end'];

        if (strlen($p_input['f_interview_questions_limit'])) {
            $this->m_properties['questions_limit'] = $p_input['f_interview_questions_limit'];
        } else {
            $this->m_properties['questions_limit'] = 0;
        }
        
        $this->m_properties['image_delete'] = $p_input['f_interview_image_delete'];
        $this->m_properties['image_description'] = $p_input['f_interview_image_description'];
        $files = CampRequest::GetInput('files');
        $this->m_properties['image'] = $files['f_interview_image'];
        
        $this->m_interview = new Interview($p_input['f_interview_id']);
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {        
        if (!is_object($this->m_interview)) {
            return false;   
        } 
        
        $User = $p_context->user;
        if (!$User->has_permission('plugin_interview_admin')) {
            $this->m_error = new PEAR_Error('User have no permission to maintain interviews.', ACTION_INTERVIEW_ERR_NO_PERMISSION);
            return false;   
        }
         
        $image_id = $this->m_interview->getProperty('fk_image_id');
        
        if ($this->m_properties['image_delete'] && $image_id) {
            $Image = new Image($this->m_interview->getProperty('fk_image_id'));
            $Image->delete();
            $image_id = null;    
        } else {
            $file = $this->m_properties['image'];
            if (strlen($file['name'])) {
                $attributes = array(
                    'Description' => strlen($this->m_properties['f_interview_image_description']) ? $this->m_properties['f_interview_image_description'] : $file['name'],
                );
                $Image = Image::OnImageUpload($file, $attributes, $p_user_id, !empty($image_id) ? $image_id : null);
                if (is_a($Image, 'Image')) {
                    $image_id = $Image->getProperty('Id');   
                } else {
                    return false;    
                }
            }
        }
        
        if ($this->m_interview->exists()) {
            // edit existing interview    
            $this->m_interview->setProperty('fk_language_id', $this->m_properties['language_id']);
            $this->m_interview->setProperty('fk_moderator_user_id', $this->m_properties['moderator_user_id']);
            $this->m_interview->setProperty('fk_guest_user_id', $this->m_properties['guest_user_id']);
            $this->m_interview->setProperty('title', $this->m_properties['title']);
            $this->m_interview->setProperty('fk_image_id', $image_id);
            $this->m_interview->setProperty('description_short', $this->m_properties['description_short']);
            $this->m_interview->setProperty('description', $this->m_properties['description']);
            $this->m_interview->setProperty('interview_begin', $this->m_properties['interview_begin']);
            $this->m_interview->setProperty('interview_end', $this->m_properties['interview_end']);
            $this->m_interview->setProperty('questions_begin', $this->m_properties['questions_begin']);
            $this->m_interview->setProperty('questions_end', $this->m_properties['questions_end']);
            $this->m_interview->setProperty('questions_limit', $this->m_properties['questions_limit']);
            #$this->m_interview->setProperty('status', $this->m_properties['status']);
            
            $this->m_error = ACTION_OK;
            return true;
            
        } else {
            // create new interview
            if ($this->m_interview->create(
                $this->m_properties['language_id'], 
                $this->m_properties['moderator_user_id'], 
                $this->m_properties['guest_user_id'],
                $this->m_properties['title'], 
                $image_id, 
                $this->m_properties['description_short'], 
                $this->m_properties['description'],
                $this->m_properties['interview_begin'], 
                $this->m_properties['interview_end'],
                $this->m_properties['questions_begin'],
                $this->m_properties['questions_end'],
                $this->m_properties['questions_limit']
            )) {
                $_REQUEST['f_interview_id'] = $this->m_interview->getProperty('interview_id');
                $this->m_error = ACTION_OK;
                return true;   
            }   
            
        }
        return false;
    }
}

?>