<?php


define('ACTION_LOGIN_ERR_NO_USER_NAME', 'action_login_err_no_user_name');
define('ACTION_LOGIN_ERR_NO_PASSWORD', 'action_login_err_no_password');
define('ACTION_LOGIN_ERR_INVALID_CREDENTIALS', 'action_login_err_invalid_credentials');


class MetaActionLogin extends MetaAction
{
    private $m_user;


    public function __construct(array $p_input)
    {
        if (!isset($p_input['f_user_name'])) {
            $this->m_error = new PEAR_Error('The user name was not filled in.',
            ACTION_LOGIN_ERR_NO_USER_NAME);
            return;
        }
        if (!isset($p_input['f_password'])) {
            $this->m_error = new PEAR_Error('The password was not filled in.',
            ACTION_LOGIN_ERR_NO_PASSWORD);
            return;
        }
        $user = User::FetchUserByName($p_input['f_user_name']);
        if (is_null($user) || !$user->isValidPassword($p_input['f_password'])) {
            $this->m_error = new PEAR_Error('Invalid user credentials',
            ACTION_LOGIN_ERR_INVALID_CREDENTIALS);
            return;
        }
        $this->m_defined = true;
        $this->m_error = null;
        $this->m_properties['user_name'] = $p_input['f_user_name'];
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
    	setcookie("LoginUserId", $this->m_user->getUserId());
	    setcookie("LoginUserKey", $this->m_user->getKeyId());
	    $p_context->user = new MetaUser($this->m_user->getUserId());
        return true;
    }
}

?>