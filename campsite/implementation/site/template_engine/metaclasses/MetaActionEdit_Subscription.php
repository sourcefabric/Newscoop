<?php

define('ACTION_EDIT_SUBSCRIPTION_ERR_', 'action_edit_subscription_err_');
define('ACTION_EDIT_SUBSCRIPTION_ERR_INTERNAL', 'action_edit_subscription_err_internal');


class MetaActionEdit_Subscription extends MetaAction
{
    private $m_user;


    /**
     * Reads the input parameters and sets up the subscription edit action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'edit_subscription';
        $this->m_properties = array();

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
        if (!$metaUser->logged_in) {
            $this->m_error = new PEAR_Error('You must be logged in to create or edit your subscription.');
            return false;
        }

        if (isset($this->m_properties['password'])
        && $this->m_properties['password'] != $this->m_properties['passwordagain']) {
            $this->m_error = new PEAR_Error("The password and password confirmation do not match.",
            ACTION_EDIT_USER_ERR_PASSWORD_MISMATCH);
            return false;
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
            if (!$user->create($this->m_data)
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
                if (!isset(MetaActionEdit_User::$m_fields[$property]['db_field'])) {
                    continue;
                }
                $dbProperty = MetaActionEdit_User::$m_fields[$property]['db_field'];
                if ($property != 'password' && $property != 'passwordagain') {
                    $user->setProperty($dbProperty, $value, false);
                    if ($property == 'email') {
                        $phorumUser->setProperty('email', $value, false);
                    }
                } elseif ($property == 'password') {
                    $user->setPassword($this->m_properties['password'], false);
                    $phorumUser->setPassword($this->m_properties['password'], false);
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