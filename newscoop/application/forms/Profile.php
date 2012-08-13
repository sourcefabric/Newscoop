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
            'maxfilesize' => $this->getMaxFileSize(),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Save profile',
            'ignore' => true,
        ));
    }

    public function setDefaultsFromEntity(User $user)
    {
        $this->setDefaults(array(
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'username' => $user->getUsername(),
            'attributes' => $user->getAttributes(),
        ));
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
