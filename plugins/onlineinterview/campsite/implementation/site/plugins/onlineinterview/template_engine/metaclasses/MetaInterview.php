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
		$this->m_properties['id'] = 'interview_id';
		$this->m_properties['language_id'] = 'fk_language_id';
		
		$this->m_properties['title'] = 'title';
		$this->m_properties['name'] = 'title';
		$this->m_properties['image'] = 'image';
		
		$this->m_properties['description_short'] = 'description_short';
		$this->m_properties['description'] = 'description';		
		$this->m_properties['questions_limit'] = 'questions_limit';
		
		$this->m_properties['last_modified'] = 'last_modified';
	}


    public function __construct($p_interview_id = null)
    {
		$this->m_dbObject =& new Interview($p_interview_id);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['in_time'] = 'isInValid';
        
        $this->m_customProperties['interview_begin'] = 'getInterviewBegin';
		$this->m_customProperties['interview_end'] = 'getInterviewEnd';
        $this->m_customProperties['questions_begin'] = 'getInterviewBegin';
		$this->m_customProperties['questions_end'] = 'getInterviewEnd';
		
        $this->m_customProperties['getpolls'] = 'getInterviews';
        $this->m_customProperties['identifier'] = 'getIdentifier';
        
        $this->m_customProperties['form_hidden'] = 'formHidden';

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
    
    public function formHidden()
    {
        $interview_id = $this->m_dbObject->getProperty('interview_id');
        
        $html .= "<INPUT TYPE=\"hidden\" NAME=\"interview_id\" VALUE=\"$interview_id\" />\n";
        
        return $html;   
    }

} // class MetaInterview

?>