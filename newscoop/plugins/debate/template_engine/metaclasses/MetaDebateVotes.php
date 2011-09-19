<?php
/**
 * @package Campsite
 */
final class MetaDebateVotes
{
    public $number;
    public $value;
    public $percentage;

    public function __construct($answerArray)
    {
        if (isset($answerArray['answer_nr'])) {
            $this->number = $answerArray['answer_nr'];
        }
        if (isset($answerArray['value'])) {
            $this->value = $answerArray['value'];
        }
        if (isset($answerArray['percentage'])) {
            $this->percentage  = $answerArray['percentage'];
        }
    }
}