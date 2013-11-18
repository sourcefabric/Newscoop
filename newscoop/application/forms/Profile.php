<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class Application_Form_Profile extends Zend_Form
{
    public function init()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('text', 'first_name', array(
            'label' => $translator->trans('First Name', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $this->addElement('text', 'last_name', array(
            'label' => $translator->trans('Last Name', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $this->addElement('text', 'username', array(
            'label' => $translator->trans('Username', array(), 'users'),
            'filters' => array('stringTrim'),
            'required' => true,
        ));

        $this->addElement('password', 'password', array(
            'label' => $translator->trans('Password'),
        ));

        $this->addElement('file', 'image', array(
            'label' => $translator->trans('Profile image', array(), 'users'),
        ));

        $profile = new Zend_Form_SubForm();

        $profile->addElement('radio', 'gender', array(
            'label' => $translator->trans('Gender', array(), 'users'),
            'multioptions' => array(
                'male' => 'Male',
                'female' => 'Female',
            ),
        ));

        $profile->addElement('textarea', 'bio', array(
            'label' => $translator->trans('About me', array(), 'users'),
            'filters' => array('stringTrim'),
            'cols' => 60,
            'rows' => 4,
        ));

        $profile->addElement('text', 'birth_date', array(
            'label' => $translator->trans('Date of birth', array(), 'users'),
            'class' => 'date',
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'organisation', array(
            'label' => $translator->trans('Organisation', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'website', array(
            'label' => $translator->trans('Website', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'twitter', array(
            'label' => $translator->trans('Twitter', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'facebook', array(
            'label' => $translator->trans('Facebook', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'google', array(
            'label' => $translator->trans('Google+', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'geolocation', array(
            'label' => $translator->trans('Geolocation', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('checkbox', 'first_name_public', array(
            'label' => $translator->trans('First Name Public', array(), 'users'),
        ));

        $profile->addElement('checkbox', 'last_name_public', array(
            'label' => $translator->trans('Last Name Public', array(), 'users'),
        ));

        $profile->addElement('checkbox', 'email_public', array(
            'label' => $translator->trans('Allow sending emails', array(), 'users'),
        ));

        $this->addSubForm($profile, 'attributes');

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save profile', array(), 'users'),
            'ignore' => true,
        ));
    }

    public function setDefaultsFromEntity(User $user)
    {
        $defaults = array(
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'username' => $user->getUsername(),
            'attributes' => array(),
        );

        $profile = $this->getSubForm('attributes');
        foreach ($profile as $field) {
            $defaults['attributes'][$field->getName()] = (string) $user->getAttribute($field->getName());
        }

        $this->setDefaults($defaults);
    }

    /**
     * Get maximum file size in bytes
     *
     * @return int
     */
    public function getMaxFileSize()
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $maxFileSize = $preferencesService->MaxProfileImageFileSize;
        if (!$maxFileSize) {
            $maxFileSize = ini_get('upload_max_filesize');
        }

        return camp_convert_bytes($maxFileSize);
    }
}
