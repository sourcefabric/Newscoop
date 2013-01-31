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
        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('text', 'first_name', array(
            'label' => 'First Name',
            'filters' => array('stringTrim'),
        ));

        $this->addElement('text', 'last_name', array(
            'label' => 'Last Name',
            'filters' => array('stringTrim'),
        ));

        $this->addElement('text', 'username', array(
            'label' => 'Username',
            'filters' => array('stringTrim'),
            'required' => true,
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
        ));

        $this->addElement('file', 'image', array(
            'label' => 'Profile image',
        ));

        $profile = new Zend_Form_SubForm();

        $profile->addElement('radio', 'gender', array(
            'label' => 'Gender',
            'multioptions' => array(
                'male' => 'Male',
                'female' => 'Female',
            ),
        ));

        $profile->addElement('textarea', 'bio', array(
            'label' => 'About me',
            'filters' => array('stringTrim'),
            'cols' => 60,
            'rows' => 4,
        ));

        $profile->addElement('text', 'birth_date', array(
            'label' => 'Date of birth',
            'class' => 'date',
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'organisation', array(
            'label' => 'Organisation',
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'website', array(
            'label' => 'Website',
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'twitter', array(
            'label' => 'Twitter',
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'facebook', array(
            'label' => 'Facebook',
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'google', array(
            'label' => 'Google+',
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'geolocation', array(
            'label' => 'Geolocation',
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('checkbox', 'first_name_public', array(
            'label' => 'First Name Public',
        ));

        $profile->addElement('checkbox', 'last_name_public', array(
            'label' => 'Last Name Public',
        ));

        $profile->addElement('checkbox', 'email_public', array(
            'label' => 'Allow sending emails',
        ));

        $this->addSubForm($profile, 'attributes');

        $this->addElement('submit', 'submit', array(
            'label' => 'Save profile',
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
        $maxFileSize = SystemPref::Get("MaxProfileImageFileSize");
        if (!$maxFileSize) {
            $maxFileSize = ini_get('upload_max_filesize');
        }

        return camp_convert_bytes($maxFileSize);
    }
}
