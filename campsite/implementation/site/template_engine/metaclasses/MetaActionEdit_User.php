<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/User.php');


define('ACTION_EDIT_USER_ERR_INTERNAL', 'action_edit_user_err_internal');
define('ACTION_EDIT_USER_ERR_NO_USER_NAME', 'action_edit_user_err_no_user_name');
define('ACTION_EDIT_USER_ERR_DUPLICATE_USER_NAME', 'action_edit_user_err_duplicate_user_name');
define('ACTION_EDIT_USER_ERR_NO_NAME', 'action_edit_user_err_no_name');
define('ACTION_EDIT_USER_ERR_NO_PASSWORD', 'action_edit_user_err_no_password');
define('ACTION_EDIT_USER_ERR_NO_PASSWORD_CONFIRMATION', 'action_edit_user_err_no_password_confirmation');
define('ACTION_EDIT_USER_ERR_PASSWORD_MISMATCH', 'action_edit_user_err_password_mismatch');
define('ACTION_EDIT_USER_ERR_NO_EMAIL', 'action_edit_user_err_no_email');
define('ACTION_EDIT_USER_ERR_DUPLICATE_EMAIL', 'action_edit_user_err_duplicate_email');
define('ACTION_EDIT_USER_ERR_INVALID_CREDENTIALS', 'action_edit_user_err_invalid_credentials');


class MetaActionEdit_User extends MetaAction
{
    static private $m_fields = array(
    	'name'=>array('mandatory', 'type'=>'text', 'max_size'=>255),
        'uname'=>array('mandatory', 'type'=>'text', 'max_size'=>70),
    	'password'=>array('mandatory', 'type'=>'password', 'max_size'=>64),
        'passwordagain'=>array('mandatory', 'type'=>'password', 'max_size'=>64),
    	'email'=>array('mandatory', 'type'=>'text', 'max_size'=>255),
    	'city'=>array('type'=>'text', 'max_size'=>100),
    	'straddress'=>array('type'=>'text', 'max_size'=>255),
    	'state'=>array('type'=>'text', 'max_size'=>32),
    	'phone'=>array('type'=>'text', 'max_size'=>20),
    	'fax'=>array('type'=>'text', 'max_size'=>20),
    	'contact'=>array('type'=>'text', 'max_size'=>64),
    	'phone2'=>array('type'=>'text', 'max_size'=>20),
    	'postalcode'=>array('type'=>'text', 'max_size'=>70),
    	'employer'=>array('type'=>'text', 'max_size'=>140),
    	'position'=>array('type'=>'text', 'max_size'=>70),
    	'interests'=>array('type'=>'textarea', 'width'=>60, 'height'=>3),
    	'how'=>array('type'=>'text', 'max_size'=>255),
    	'languages'=>array('type'=>'text', 'max_size'=>100),
    	'improvements'=>array('type'=>'textarea', 'width'=>60, 'height'=>3),
    	'field1'=>array('type'=>'text', 'max_size'=>150),
    	'field2'=>array('type'=>'text', 'max_size'=>150),
    	'field3'=>array('type'=>'text', 'max_size'=>150),
    	'field4'=>array('type'=>'text', 'max_size'=>150),
    	'field5'=>array('type'=>'text', 'max_size'=>150),
    	'text1'=>array('type'=>'textarea', 'width'=>60, 'height'=>3),
    	'text2'=>array('type'=>'textarea', 'width'=>60, 'height'=>3),
        'text3'=>array('type'=>'textarea', 'width'=>60, 'height'=>3),
    	'country'=>array('type'=>'select'),
    	'title'=>array('type'=>'select'),
    	'gender'=>array('type'=>'select'),
    	'age'=>array('type'=>'select'),
    	'employertype'=>array('type'=>'select'),
    	'pref1'=>array('type'=>'select'),
    	'pref2'=>array('type'=>'select'),
        'pref3'=>array('type'=>'select'),
        'pref4'=>array('type'=>'select')
    );

    private $m_input;

    private $m_user;


