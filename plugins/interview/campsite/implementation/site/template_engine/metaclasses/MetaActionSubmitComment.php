<?php

define('ACTION_SUBMIT_COMMENT_ERR_', 'action_comment_submit_err_');


class MetaActionSubmitComment extends MetaAction
{
    /**
     * Reads the input parameters and sets up the login action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'commentsubmit';
        if (!isset($p_input['f_user_uname']) || empty($p_input['f_user_uname'])) {
            $this->m_error = new PEAR_Error('The user name was not filled in.',
            ACTION_LOGIN_ERR_NO_USER_NAME);
            return;
        }
        $this->m_error = ACTION_OK;
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        if ($this->m_error != ACTION_OK) {
            return false;
        }
        return true;
    }
}

?>