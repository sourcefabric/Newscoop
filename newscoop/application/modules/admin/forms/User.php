<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class Admin_Form_User extends Zend_Form
{
    /**
     */
    public function init()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->addElement('hash', 'csrf');

        $this->addElement('text', 'first_name', array(
            'label' => $translator->trans('First Name', array(), 'users'),
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('text', 'last_name', array(
            'label' => $translator->trans('Last Name', array(), 'users'),
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('text', 'email', array(
            'label' => $translator->trans('Email', array(), 'users'),
            'required' => TRUE,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                'emailAddress',
            ),
        ));

        $this->addElement('text', 'username', array(
            'label' => $translator->trans('Username', array(), 'users'),
            'required' => TRUE,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(5, 80)),
            ),
        ));

        $this->addElement('password', 'password', array(
            'label' => $translator->trans('Password'),
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
        ));

        $this->addElement('checkbox', 'status', array(
            'label' => $translator->trans('User account is active', array(), 'users'),
        ));

        $this->addElement('checkbox', 'is_admin', array(
            'label' => $translator->trans('Allow user access to login to site backend', array(), 'users'),
        ));

        $this->addElement('checkbox', 'is_public', array(
            'label' => $translator->trans("Allow users profile to be publicly displayed", array(), 'users'),
        ));

        $this->addElement('checkbox', 'is_verified', array(
            'label' => $translator->trans('User account is verified', array(), 'users'),
        ));

        $this->addElement('checkbox', 'is_featured', array(
            'label' => $translator->trans('User account is highlighted as "featured account"', array(), 'users'),
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
            'style' => 'width: 80%',
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

        $profile->addElement('text', 'custom_attr_1', array(
            'filters' => array('stringTrim'),
            'placeholder' => $translator->trans('Custom attribute...', array(), 'users'),
        ));

        $profile->addElement('text', 'custom_attr_2', array(
            'filters' => array('stringTrim'),
            'placeholder' => $translator->trans('Custom attribute...', array(), 'users'),
        ));

        $profile->addElement('text', 'google', array(
            'label' => $translator->trans('Google+', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $profile->addElement('text', 'location', array(
            'label' => $translator->trans('Location', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $this->addSubForm($profile, 'attributes');

        $this->addElement('multiCheckbox', 'user_type', array(
            'label' => $translator->trans('User Type', array(), 'users'),
        ));

        $this->addElement('select', 'author', array(
            'label' => $translator->trans('Author'),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'ignore' => true,
            'id' => 'save_button'
        ));

        $this->addDisplayGroup(array(
            'csrf',
            'first_name',
            'last_name',
            'email',
            'username',
            'password',
            'status',
            'is_admin',
            'is_public',
            'is_verified',
            'is_featured',
            'user_type',
            'author'
        ), 'adminSettings');

        $this->addDisplayGroup(array(
            'gender',
            'bio',
            'birth_date',
            'organisation',
            'website',
            'twitter',
            'facebook',
            'google',
            'submit'
        ), 'attributes');
    }

    /**
     * Set form defaults
     *
     * @param  Newscoop\Entity\User $user
     * @return Admin_Form_User
     */
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

        $types = array();
        foreach ($user->getUserTypes() as $type) {
            $types[] = $type->getId();
        }

        try {
            $user->getAuthorId();
        } catch (\Doctrine\ORM\EntityNotFoundException $e) { // deleted author
            $user->setAuthor(null);
        }

        $settings = array(
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'status' => $user->isActive(),
            'is_admin' => $user->isAdmin(),
            'is_public' => $user->isPublic(),
            'is_verified' => $user->getAttribute('is_verified'),
            'is_featured' => $user->getAttribute('is_featured'),
            'user_type' => $types,
            'author' => $user->getAuthorId(),
        );

        return $this->setDefaults(array_merge($settings, $defaults));
    }
}
