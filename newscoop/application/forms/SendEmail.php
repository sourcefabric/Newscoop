<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_SendEmail extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'subject', array(
            'label' => 'Subject',
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
        ));

        $this->addElement('textarea', 'message', array(
            'label' => 'Message',
            'required' => true,
            'columns' => 60,
            'rows' => 5,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Send email',
            'ignore' => true,
        ));
    }
}
