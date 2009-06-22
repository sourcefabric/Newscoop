<?php
/**
 * @package Campsite
 */
final class MetaInterview extends MetaDbObject {

    protected $single = true;
    
	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['identifier'] = 'interview_id';
		$this->m_properties['language_id'] = 'fk_language_id';
		$this->m_properties['title'] = 'title';
		$this->m_properties['name'] = 'title';
		$this->m_properties['description_short'] = 'description_short';
		$this->m_properties['description'] = 'description';	
		$this->m_properties['questions_begin'] = 'questions_begin';
		$this->m_properties['questions_end'] = 'questions_end';
		$this->m_properties['interview_begin'] = 'interview_begin';
		$this->m_properties['interview_end'] = 'interview_end';	
		$this->m_properties['questions_limit'] = 'questions_limit';
		$this->m_properties['status'] = 'status';
		$this->m_properties['last_modified'] = 'last_modified';
		$this->m_properties['moderator_user_id'] = 'fk_moderator_user_id';
		$this->m_properties['guest_user_id'] = 'fk_guest_user_id';
		$this->m_properties['guest_invitation_sent'] = 'guest_invitation_sent';
		$this->m_properties['questioneer_invitation_sent'] = 'questioneer_invitation_sent';
		$this->m_properties['order'] = 'position';
		$this->m_properties['position'] = 'position';
		$this->m_properties['last_modified'] = 'last_modified';
	}


    public function __construct($p_interview_id = null)
    {
		$this->m_dbObject =& new Interview($p_interview_id);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['image'] = 'getImage';
        $this->m_customProperties['image_description'] = 'getImageDescription';
        $this->m_customProperties['image_delete'] = 'null';
        $this->m_customProperties['language'] = 'getLanguage';
        $this->m_customProperties['moderator'] = 'getModerator';
        $this->m_customProperties['guest'] = 'getGuest';
        $this->m_customProperties['user_is_admin'] = 'userIsAdmin';
        $this->m_customProperties['user_is_moderator'] = 'userIsModerator';
        $this->m_customProperties['user_is_guest'] = 'userIsGuest';
        $this->m_customProperties['in_questions_timeframe'] = 'inQuestionsTimeframe';
        $this->m_customProperties['in_interview_timeframe'] = 'inInterviewTimeframe';
        $this->m_customProperties['nr_questions'] = 'nrQuestions';
        $this->m_customProperties['nr_answeres'] = 'nrAnswers';
        

    } // fn __construct
    
    public function isInValid()
    {
        if (strtotime($this->date_begin) > time()) {
            return false;   
        }
        if (strtotime($this->date_end) < time()) {
            return false;   
        }
        
        return true;  
    }
    
    public function getLanguage()
    {
        $Language = new MetaLanguage($this->m_dbObject->getProperty('fk_language_id'));
        return $Language;   
    }
    
    public function getModerator()
    {
        $Moderator = new MetaUser($this->m_dbObject->getProperty('fk_moderator_user_id'));
        return $Moderator;   
    }
    
    public function getGuest()
    {
        $Guest = new MetaUser($this->m_dbObject->getProperty('fk_guest_user_id'));
        return $Guest;   
    }
    
    public function getImage()
    {
        $image_id = $this->m_dbObject->getProperty('fk_image_id');
        $MetaImage = new MetaImage($image_id);
        
        return $MetaImage;   
    }
    
    public function getImageDescription()
    {
        $MetaImage = $this->getImage();
        
        return $MetaImage->description;   
    }
    
    public function null()
    {
        return null;
    }
    
    public function userIsAdmin(&$p_context = null)
    {
        if (is_object($p_context)) {
            $context = $p_context;   
        } else {
            $context = CampTemplate::singleton()->context();
        }
        
        if ($context->user->has_permission('plugin_interview_admin')) {
            return true;   
        }
        return false;
    }
    
    public function userIsModerator(&$p_context = null)
    {
        if (is_object($p_context)) {
            $context = $p_context;   
        } else {
            $context = CampTemplate::singleton()->context();
        }
        
        if ($context->user->has_permission('plugin_interview_moderator')) {
            return true;   
        }
        return false;
    }

    public function userIsGuest(&$p_context = null)
    {
        if (is_object($p_context)) {
            $context = $p_context;   
        } else {
            $context = CampTemplate::singleton()->context();
        }
        
        if ($context->user->identifier == $this->guest_user_id) {
            return true;   
        }
        if ($context->user->has_permission('plugin_interview_guest')) {
            return true;   
        }
        return false;
    }
    
    public function inQuestionsTimeframe()
    {
        if (strtotime($this->questions_begin) <= time() && strtotime($this->questions_end) >= time()) {
            return true;   
        } 
        return false;
    }
    
    public function inInterviewTimeframe()
    {
        if (strtotime($this->interview_begin) <= time() && strtotime($this->interview_end) >= time()) {
            return true;   
        } 
        return false;
    }
    
    public function nrQuestions()
    {
        $start = 0;
        $params = array('constraints' => 'status not rejected');
        $itemsList = new InterviewItemsList($start, $params);
        return $itemsList->count;
    }
    
    public function nrAnswers()
    {
        $start = 0;
        $params = array('constraints' => 'status is published');
        $itemsList = new InterviewItemsList($start, $params);
        return $itemsList->count;
    }
    
} // class MetaInterview

?>