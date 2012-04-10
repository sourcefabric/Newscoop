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
        $this->addElement('hash', 'csrf');

        $this->addElement('text', 'first_name', array(
            'label' => getGS('First Name'),
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('text', 'last_name', array(
            'label' => getGS('Last Name'),
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('text', 'email', array(
            'label' => getGS('Email'),
            'required' => TRUE,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                'emailAddress',
            ),
        ));

        $this->addElement('text', 'username', array(
            'label' => getGS('Username'),
            'required' => TRUE,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(5, 80)),
            ),
        ));

        $this->addElement('password', 'password', array(
            'label' => getGS('Password'),
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
        ));

        $this->addElement('checkbox', 'status', array(
            'label' => getGS('User account is active'),
        ));

        $this->addElement('checkbox', 'is_admin', array(
            'label' => getGS('Allow user access to login to site backend'),
        ));

        $this->addElement('checkbox', 'is_public', array(
            'label' => getGS("Allow user's profile to be publicly displayed"),
        ));

        $this->addElement('multiCheckbox', 'user_type', array(
            'label' => getGS('User Type'),
        ));

        $this->addElement('select', 'author', array(
            'label' => getGS('Author'),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'ignore' => true,
            'id' => 'save_button'
        ));
    }

    /**
     * Set form defaults
     *
     * @param Newscoop\Entity\User $user
     * @return Admin_Form_User
     */
    public function setDefaultsFromEntity(User $user)
    {
        $types = array();
        foreach ($user->getUserTypes() as $type) {
            $types[] = $type->getId();
        }

        try {
            $user->getAuthorId();
        } catch (\Doctrine\ORM\EntityNotFoundException $e) { // deleted author
            $user->setAuthor(null);
        }

        return $this->setDefaults(array(
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'status' => $user->isActive(),
            'is_admin' => $user->isAdmin(),
            'is_public' => $user->isPublic(),
            'user_type' => $types,
            'author' => $user->getAuthorId(),
        ));
    }
}
