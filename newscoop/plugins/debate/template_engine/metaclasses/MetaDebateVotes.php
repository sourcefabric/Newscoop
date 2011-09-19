<?php
/**
 * @package Campsite
 */
final class MetaDebateVotes
{
    private $m_properties = array( 'number' => null, 'value' => null, 'percentage' => null );

    public function __construct($answerArray)
    {
        if (isset($answerArray['answer_nr'])) {
            $this->m_properties['number'] = $answerArray['answer_nr'];
        }
        if (isset($answerArray['value'])) {
            $this->m_properties['value'] = $answerArray['value'];
        }
        if (isset($answerArray['percentage'])) {
            $this->m_properties['percentage']  = $answerArray['percentage'];
        }
    }

    public function __get($name)
    {
        return isset($this->m_properties[$name]) ? $this->m_properties[$name] : null;
    }
}