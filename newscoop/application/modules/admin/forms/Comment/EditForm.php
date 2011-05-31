<?php

/**
 * @author Nistor Mihai
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Newscoop\Form;

class Admin_Form_Comment_EditForm extends Form
{

    public function init()
    {
        parent::init();
        $this->addElement('hidden', 'id',
                array(
            'label' => getGS('Comment id'),
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true)
            ),
        ));

        $this->addElement('text', 'subject',
                array(
            'label' => getGS('Subject'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('NotEmpty', true),
                array('stringLength', false, array(1, 140)),
            ),
            'errorMessages' => array(getGS('Subject is not $1 characters long',
                        '1-140')),
        ));

        $this->addElement('textarea', 'message',
                array(
            'label' => getGS('Comment'),
            'required' => false,
        ));

        $this->addElement('submit', 'submit',
                array(
            'label' => getGS('Save'),
        ));
//buttonBar
        $this->setAjax();
    }

}
