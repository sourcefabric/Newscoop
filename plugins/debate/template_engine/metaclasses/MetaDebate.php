<?php
/**
 * @package Newscoop 
 */
final class MetaDebate extends MetaDbObject
{
    protected $single = true;

    private function InitProperties()
    {
        $this->m_properties['number'] = 'debate_nr';
        $this->m_properties['language_id'] = 'fk_language_id';
        $this->m_properties['title'] = 'title';
        $this->m_properties['name'] = 'title';
        $this->m_properties['question'] = 'question';
        $this->m_properties['answers'] = 'nr_of_answers';
        $this->m_properties['votes_per_user'] = 'votes_per_user';
        $this->m_properties['allow_not_logged_in'] = 'allow_not_logged_in';
        $this->m_properties['results_time_unit'] = 'results_time_unit';
        $this->m_properties['votes'] = 'nr_of_votes';
        $this->m_properties['votes_overall'] = 'nr_of_votes_overall';
        $this->m_properties['percentage_overall'] = 'percentage_of_votes_overall';
        $this->m_properties['last_modified'] = 'last_modified';
    }

    public function __construct($p_languageId = null, $p_debate_nr = null, $p_user_id = null)
    {
        $this->m_dbObject = new Debate($p_languageId, $p_debate_nr, $p_user_id);

        $this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['is_current'] = 'isCurrent';
        $this->m_customProperties['date_begin'] = 'getDateBegin';
        $this->m_customProperties['date_end'] = 'getDateEnd';
        $this->m_customProperties['getdebates'] = 'getDebates';
        $this->m_customProperties['identifier'] = 'getIdentifier';
        $this->m_customProperties['is_votable'] = 'isVotable';
        $this->m_customProperties['is_closed'] = 'isClosed';
        $this->m_customProperties['is_started'] = 'isStarted';
        $this->m_customProperties['user_vote_count'] = 'getUserVoteCount';
    }

    /**
     * Whether the debate is currently active or not
     *
     * @return bool
     */
    public function isCurrent()
    {
        return ($this->isStarted() && !$this->isClosed()) ? true : false;
    }

    public function getResultsTimeUnit()
    {
        return strtotime($this->m_dbObject->getProperty('results_time_unit'));
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
        $id = $this->m_dbObject->getProperty('fk_language_id').'_'.$this->m_dbObject->getProperty('debate_nr');
        return $id;
    }

    public function getNr()
    {
        return $this->m_dbObject->getProperty('debate_nr');
    }

    public function getLanguageId()
    {
        return $this->m_dbObject->getProperty('fk_language_id');
    }

    public function isVotable()
    {
        return $this->m_dbObject->isVotable();
    }

    /**
     * Returns whether the debate is closed or not
     *
     * @return bool
     */
    public function isClosed()
    {
        return $this->m_dbObject->isClosed();
    }

    /**
     * Whether the debates has started or not
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->m_dbObject->isStarted();
    }

    public function getUserVoteCount()
    {
        return $this->m_dbObject->getUserVoteCount();
    }

}
