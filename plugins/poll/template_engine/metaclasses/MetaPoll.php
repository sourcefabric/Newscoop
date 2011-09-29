<?php
/**
 * @package Campsite
 */
final class MetaPoll extends MetaDbObject {

    protected $single = true;

	private function InitProperties()
	{
		$this->m_properties['number'] = 'poll_nr';
		$this->m_properties['language_id'] = 'fk_language_id';
		$this->m_properties['title'] = 'title';
		$this->m_properties['name'] = 'title';
		$this->m_properties['question'] = 'question';
		$this->m_properties['answers'] = 'nr_of_answers';
		$this->m_properties['votes_per_user'] = 'votes_per_user';
		$this->m_properties['votes'] = 'nr_of_votes';
		$this->m_properties['votes_overall'] = 'nr_of_votes_overall';
		$this->m_properties['percentage_overall'] = 'percentage_of_votes_overall';
		$this->m_properties['last_modified'] = 'last_modified';
	}


    public function __construct($p_languageId = null, $p_poll_nr = null)
    {
		$this->m_dbObject = new Poll($p_languageId, $p_poll_nr);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['is_current'] = 'isCurrent';
        $this->m_customProperties['date_begin'] = 'getDateBegin';
		$this->m_customProperties['date_end'] = 'getDateEnd';
        $this->m_customProperties['getpolls'] = 'getPolls';
        $this->m_customProperties['identifier'] = 'getIdentifier';
        $this->m_customProperties['is_votable'] = 'isVotable';
        $this->m_customProperties['user_vote_count'] = 'getUserVoteCount';
    } // fn __construct

    public function isCurrent()
    {
        if (strtotime($this->date_begin) > strtotime(date('Y-m-d'))) {
            return false;
        }
        if (strtotime($this->date_end) < strtotime(date('Y-m-d'))) {
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

    public function isVotable()
    {
        return $this->m_dbObject->isVotable();
    }

    public function getUserVoteCount()
    {
        return $this->m_dbObject->getUserVoteCount();
    }

} // class MetaPoll

?>