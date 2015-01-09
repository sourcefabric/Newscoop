<?php
/**
 * @package Newscoop
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

/**
 */
class Admin_Form_Geolocation extends Zend_Form
{
    public function init()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->setAttrib('id', 'edit-form');

        $this->addElement('text', 'geolocation', array(
            'label' => $translator->trans('Geolocation', array(), 'users'),
            'filters' => array('stringTrim'),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'ignore' => true,
            'order' => 99,
        ));
    }

    /**
     * Set form defaults
     *
     * @param  Newscoop\Entity\User   $user
     * @return Admin_Form_Geolocation
     */
    public function setDefaultsFromEntity(User $user)
    {
        return $this->setDefaults(array('geolocation' => $user->getAttribute('geolocation')));
    }
}
