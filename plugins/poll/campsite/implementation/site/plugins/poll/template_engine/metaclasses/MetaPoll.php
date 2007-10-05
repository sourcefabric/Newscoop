<?php
/**
 * @package Campsite
 */
final class MetaPoll extends MetaDbObject {

    protected $single = true;
    
	private function InitProperties()
	{
		if (!is_null($this->m_properties)) {
			return;
		}
		$this->m_properties['number'] = 'poll_nr';
		$this->m_properties['language_id'] = 'fk_language_id';
		$this->m_properties['title'] = 'title';
		$this->m_properties['name'] = 'title';
		$this->m_properties['question'] = 'question';
		$this->m_properties['nr_of_answers'] = 'nr_of_answers';
		$this->m_properties['is_show_after_expiration'] = 'is_show_after_expiration';
		$this->m_properties['is_used_as_default'] = 'is_used_as_default';
		$this->m_properties['nr_of_votes'] = 'nr_of_votes';
		$this->m_properties['nr_of_votes_overall'] = 'nr_of_votes_overall';
		$this->m_properties['percentage_of_votes_overall'] = 'percentage_of_votes_overall';
		$this->m_properties['last_modified'] = 'last_modified';
	}


    public function __construct($p_languageId = null, $p_poll_nr = null)
    {
		$this->m_dbObject =& new Poll($p_languageId, $p_poll_nr);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['in_time'] = 'isInValid';
        $this->m_customProperties['date_begin'] = 'getDateBegin';
		$this->m_customProperties['date_end'] = 'getDateEnd';
        $this->m_customProperties['getpolls'] = 'getPolls';
        $this->m_customProperties['identifier'] = 'getIdentifier';
        $this->m_customProperties['form_hidden'] = 'formHidden';
        $this->m_customProperties['register_voting'] = 'registerVoting'; 
        $this->m_customProperties['votable'] = 'isVotable';
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
    
    public function getDateBegin()
    {
        return strtotime($this->m_dbObject->getProperty('date_begin'));   
    }
    
    public function getDateEnd()
    {
        return strtotime($this->m_dbObject->getProperty('date_end'));   
    }
    
    public function getIdentifier()
    {
        $id = $this->m_dbObject->getProperty('fk_language_id').'_'.$this->m_dbObject->getProperty('poll_nr');
        return $id;     
    }
    
    public function formHidden()
    {
        $language_id = $this->m_dbObject->getProperty('fk_language_id');
        $poll_nr = $this->m_dbObject->getProperty('poll_nr');
        
        $html .= "<INPUT TYPE=\"hidden\" NAME=\"IdLanguage\" VALUE=\"$language_id\" />\n";
        $html .= "<INPUT TYPE=\"hidden\" NAME=\"poll_nr\" VALUE=\"$poll_nr\" />\n";
        
        return $html;   
    }
    
    public function isVotable()
    {
        return $this->m_dbObject->isVotable();   
    }

} // class MetaPoll

?>