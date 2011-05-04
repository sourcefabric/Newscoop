<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');


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
    	'name'=>array('mandatory', 'type'=>'text', 'max_size'=>255, 'db_field'=>'Name'),
        'uname'=>array('mandatory', 'type'=>'text', 'max_size'=>70, 'db_field'=>'UName'),
    	'password'=>array('mandatory', 'type'=>'password', 'max_size'=>64, 'db_field'=>'passwd'),
        'passwordagain'=>array('mandatory', 'type'=>'password', 'max_size'=>64),
    	'email'=>array('mandatory', 'type'=>'text', 'max_size'=>255, 'db_field'=>'EMail'),
    	'city'=>array('type'=>'text', 'max_size'=>100, 'db_field'=>'City'),
    	'str_address'=>array('type'=>'text', 'max_size'=>255, 'db_field'=>'StrAddress'),
    	'state'=>array('type'=>'text', 'max_size'=>32, 'db_field'=>'State'),
    	'phone'=>array('type'=>'text', 'max_size'=>20, 'db_field'=>'Phone'),
    	'fax'=>array('type'=>'text', 'max_size'=>20, 'db_field'=>'Fax'),
    	'contact'=>array('type'=>'text', 'max_size'=>64, 'db_field'=>'Contact'),
    	'second_phone'=>array('type'=>'text', 'max_size'=>20, 'db_field'=>'Phone2'),
    	'postal_code'=>array('type'=>'text', 'max_size'=>70, 'db_field'=>'PostalCode'),
    	'employer'=>array('type'=>'text', 'max_size'=>140, 'db_field'=>'Employer'),
    	'position'=>array('type'=>'text', 'max_size'=>70, 'db_field'=>'Position'),
    	'interests'=>array('type'=>'textarea', 'width'=>60, 'height'=>3, 'db_field'=>'Interests'),
    	'how'=>array('type'=>'text', 'max_size'=>255, 'db_field'=>'How'),
    	'languages'=>array('type'=>'text', 'max_size'=>100, 'db_field'=>'Languages'),
    	'improvements'=>array('type'=>'textarea', 'width'=>60, 'height'=>3, 'db_field'=>'Improvements'),
    	'field1'=>array('type'=>'text', 'max_size'=>150, 'db_field'=>'Field1'),
    	'field2'=>array('type'=>'text', 'max_size'=>150, 'db_field'=>'Field2'),
    	'field3'=>array('type'=>'text', 'max_size'=>150, 'db_field'=>'Field3'),
    	'field4'=>array('type'=>'text', 'max_size'=>150, 'db_field'=>'Field4'),
    	'field5'=>array('type'=>'text', 'max_size'=>150, 'db_field'=>'Field5'),
    	'text1'=>array('type'=>'textarea', 'width'=>60, 'height'=>3, 'db_field'=>'Text1'),
    	'text2'=>array('type'=>'textarea', 'width'=>60, 'height'=>3, 'db_field'=>'Text2'),
        'text3'=>array('type'=>'textarea', 'width'=>60, 'height'=>3, 'db_field'=>'Text3'),
    	'country'=>array('type'=>'select', 'db_field'=>'CountryCode'),
    	'title'=>array('type'=>'select', 'db_field'=>'Title'),
    	'gender'=>array('type'=>'select', 'db_field'=>'Gender'),
    	'age'=>array('type'=>'select', 'db_field'=>'Age'),
    	'employertype'=>array('type'=>'select', 'db_field'=>'EmployerType'),
    	'pref1'=>array('type'=>'select', 'db_field'=>'Pref1'),
    	'pref2'=>array('type'=>'select', 'db_field'=>'Pref2'),
        'pref3'=>array('type'=>'select', 'db_field'=>'Pref3'),
        'pref4'=>array('type'=>'select', 'db_field'=>'Pref4')
    );

    private $m_data;

    private $m_user;


    /**
     * Reads the input parameters and sets up the user edit action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'edit_user';
        $this->m_properties = array();
        $this->m_data = array();
        foreach ($p_input as $fieldName=>$fieldValue) {
            $fieldName = strtolower($fieldName);
            if (strncmp('f_user_', $fieldName, strlen('f_user_')) != 0) {
                continue;
            }
            $property = substr($fieldName, strlen('f_user_'));
            if (array_key_exists($property, MetaActionEdit_User::$m_fields)) {
                $this->m_properties[$property] = $fieldValue;
                if (isset(MetaActionEdit_User::$m_fields[$property]['db_field'])) {
                    $this->m_data[MetaActionEdit_User::$m_fields[$property]['db_field']] = $fieldValue;
                }
            }
        }
        if (isset($this->m_properties['password'])) {
            $this->m_data['passwd'] = $this->m_properties['password'];
        }
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
        $p_context->default_url->reset_parameter('f_'.$this->m_name);
        $p_context->url->reset_parameter('f_'.$this->m_name);

        if (PEAR::isError($this->m_error)) {
            return false;
        }

        $metaUser = $p_context->user;
        if (!$metaUser->defined) {
            $this->m_properties['type'] = 'add';
            if (!MetaAction::ValidateInput($this->m_properties, 'name', 1,
            $this->m_error, 'The user name was not filled in.', ACTION_EDIT_USER_ERR_NO_NAME)) {
                return false;
            }
            if (!MetaAction::ValidateInput($this->m_properties, 'uname', 1,
            $this->m_error, 'The user login name was not filled in.',
            ACTION_EDIT_USER_ERR_NO_USER_NAME)) {
                return false;
            }
            if (!MetaAction::ValidateInput($this->m_properties, 'password', 6,
            $this->m_error, 'The user password was not filled in or was too short.',
            ACTION_EDIT_USER_ERR_NO_PASSWORD)) {
                return false;
            }
            if (!MetaAction::ValidateInput($this->m_properties, 'passwordagain', 6,
            $this->m_error, 'The password confirmation was not filled in or was too short.',
            ACTION_EDIT_USER_ERR_NO_PASSWORD_CONFIRMATION)) {
                return false;
            }
            if (!MetaAction::ValidateInput($this->m_properties, 'email', 8,
            $this->m_error, 'The user email was not filled in or was invalid.',
            ACTION_EDIT_USER_ERR_NO_EMAIL)) {
                return false;
            }

            if (SystemPref::Get('PLUGIN_RECAPTCHA_SUBSCRIPTIONS_ENABLED') == 'Y') {
                $captcha = Captcha::factory('ReCAPTCHA');
                if (!$captcha->validate()) {
                    $this->m_error = new PEAR_Error('The code you entered is not the same as the one shown.',
                        ACTION_SUBMIT_COMMENT_ERR_INVALID_CAPTCHA_CODE);
                    return false;
                }
            }
        } else {
            $this->m_properties['type'] = 'edit';
            if (isset($this->m_properties['password'])) {
                if (!MetaAction::ValidateInput($this->m_properties, 'password', 6,
                $this->m_error, 'The user password was not filled in or was too short.',
                ACTION_EDIT_USER_ERR_NO_PASSWORD)) {
                    return false;
                }
                if (!MetaAction::ValidateInput($this->m_properties, 'passwordagain', 6,
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

        if (!$metaUser->defined) {
            if (User::UserNameExists($this->m_properties['uname'])) {
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
            if (!$user->create($this->m_data)) {
                $user->delete();
                $this->m_error = new PEAR_Error("There was an internal error creating the account (code 1).",
                ACTION_EDIT_USER_ERR_INTERNAL);
                return false;
            }
            $user->initLoginKey();
            setcookie("LoginUserKey", $user->getKeyId(), null, '/');
            $p_context->user = new MetaUser($user->getUserId());
        } else {
            $user = new User($metaUser->identifier);
            if (!$user->exists()) {
                $this->m_error = new PEAR_Error("There was an internal error updating the account (code 2).",
                ACTION_EDIT_USER_ERR_INTERNAL);
                return false;
            }
            foreach ($this->m_properties as $property=>$value) {
                if (!isset(MetaActionEdit_User::$m_fields[$property]['db_field'])) {
                    continue;
                }
                $dbProperty = MetaActionEdit_User::$m_fields[$property]['db_field'];
                if ($property != 'password' && $property != 'passwordagain') {
                    $user->setProperty($dbProperty, $value, false);
                } elseif ($property == 'password') {
                    $user->setPassword($this->m_properties['password'], false);
                }
            }
            if (!$user->commit()) {
                $this->m_error = new PEAR_Error("There was an internal error updating the account (code 4).",
                ACTION_EDIT_USER_ERR_INTERNAL);
                return false;
            }
        }

        foreach ($this->m_properties as $property=>$value) {
            $p_context->default_url->reset_parameter('f_user_'.$property);
            $p_context->url->reset_parameter('f_user_'.$property);
        }

        $this->m_error = ACTION_OK;
        return true;
    }
}

?>
