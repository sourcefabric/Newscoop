<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Api_Form_Profile extends Application_Form_Profile
{
    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->addElement('text', 'first_name');
        $this->addElement('text', 'last_name');
        $this->addElement('text', 'customer_id');
        $this->addElement('text', 'birth_date');
        $this->addElement('text', 'facebook');
        $this->addElement('text', 'google');
        $this->addElement('text', 'twitter');
        $this->addElement('text', 'website');
        $this->addElement('text', 'bio');
        $this->addElement('text', 'email_public');
        $this->addElement('text', 'profile_image_data');
    }
}
