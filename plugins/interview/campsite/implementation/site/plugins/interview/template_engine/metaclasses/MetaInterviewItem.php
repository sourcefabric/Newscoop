<?php
/**
 * @package Campsite
 */
final class MetaInterviewItem extends MetaDbObject {

    protected $single = true;
    
	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['identifier'] = 'item_id';
		$this->m_properties['interview_id'] = 'fk_interview_id';
		$this->m_properties['question'] = 'question';
		$this->m_properties['status'] = 'status';
		$this->m_properties['answer'] = 'answer';
		$this->m_properties['item_order'] = 'item_order';		
		$this->m_properties['last_modified'] = 'last_modified';
	}


    public function __construct($p_item_id = null)
    {
		$this->m_dbObject =& new InterviewItem(null, $p_item_id);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
		$this->m_customProperties['questioneer'] = 'getQuestioneer';
        $this->m_customProperties['interview'] = 'getInterview';

    } // fn __construct
    
   
    protected function getQuestioneer()
    {
        $questioneer = new MetaUser($this->m_dbObject->getProperty('fk_questioneer_user_id'));
        return $questioneer;   
    }
    
    protected function getInterview()
    {
        $interview_id = $this->m_dbObject->getProperty('interview_id');
        
        $MetaInterview = new MetaInterview($interview_id);
        
        return $MetaInterview;   
    }

} // class MetaInterviewItem

?>