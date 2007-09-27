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
		$this->m_properties['date_begin'] = 'date_begin';
		$this->m_properties['date_end'] = 'date_end';
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
        $this->m_customProperties['getpolls'] = 'getPolls';
    } // fn __construct

} // class MetaPoll

?>