<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/User.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/classes/Country.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaUser extends MetaDbObject {

    private function InitProperties()
    {
        if (!is_null($this->m_properties)) {
            return;
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
        $this->m_properties['password_encrypted'] = 'Password';
    }


    public function __construct($p_userId = null)
    {
        $this->m_dbObject = new User($p_userId);

        $this->InitProperties();
        $this->m_customProperties['country'] = 'getCountry';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['logged_in'] =  'isLoggedIn';
        $this->m_customProperties['blocked_from_comments'] = 'isBlockedFromComments';
        $this->m_customProperties['subscription'] = 'getSubscription';
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
        $contextObj = $smartyObj->get_template_vars('campsite');
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
     * Returns true of the user was authenticated, false if not
     *
     * @return bool
     */
    protected function isLoggedIn()
    {
        $context = CampTemplate::singleton()->context();
        return (($context->login_action->defined
        && $context->login_action->ok
        && $context->login_action->user_name == $this->uname
        && $this->uname != '')
        || ($this->m_dbObject->getUserId() == CampRequest::GetVar('LoginUserId')
        && $this->m_dbObject->getKeyId() == CampRequest::GetVar('LoginUserKey')
        && $this->m_dbObject->getUserId() > 0
        && $this->m_dbObject->getKeyId() > 0));
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
} // class MetaUser

?>