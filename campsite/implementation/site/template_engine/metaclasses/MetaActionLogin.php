<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');


define('ACTION_LOGIN_ERR_NO_USER_NAME', 'action_login_err_no_user_name');
define('ACTION_LOGIN_ERR_NO_PASSWORD', 'action_login_err_no_password');
define('ACTION_LOGIN_ERR_INVALID_CREDENTIALS', 'action_login_err_invalid_credentials');


class MetaActionLogin extends MetaAction
{
    private $m_user;


    /**
     * Reads the input parameters and sets up the login action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'login';
        if (!isset($p_input['f_user_uname']) || empty($p_input['f_user_uname'])) {
            $this->m_error = new PEAR_Error('The user name was not filled in.',
            ACTION_LOGIN_ERR_NO_USER_NAME);
            return;
        }
        $this->m_properties['user_name'] = $p_input['f_user_uname'];
        if (!isset($p_input['f_user_password'])) {
            $this->m_error = new PEAR_Error('The password was not filled in.',
            ACTION_LOGIN_ERR_NO_PASSWORD);
            return;
        }
        $user = User::FetchUserByName($p_input['f_user_uname']);
        if (is_null($user) || !$user->isValidPassword($p_input['f_user_password'])) {
            $this->m_error = new PEAR_Error('Invalid user credentials',
            ACTION_LOGIN_ERR_INVALID_CREDENTIALS);
            return;
        }
        $this->m_error = ACTION_OK;
        $this->m_user = $user;
        $this->m_user->initLoginKey();
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        $p_context->default_url->reset_parameter('f_'.$this->m_name);
        $p_context->url->reset_parameter('f_'.$this->m_name);

        $p_context->default_url->reset_parameter('f_user_uname');
        $p_context->url->reset_parameter('f_user_uname');
        $p_context->default_url->reset_parameter('f_user_password');
        $p_context->url->reset_parameter('f_user_password');

        if ($this->m_error != ACTION_OK) {
            return false;
        }

        setcookie("LoginUserId", $this->m_user->getUserId(), null, '/');
        setcookie("LoginUserKey", $this->m_user->getKeyId(), null, '/');
        $p_context->user = new MetaUser($this->m_user->getUserId());
        return true;
    }
}

?>