    /**
     * Reads the input parameters and sets up the login action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'edit_user';
        $this->m_input = $p_input;
        $this->m_error = null;
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        if (PEAR::isError($this->m_error)) {
            return false;
        }

        $metaUser = $p_context->user;
        if (!$metaUser->defined) {
            if (!MetaAction::ValidateInput($this->m_input, 'f_user_name', 1,
            $this->m_error, 'The user name was not filled in.', ACTION_EDIT_USER_ERR_NO_NAME)) {
                return false;
            }
            if (!MetaAction::ValidateInput($this->m_input, 'f_user_uname', 1,
            $this->m_error, 'The user login name was not filled in.',
            ACTION_EDIT_USER_ERR_NO_USER_NAME)) {
                return false;
            }
            if (!MetaAction::ValidateInput($this->m_input, 'f_user_password', 6,
            $this->m_error, 'The user password was not filled in or was too short.',
            ACTION_EDIT_USER_ERR_NO_PASSWORD)) {
                return false;
            }
            if (!MetaAction::ValidateInput($this->m_input, 'f_user_password', 6,
            $this->m_error, 'The password confirmation was not filled in or was too short.',
            ACTION_EDIT_USER_ERR_NO_PASSWORD_CONFIRMATION)) {
                return false;
            }
            if (!MetaAction::ValidateInput($this->m_input, 'f_user_email', 8,
            $this->m_error, 'The user email was not filled in or was invalid.',
            ACTION_EDIT_USER_ERR_NO_EMAIL)) {
                return false;
            }
        } else {
            if (isset($this->m_input['f_user_password'])) {
                if (!MetaAction::ValidateInput($this->m_input, 'f_user_password', 6,
                $this->m_error, 'The user password was not filled in or was too short.',
                ACTION_EDIT_USER_ERR_NO_PASSWORD)) {
                    return false;
                }
                if (!MetaAction::ValidateInput($this->m_input, 'f_user_password', 6,
                $this->m_error, 'The password confirmation was not filled in or was too short.',
                ACTION_EDIT_USER_ERR_NO_PASSWORD_CONFIRMATION)) {
                    return false;
                }
            }
        }
        if (isset($this->m_properties['password'])
        && $this->m_properties['password'] != $this->m_properties['passwordagain']) {
            $this->m_error = new PEAR_Error("The password and password confirmation do not match.",
            ACTION_EDIT_USER_ERR_PASSWORD_MISMATCH);
            return false;
        }

        foreach (MetaActionEdit_User::$m_fields as $field=>$fieldConstraints) {
            if (isset($this->m_input['f_user_'.$field])) {
                $this->m_properties[$field] = $this->m_input['f_user_'.$field];
            }
        }

        if (!$metaUser->defined) {
            if (User::UserNameExists($this->m_properties['uname'])
            || Phorum_user::UserNameExists($this->m_properties['uname'])) {
                $this->m_error = new PEAR_Error("The login name already exists, please choose a different one.",
                ACTION_EDIT_USER_ERR_DUPLICATE_USER_NAME);
                return false;
            }
            if (User::EmailExists($this->m_properties['email'])) {
                $this->m_error = new PEAR_Error("Another user is registered with this e-mail address, please choose a different one.",
                ACTION_EDIT_USER_ERR_DUPLICATE_EMAIL);
                return false;
            }
            $user = new User();
            $phorumUser = new Phorum_user();
            if (!$user->create($this->m_properties)
            || !$phorumUser->create($this->m_properties['uname'], $this->m_properties['password'], $this->m_properties['email'], $user->getUserId())) {
                $user->delete();
                $phorumUser->delete();
                $this->m_error = new PEAR_Error("There was an internal error creating the account (code 1).",
                ACTION_EDIT_USER_ERR_INTERNAL);
                return false;
            }
        } else {
            $user = new User($metaUser->identifier);
            if (!$user->exists()) {
                $this->m_error = new PEAR_Error("There was an internal error updating the account (code 2).",
                ACTION_EDIT_USER_ERR_INTERNAL);
                return false;
            }
            $phorumUser = Phorum_user::GetByUserName($user->getUserName());
            if (is_null($phorumUser)) {
                $phorumUser = new Phorum_user();
                if (!$phorumUser->create($user->getUserName(), $user->getPassword(), $user->getEmail(), $user->getUserId(), true)) {
                    $this->m_error = new PEAR_Error("There was an internal error updating the account (code 3).",
                    ACTION_EDIT_USER_ERR_INTERNAL);
                    return false;
                }
            }
            foreach ($this->m_properties as $property=>$value) {
                if ($property != 'password' && $property != 'passwordagain') {
                    $user->setProperty($property, $value, false);
                    if ($property == 'email') {
                        $phorumUser->setProperty($property, $value, false);
                    }
                } elseif ($property == 'password') {
                    $user->setPassword($this->m_properties['password'], false);
                    $phorumUser->setPassword($this->m_properties['password']);
                }
            }
            if (!$user->commit() || !$phorumUser->commit()) {
                $this->m_error = new PEAR_Error("There was an internal error updating the account (code 4).",
                ACTION_EDIT_USER_ERR_INTERNAL);
                return false;
            }
        }

        $this->m_error = ACTION_OK;
        return true;
    }
}

?>