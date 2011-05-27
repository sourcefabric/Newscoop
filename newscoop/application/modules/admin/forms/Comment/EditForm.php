<?php

/**
 * @author Nistor Mihai
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
class Admin_Form_Comment_EditForm extends Zend_Form {

    public function init() {
        $this->setMethod('post');
        $this->addElement('text', 'comment', array(
            'label' => getGS('Comment'),
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true)
            )
        ));

        $this->addElement('text', 'subject', array(
            'label' => getGS('Subject'),
            'required' => true,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('NotEmpty', true),
                array('stringLength', false, array(1, 140)),
            ),
            'errorMessages' => array(getGS('Subject is not $1 characters long', '1-140')),

        ));

        $this->addElement('text', 'message', array(
            'label' => getGS('Message'),
            'required' => false,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
        ));
    }

}
