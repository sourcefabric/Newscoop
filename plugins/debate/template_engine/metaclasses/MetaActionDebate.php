<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');


define('ACTION_DEBATE_ERR_NO_DEBATE_NUMBER',    'ACTION_DEBATE_ERR_NO_DEBATE_NUMBER');
define('ACTION_DEBATE_ERR_NO_LANGUAGE_ID',    'ACTION_DEBATE_ERR_NO_LANGUAGE_ID');
define('ACTION_DEBATE_ERR_NOT_EXISTS',        'ACTION_DEBATE_ERR_NOT_EXISTS');
define('ACTION_DEBATE_ERR_NOT_VOTABLE',       'ACTION_DEBATE_ERR_NOT_VOTABLE');
define('ACTION_DEBATE_ERR_INVALID_VALUE',     'ACTION_DEBATE_ERR_INVALID_VALUE');
define('ACTION_DEBATE_ERR_INVLID_MODE',       'ACTION_DEBATE_ERR_INVLID_MODE');

class MetaActionDebate extends MetaAction
{
    private $m_debate;

    /**
     * Reads the input parameters and vote the debate
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'debate';

        if (!isset($p_input['f_debate_nr']) || empty($p_input['f_debate_nr'])) {
            $this->m_error = new PEAR_Error('The debate number is missing.', ACTION_DEBATE_ERR_NO_DEBATE_NUMBER);
            return false;
        }
        $this->m_properties['debate_nr'] = $p_input['f_debate_nr'];

        if (!isset($p_input['f_debate_language_id']) || empty($p_input['f_debate_language_id'])) {
            $this->m_error = new PEAR_Error('The debate language is missing.', ACTION_DEBATE_ERR_NO_LANGUAGE_ID);
            return false;
        }
        $this->m_properties['debate_language_id'] = $p_input['f_debate_language_id'];

        if ($p_input['f_debate_mode'] !== 'standard' && $p_input['f_debate_mode'] !== 'ajax') {
            $this->m_error = new PEAR_Error('The debate mode parameter is invalid.', ACTION_DEBATE_ERR_INVLID_MODE);
            return false;
        }
        $this->m_properties['debate_mode'] = $p_input['f_debate_mode'];

        $Debate = new Debate($this->m_properties['debate_language_id'], $this->m_properties['debate_nr']);

        if (!$Debate->exists()) {
            $this->m_error = new PEAR_Error('Debate does not exists.', ACTION_DEBATE_ERR_NOT_EXISTS);
            return false;
        }

        // need to check this by user also and here if I try to get the user from CampContext it breaks
        // if (!$Debate->isVotable()) {
        //    $this->m_error = new PEAR_Error('Debate is not votable.', ACTION_DEBATE_ERR_NOT_VOTABLE);
        //    syslog(LOG_WARNING, 221);
        //    return false;
        //
        // } else {

            switch($p_input['f_debate_mode']) {
                case 'ajax':
                    $allowed_values = $_SESSION['camp_debate_maxvote'][$this->m_properties['debate_nr']][$this->m_properties['debate_language_id']];

                    if (!is_array($allowed_values)) {
                        $this->m_error = new PEAR_Error('Invalid debate voting value.', ACTION_DEBATE_ERR_INVALID_VALUE);
                        return false;
                    }

                    foreach ($Debate->getAnswers() as $DebateAnswer) {
                        $nr = $DebateAnswer->getNumber();

                        if (isset($p_input['f_debateanswer_'.$nr]) && !empty($p_input['f_debateanswer_'.$nr])) {

                            // check if value is valid
                            if (!array_key_exists($p_input['f_debateanswer_'.$nr], $allowed_values[$nr])) {
                                $this->m_error = new PEAR_Error('Invalid debate voting value.', ACTION_DEBATE_ERR_INVALID_VALUE);
                                return false;
                            }
                            $this->m_properties['debateanswer_nr'] = $nr;
                            $this->m_properties['value'] = $p_input['f_debateanswer_'.$nr];
                            break;
                        }
                    }
                    if (!$this->m_properties['value']) {
                        $this->m_error = new PEAR_Error('No answer value was given.', ACTION_DEBATE_ERR_NOANSWER_VALUE);
                        return false;
                    }
                break;

                case 'standard':

                    if (!isset($p_input['f_debateanswer_nr']) || empty($p_input['f_debateanswer_nr'])) {
                        $this->m_error = new PEAR_Error('Invalid debate voting value.', ACTION_DEBATE_ERR_INVALID_VALUE);
                        return false;
                    }
                    $this->m_properties['debateanswer_nr'] = $p_input['f_debateanswer_nr'];
                    $this->m_properties['value'] = 1;
                break;
            }
        // }

        $this->m_debate = $Debate;
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        $user = null;
        if ($p_context->user instanceof  MetaUser) {
            $user = $p_context->user->identifier;
        }
        if ($this->m_debate instanceof Debate) {
            $this->m_debate->setUserId($user);
        }
        else {
            return false;
        }

        if (!$this->m_debate->isVotable()) {
            $this->m_error = new PEAR_Error('Debate is not votable.', ACTION_DEBATE_ERR_NOT_VOTABLE);
            return false;
        }

        if (!is_object($this->m_debate)) {
            return false;
        }

        // vote the debate
        $DebateAnswer = new DebateAnswer($this->m_properties['debate_language_id'],
                                     $this->m_properties['debate_nr'],
                                     $this->m_properties['debateanswer_nr'], $user);
        $DebateAnswer->vote($this->m_properties['value']);

        // reset the f_debateanswer(_$nr) context vars
        $p_context->default_url->reset_parameter('f_debateanswer_nr');
        $p_context->url->reset_parameter('f_debateanswer_nr');

        foreach ($this->m_debate->getAnswers() as $DebateAnswer) {
            $nr = $DebateAnswer->getNumber();
            $p_context->default_url->reset_parameter('f_debateanswer_'.$nr);
            $p_context->url->reset_parameter('f_debateanswer_'.$nr);
        }

        $p_context->debatejustvoted = new MetaDebateAnswer($this->m_properties['debate_language_id'], $this->m_properties['debate_nr'], $this->m_properties['debateanswer_nr']);

        $this->m_error = ACTION_OK;
        return true;
    }
}

