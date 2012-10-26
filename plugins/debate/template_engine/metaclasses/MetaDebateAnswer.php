<?php
/**
 * @package Campsite
 */
final class MetaDebateAnswer extends MetaDbObject {

    private $voted = false;

	private function InitProperties()
	{
		$this->m_properties['debate_nr'] = 'fk_debate_nr';
		$this->m_properties['language_id'] = 'fk_language_id';
		$this->m_properties['number'] = 'nr_answer';
		$this->m_properties['answer'] = 'answer';
		$this->m_properties['votes'] = 'nr_of_votes';
		$this->m_properties['percentage'] = 'percentage';
		$this->m_properties['percentage_overall'] = 'percentage_overall';
		$this->m_properties['value'] = 'value';
		$this->m_properties['average_value'] = 'average_value';
		$this->m_properties['last_modified'] = 'last_modified';

		$this->m_customProperties['voted'] = 'getVoted';
	}

    /**
     * @param int $p_languageId
     * @param int $p_debate_nr
     * @param int $p_nr_answer
     * @param int $p_nr_voted voted answer number
     */
    public function __construct($p_languageId = null, $p_debate_nr = null, $p_nr_answer = null, $p_nr_voted = null)
    {
		$this->m_dbObject = new DebateAnswer($p_languageId, $p_debate_nr, $p_nr_answer);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->voted = ($p_nr_voted==$p_nr_answer);
    } // fn __construct

    public function getVoted()
    {
        return $this->voted;
    }

} // class MetaDebateAnswer

