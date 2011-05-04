<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 * User form
 */
abstract class Admin_Form_User extends Zend_Form
{
    public function init()
    {
        $this->addElement('hash', 'csrf');

        $this->addElement('text', 'username', array(
            'label' => getGS('Account name'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(3, 32)),
            ),
            'order' => 10,
        ));

        $this->addElement('password', 'password', array(
            'label' => getGS('Password'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(5, 32)),
            ),
            'order' => 20,
        ));

        $this->addElement('password', 'password_confirm', array( // checked with isValid
            'label' => getGS('Confirm password'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array(new Zend_Validate_Callback(function($value, $context) {
                    return empty($context['password']) || $context['password'] == $value;
                }), false),
            ),
            'errorMessages' => array(getGS('Confirmation failed')),
            'order' => 30,
        ));

        $this->addElement('text', 'name', array(
            'label' => getGS('Name'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(1, 128)),
            ),
            'order' => 40,
        ));

        $this->addElement('text', 'email', array(
            'label' => getGS('E-mail'),
            'required' => true,
            'order' => 50,
        ));

        $this->addElement('text', 'phone', array(
            'label' => getGS('Phone'),
            'order' => 60,
        ));

        $this->addElement('select', 'title', array(
            'label' => getGS('Title'),
            'multioptions' => array(
                getGS('Mr.') => getGS('Mr.'),
                getGS('Mrs.') => getGS('Mrs.'),
                getGS('Ms.') => getGS('Ms.'),
                getGS('Dr.') => getGS('Dr.'),
            ),
        ));

        $this->addElement('radio', 'gender', array(
            'label' => getGS('Gender'),
            'multioptions' => array(
                'M' => getGS('Male'),
                'F' => getGS('Female'),
            ),
        ));

        $this->addElement('select', 'age', array(
            'label' => getGS('Age'),
            'multioptions' => array(
                '0-17' => getGS('under 18'),
                '18-24' => getGS('18-24'),
                '25-39' => getGS('25-39'),
                '40-49' => getGS('40-49'),
                '50-65' => getGS('50-65'),
                '65-' => getGS('65 or over'),
            ),
        ));

        $this->addElement('text', 'city', array(
            'label' => getGS('City'),
        ));

        $this->addElement('text', 'street_address', array(
            'label' => getGS('Street address'),
            'validators' => array(
                array('stringLength', false, array(0, 255)),
            ),
        ));

        $this->addElement('text', 'postal_code', array(
            'label' => getGS('Postal code'),
            'size' => 10,
        ));

        $this->addElement('text', 'state', array(
            'label' => getGS('State'),
        ));

        $this->addElement('select', 'country', array(
            'label' => getGS('Country'),
        ));

        $this->addElement('text', 'fax', array(
            'label' => getGS('Fax'),
        ));

        $this->addElement('text', 'contact_person', array(
            'label' => getGS('Contact person'),
        ));

        $this->addElement('text', 'phone_second', array(
            'label' => getGS('Second phone'),
        ));

        $this->addElement('text', 'employer', array(
            'label' => getGS('Employer'),
        ));

        $this->addElement('select', 'employer_type', array(
            'multioptions' => array(
                'Corporate' => getGS('Corporate'),
				'NGO' => getGS('Non-Governmental Organisation'),
				'Government Agency' => getGS('Government Agency'),
                'Academic' => getGS('Academic'),
                'Media' => getGS('Media'),
                'Other' => getGS('Other'),
            ),
            'label' => getGS('Employer Type'),
        ));

        $this->addElement('text', 'position', array(
            'label' => getGS('Position'),
        ));

        $this->addDisplayGroup(array(
            'title',
            'gender',
            'age',
            'city',
            'street_address',
            'postal_code',
            'state',
            'country',
            'fax',
            'contact_person',
            'phone_second',
            'employer',
            'employer_type',
            'position',
        ), 'personal_info', array(
            'legend' => getGS('Show more user details'),
            'class' => 'toggle',
            'order' => 70,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'order' => 99,
        ));
    }

    /**
     * Set default values by entity
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function setDefaultsFromEntity(User $user)
    {
        $this->setDefaults(array(
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'title' => $user->getTitle(),
            'gender' => $user->getGender(),
            'age' => $user->getAge(),
            'city' => $user->getCity(),
            'street_address' => $user->getStreetAddress(),
            'postal_code' => $user->getPostalCode(),
            'state' => $user->getState(),
            'country' => $user->getCountry(),
            'fax' => $user->getFax(),
            'contact_person' => $user->getContactPerson(),
            'phone_second' => $user->getPhoneSecond(),
            'employer' => $user->getEmployer(),
            'employer_type' => $user->getEmployerType(),
            'position' => $user->getPosition(),
        ));

        // can't change on edit
        $this->getElement('username')->setAttrib('readonly', TRUE);

        // make password change optional
        $this->getElement('password')->setRequired(false);
        $this->getElement('password_confirm')->setRequired(!empty($_POST['password']));
        $this->addDisplayGroup(array(
            'password',
            'password_confirm',
        ), 'password_change', array(
            'legend' => getGS('Change password'),
            'class' => 'toggle',
            'order' => 62,
        ));
    }
}
