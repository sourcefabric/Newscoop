<?php
/**
 * @package Campsite
 */
final class MetaPollAnswer extends MetaDbObject {

	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['poll_nr'] = 'fk_poll_nr';
		$this->m_properties['language_id'] = 'fk_language_id';
		$this->m_properties['nr_answer'] = 'nr_answer';
		$this->m_properties['answer'] = 'answer';
		$this->m_properties['nr_of_votes'] = 'nr_of_votes';
		$this->m_properties['percentage'] = 'percentage';
		$this->m_properties['percentage_overall'] = 'percentage_overall';
		$this->m_properties['last_modified'] = 'last_modified';
	}


    public function __construct($p_languageId = null, $p_poll_nr = null, $p_nr_answer)
    {
		$this->m_dbObject =& new PollAnswer($p_languageId, $p_poll_nr, $p_nr_answer);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['getpollanswers'] = 'getPollAnswers';
        $this->m_customProperties['identifier'] = 'getIdentifier';
        $this->m_customProperties['form_radio'] = 'formRadio';
    } // fn __construct
    
    public function getIdentifier()
    {
        $id = $this->language_id.'_'.$this->poll_nr.'_'.$this->nr_answer;
        return $id;     
    }
    
    public function formRadio()
    {   
        $id = $this->getIdentifier();
        return "<INPUT TYPE=\"radio\" name=\"poll_answer[$id]\" />";   
    }
} // class MetaPoll

?>