<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Country.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaUser extends MetaDbObject {

    public function __construct($p_userId = null)
    {
        $this->m_dbObject = new User($p_userId);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new User();
        }

        $this->m_properties['identifier'] = 'Id';
        $this->m_properties['name'] = 'Name';
        $this->m_properties['uname'] = 'UName';
        $this->m_properties['email'] = 'EMail';
        $this->m_properties['city'] = 'City';
        $this->m_properties['str_address'] = 'StrAddress';
        $this->m_properties['state'] = 'State';
        $this->m_properties['phone'] = 'Phone';
        $this->m_properties['fax'] = 'Fax';
        $this->m_properties['contact'] = 'Contact';
        $this->m_properties['second_phone'] = 'Phone2';
        $this->m_properties['postal_code'] = 'PostalCode';
        $this->m_properties['employer'] = 'Employer';
        $this->m_properties['position'] = 'Position';
        $this->m_properties['interests'] = 'Interests';
        $this->m_properties['how'] = 'How';
        $this->m_properties['languages'] = 'Languages';
        $this->m_properties['improvements'] = 'Improvements';
        $this->m_properties['field1'] = 'Field1';
        $this->m_properties['field2'] = 'Field2';
        $this->m_properties['field3'] = 'Field3';
        $this->m_properties['field4'] = 'Field4';
        $this->m_properties['field5'] = 'Field5';
        $this->m_properties['text1'] = 'Text1';
        $this->m_properties['text2'] = 'Text2';
        $this->m_properties['text3'] = 'Text3';
        $this->m_properties['title'] = 'Title';
        $this->m_properties['age'] = 'Age';
        $this->m_properties['country_code'] = 'CountryCode';
        $this->m_properties['gender'] = 'Gender';
        $this->m_properties['pref1'] = 'Pref1';
        $this->m_properties['pref2'] = 'Pref2';
        $this->m_properties['pref3'] = 'Pref3';
        $this->m_properties['pref4'] = 'Pref4';
        $this->m_properties['password_encrypted'] = 'Password';

        $this->m_customProperties['country'] = 'getCountry';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['logged_in'] =  'isLoggedIn';
        $this->m_customProperties['blocked_from_comments'] = 'isBlockedFromComments';
        $this->m_customProperties['subscription'] = 'getSubscription';
        $this->m_customProperties['is_admin'] = 'isAdmin';
    } // fn __construct


    /**
     * Returns the name of the country of the registered user.
     *
     * @return string
     */
    protected function getCountry()
    {
        $countryCode = $this->m_dbObject->getProperty('CountryCode');
        $smartyObj = CampTemplate::singleton();
        $contextObj = $smartyObj->get_template_vars('gimme');
        $country = new Country($countryCode, $contextObj->language->number);
        if (!$country->exists()) {
            return null;
        }
        return $country->getName();
    }


    /**
     * Request an user permission indicated by permission name
     *
     * @param string $p_permission permission name
     * @return boolean
     */
    public function has_permission($p_permission)
    {
        if ($this->m_dbObject->hasPermission($p_permission)) {
            return true;
        }
        return false;
    }


    /**
     * Returns true if the user had adminstration rights
     *
     * @return bool
     */
    protected function isAdmin() {
        return $this->m_dbObject->isAdmin();
    }


    /**
     * Returns true of the user was authenticated, false if not
     *
     * @return bool
     */
    protected function isLoggedIn()
    {
        $auth = Zend_Auth::getInstance();
        $context = CampTemplate::singleton()->context();

        if ($context->login_action->defined
            && $context->login_action->ok
            && $context->login_action->user_name == $this->uname
            && $this->uname != '') {
            return true;
        }

        return $auth->hasIdentity() && $this->m_dbObject->getKeyId() == CampRequest::GetVar('LoginUserKey');
    }


    protected function isBlockedFromComments() {
        return (int)Phorum_user::IsBanned($this->m_dbObject->getRealName(), $this->m_dbObject->getEmail());
    }


    protected function getSubscription() {
        $publicationId = CampTemplate::singleton()->context()->publication->identifier;
        $subscriptions = Subscription::GetSubscriptions($publicationId, $this->m_dbObject->getUserId());
        if (empty($subscriptions)) {
            return new MetaSubscription();
        }
        return new MetaSubscription($subscriptions[0]->getSubscriptionId());
    }

    /**
     * Check user token
     *
     * @param string
     * @return bool
     */
    public function checkToken($token)
    {
         $return = $token == $this->m_dbObject->getKeyId();
         if ($return) { // change key
             $this->m_dbObject->initLoginKey();
         }

         return $return;
    }
}
