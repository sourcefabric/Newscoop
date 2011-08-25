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
    public function init()
    {
        $this->addElement('hash', 'csrf');

        $this->addElement('multiCheckbox', 'user_type', array(
            'label' => getGS('User Type'),
        ));

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

        $this->addElement('text', 'username', array(
            'label' => getGS('Account Name'),
            'required' => TRUE,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(5, 80)),
            ),
        ));

        $this->addElement('text', 'email', array(
            'label' => getGS('E-mail Address'),
            'required' => TRUE,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                'emailAddress',
            ),
        ));

        $this->addElement('text', 'password', array(
            'label' => getGS('Password'),
            'required' => TRUE,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
        ));

        $this->addElement('checkbox', 'status', array(
            'label' => getGS('Active'),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Create account'),
            'ignore' => TRUE,
        ));
    }
}
