<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_PasswordRestore extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'email', array(
            'label' => 'E-mail',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Restore password',
            'ignore' => true,
        ));
    }
}
