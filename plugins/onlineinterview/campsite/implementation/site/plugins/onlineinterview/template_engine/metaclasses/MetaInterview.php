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
		$this->m_properties['questions_limit'] = 'questions_limit';
		$this->m_properties['status'] = 'status';
		$this->m_properties['last_modified'] = 'last_modified';
	}


    public function __construct($p_interview_id = null)
    {
		$this->m_dbObject =& new Interview($p_interview_id);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['image'] = 'getImage';
        $this->m_customProperties['interview_begin'] = 'getInterviewBegin';
		$this->m_customProperties['interview_end'] = 'getInterviewEnd';
        $this->m_customProperties['questions_begin'] = 'getInterviewBegin';
		$this->m_customProperties['questions_end'] = 'getInterviewEnd';
        $this->m_customProperties['language'] = 'getLanguage';
        $this->m_customProperties['moderator'] = 'getModerator';
        $this->m_customProperties['invitee'] = 'getInvitee';
        $this->m_customProperties['store'] = 'store';
        $this->m_customProperties['set_draft'] = 'set_draft';
        $this->m_customProperties['set_pending'] = 'set_pending';
        $this->m_customProperties['set_published'] = 'set_published';
        $this->m_customProperties['set_offline'] = 'set_offline';
        $this->m_customProperties['current_invitee'] = 'getCurrentInvitee';

    } // fn __construct
    
    public function isInValid()
    {
        if ($this->date_begin > strtotime(date('Y-m-d'))) {
            return false;   
        }
        if (empty($this->is_show_after_expiration) && ($this->date_end + 60*60*24 < strtotime(date('Y-m-d')))) {
            return false;   
        }
        
        return true;  
    }
    
    public function getInterviewBegin()
    {
        return strtotime($this->m_dbObject->getProperty('interview_begin'));   
    }
    
    public function getInterviewEnd()
    {
        return strtotime($this->m_dbObject->getProperty('interview_end'));   
    }
    
    public function getQuestionsBegin()
    {
        return strtotime($this->m_dbObject->getProperty('questions_begin'));   
    }
    
    public function getQuestionsEnd()
    {
        return strtotime($this->m_dbObject->getProperty('questions_end'));   
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
    
    public function getInvitee()
    {
        $Invitee = new MetaUser($this->m_dbObject->getProperty('fk_invitee_user_id'));
        return $Invitee;   
    }
    
    

    public function store()
    {
        $Interview = new Interview($this->m_dbObject->getProperty('interview_id'));
        return $Interview->store($this->m_dbObject->getProperty('fk_moderator_user_id'));
    }
    
    public function getImage()
    {
        $image_id = $this->m_dbObject->getProperty('fk_image_id');
        $MetaImage = new MetaImage($image_id);
        
        return $MetaImage;   
    }
    
            
    public function set_draft()
    {
        $Interview = new Interview($this->m_dbObject->getProperty('interview_id'));
        $Interview->setProperty('status', 'draft');     
    }
    
    public function set_pending()
    {
        $Interview = new Interview($this->m_dbObject->getProperty('interview_id'));
        $Interview->send_invitations();
        $Interview->setProperty('status', 'pending');     
    }
    
    public function getCurrentQuestioneer()
    {
        $User = $this->m_dbObject->current_questioneer;
        $MetaUser = new MetaUser($User->getProperty('Id'));
        return $MetaUser;   
    }
        
    public function set_published()
    {
        $Interview = new Interview($this->m_dbObject->getProperty('interview_id'));
        $Interview->send_invitations();
        $Interview->setProperty('status', 'published');     
    }    
        
    public function set_offline()
    {
        $Interview = new Interview($this->m_dbObject->getProperty('interview_id'));
        $Interview->send_invitations();
        $Interview->setProperty('status', 'offline');     
    }
} // class MetaInterview

?>