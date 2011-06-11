<?php

/**
 * @author Nistor Mihai
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Newscoop\Form;

class Admin_Form_Comment_ReplyForm extends Form
{

    public function init()
    {
        /** Call the parent Newscoop\Form init method for basic initialization */
        parent::init();

        /** Id of the comment */
        $this->addElement('hidden', 'id',
                array(
            'label' => getGS('Comment id'),
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('Int', true)
            ),
        ));

        /**
         * Subject input
         *      has a basic validatorfor the string length
         */
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

        /** Message input */
        $this->addElement('textarea', 'message',
                array(
            'label' => getGS('Comment'),
            'required' => false,
        ));

        /** Cancel button */
        $this->addElement('reset', 'cancel',
                array(
            'label' => getGS('Cancel'),
            'class' => 'button edit-cancel'
        ));

        /** Reply Button */
        $this->addElement('button', 'reply',
                array(
            'label' => getGS('Reply'),
            'class' => 'button edit-reply'
        ));

        /** Save Button */
        $this->addElement('submit', 'save',
                array(
            'label' => getGS('Update comment'),
            'class' => 'save-button-small update'
        ));

        /** Group buttons together */
        $this->addDisplayGroup(array(
            'cancel',
            'reply',
            'save',
                ), 'commenter',
                array(
            'class' => 'buttonBar'
        ));
    }

}
