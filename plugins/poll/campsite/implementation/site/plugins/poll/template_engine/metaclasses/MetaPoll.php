<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');

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
        $id = $this->getIdentifier();
        $html = "<INPUT TYPE=\"hidden\" NAME=\"poll_id\" VALUE=\"$id\" />\n";
        return $html;   
    }
    
    public function isVotable()
    {
        if ($this->date_begin > strtotime(date('Y-m-d'))) {
            return false;   
        }
        if ($this->date_end < strtotime(date('Y-m-d')) + 60*60*24) {
            return false;   
        }
        if ($this->single && !empty($_SESSION['poll_'.$this->getIdentifier()])) {
            return false;   
        }
        
        return true;   
    }
    
    public function setUserHasVoted()
    {
        $_SESSION['poll_'.$this->getIdentifier()] = true;       
    }
    
    public function registerVoting()
    {      
        $answers = Input::Get('poll_answer', 'array');
        
        if (count($answers)) {
            foreach ($answers as $id => $v) {
                list ($language_id, $poll_nr, $nr_answer) = explode('_', $id);
                
                if ($language_id == $this->language_id && $poll_nr == $this->number) {
                    
                    if ($this->isVotable()) {
                        $pollAnswer =& new PollAnswer($language_id, $poll_nr, $nr_answer);
                        
                        if ($pollAnswer->exists()) {
                            $pollAnswer->vote();
                            $this->setUserHasVoted();
                        } 
                    }
                }
            }
        }
    }

} // class MetaPoll

?>