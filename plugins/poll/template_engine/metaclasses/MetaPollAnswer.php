<?php
/**
 * @package Campsite
 */
final class MetaPollAnswer extends MetaDbObject {

	private function InitProperties()
	{
		$this->m_properties['poll_nr'] = 'fk_poll_nr';
		$this->m_properties['language_id'] = 'fk_language_id';
		$this->m_properties['number'] = 'nr_answer';
		$this->m_properties['answer'] = 'answer';
		$this->m_properties['votes'] = 'nr_of_votes';
		$this->m_properties['percentage'] = 'percentage';
		$this->m_properties['percentage_overall'] = 'percentage_overall';
		$this->m_properties['value'] = 'value';
		$this->m_properties['average_value'] = 'average_value';
		$this->m_properties['last_modified'] = 'last_modified';
	}


    public function __construct($p_languageId = null, $p_poll_nr = null, $p_nr_answer = null)
    {
		$this->m_dbObject = new PollAnswer($p_languageId, $p_poll_nr, $p_nr_answer);

		$this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct

} // class MetaPollAnswer

?>