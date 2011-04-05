<?php

use Newscoop\Entity\User;

/**
 * User form
 */
abstract class Admin_Form_BaseUser extends Zend_Form
{
    public function addUserDetails()
    {
        // user details
        $this->addElement('select', 'title', array(
            'multioptions' => array(
                'Mr.' => getGS('Mr.'),
                'Mrs.' => getGS('Mrs.'),
                'Ms.' => getGS('Ms.'),
                'Dr.' => getGS('Dr.'),
            ),
            'label' => getGS('Title'),
        ));

        $this->addElement('radio', 'gender', array(
            'multioptions' => array(
                'M' => getGS('Male'),
                'F' => getGS('Female'),
            ),
            'label' => getGS('Gender'),
        ));

        $this->addElement('select', 'age', array(
            'multioptions' => array(
                '0-17' => getGS('under 18'),
                '18-24' => getGS('18-24'),
                '25-39' => getGS('25-39'),
                '40-49' => getGS('40-49'),
                '50-65' => getGS('50-65'),
                '65-' => getGS('65 or over'),
            ),
            'label' => getGS('Age'),
        ));

        $this->addElement('text', 'city', array(
            'label' => getGS('City'),
        ));

        $this->addElement('text', 'street_address', array(
            'label' => getGS('Street address'),
        ));

        $this->addElement('text', 'postal_code', array(
            'label' => getGS('Postal code'),
        ));

        $this->addElement('text', 'state', array(
            'label' => getGS('State'),
        ));

        $this->addElement('select', 'country', array(
            'multioptions' => $this->getCountries(),
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
    }

    /**
     * Get countries
     *
     * @return array
     */
    private function getCountries()
    {
        $countries = array();
        foreach (Country::GetCountries(1) as $country) {
            $countries[$country->getCode()] = $country->getName();
        }

        return $countries;
    }
}
