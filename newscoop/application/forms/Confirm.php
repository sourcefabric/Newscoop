<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_Confirm extends Zend_Form
{
    public function init()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->addElement('text', 'first_name', array(
            'label' => $translator->trans('First Name', array(), 'users').'*:',
            'required' => true,
            'filters' => array('stringTrim'),
        ));
        $this->getElement('first_name')->setOrder(1);

        $this->addElement('text', 'last_name', array(
            'label' => $translator->trans('Last Name', array(), 'users').'*:',
            'required' => true,
            'filters' => array('stringTrim'),
        ));
        $this->getElement('last_name')->setOrder(2);

        $this->addElement('text', 'username', array(
            'label' => $translator->trans('Username', array(), 'users').'*:',
            'required' => true,
            'filters' => array('stringTrim'),
        ));
        $this->getElement('username')->setOrder(3);

        $this->addElement('file', 'image', array(
            'label' => $translator->trans('Profile image', array(), 'users'),
            'maxfilesize' => $this->getMaxFileSize(),
            'validators' => array(
                'isImage',
            ),
        ));
        $this->getElement('username')->setOrder(4);

        $this->addElement('password', 'password', array(
            'label' => $translator->trans('Password').'*:',
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array(
                array('stringLength', false, array(6, 80)),
            ),
        ));
        $this->getElement('password')->setOrder(5);

        $form = $this;
        $this->addElement('password', 'password_confirm', array(
            'label' => $translator->trans('Password Confirmation', array(), 'users').'*:',
            'required' => true,
            'filters' => array('stringTrim'),
            'validators' => array(
                new Zend_Validate_Callback(function ($value, $context) {
                    return $value == $context['password'];
                }),
            ),
            'errorMessages' => array($translator->trans("Password confirmation does not match your password.", array(), 'users')),
        ));
        $this->getElement('password_confirm')->setOrder(6);

        $this->addElement('checkbox', 'terms_of_use', array(
            'label' => $translator->trans('Accepting terms of use', array(), 'users'),
            'required' => true,
            'validators' => array(
                array('greaterThan', true, array('min' => 0)),
            ),
            'errorMessages' => array($translator->trans('You must accept the terms of use.', array(), 'users')),
        ));

        $this->getElement('terms_of_use')->setOrder(7);

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Login'),
            'ignore' => true,
        ));
        $this->getElement('submit')->setOrder(8);
    }

    /**
     * Get max file size
     *
     * @return int
     */
    public function getMaxFileSize()
    {
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $maxFileSize = $preferencesService->MaxProfileImageFileSize ?: ini_get('upload_max_filesize');

        return camp_convert_bytes($maxFileSize);
    }
}
