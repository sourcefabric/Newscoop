<?php

/**
 * User form
 */
class Admin_Form_Comment extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->setMethod('post');
        /*$user = new Zend_Form_Element_Select('user');
        $user->setLabel($translator->trans('Username'))
            ->setRequired(false)
            ->setOrder(30);
        $this->addElement($user);
        */
        $user = new Zend_Form_Element_Text('user');
        $user->setLabel($translator->trans('User id', array(), 'comments'))
            ->setRequired(false)
            ->setOrder(30);
        $this->addElement($user);

        $this->addElement('text', 'name', array(
            'label' => $translator->trans('Name'),
            'required' => false,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(1, 128)),
            ),
            'errorMessages' => array($translator->trans('Value is not $1 characters long', array('$1' => '1-128'), 'comments')),
            'order' => 40,
        ));

        $this->addElement('text', 'email', array(
            'label' => $translator->trans('E-mail', array(), 'comments'),
            'required' => false,
            'order' => 50,
        ));

        $this->addElement('text', 'url', array(
            'label' => $translator->trans('Website', array(), 'comments'),
            'required' => false,
            'order' => 60,
        ));


        $this->addElement('text', 'parent', array(
            'label' => $translator->trans('Parent', array(), 'comments'),
            'required' => false,
            'order' => 61,
        ));

        $this->addElement('text', 'subject', array(
            'label' => $translator->trans('Subject'),
            'required' => false,
            'order' => 70,
        ));

        $this->addElement('textarea', 'message', array(
            'label' => $translator->trans('Subject'),
            'required' => false,
            'order' => 80,
        ));

        $this->addDisplayGroup(array(
            'user'
        ), 'commentsUser', array(
            'legend' => $translator->trans('Show comment user', array(), 'comments'),
            'class' => 'toggle',
            'order' => 35,
        ));

        $this->addDisplayGroup(array(
            'parent'
        ), 'comments_parent', array(
            'legend' => $translator->trans('Show parent comment', array(), 'comments'),
            'class' => 'toggle',
            'order' =>67,
        ));

        $this->addDisplayGroup(array(
            'name',
            'email',
            'url'
        ), 'commentsUser_info', array(
            'legend' => $translator->trans('Show comment user details', array(), 'comments'),
            'class' => 'toggle',
            'order' => 65,
        ));

        $this->addDisplayGroup(array(
            'subject',
            'message'
        ), 'comment_info', array(
            'legend' => $translator->trans('Show comment details', array(), 'comments'),
            'class' => 'toggle',
            'order' => 75,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'order' => 99,
        ));
    }

    /**
     * Set default values by entity
     *
     * @param Newscoop\Entity\Commenter $comment
     * @return void
     */
    public function setFromEntity(Comments $comment)
    {
        $this->setDefaults(array(
            'user' => $user->getUser(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'url'   => $user->getUrl()
        ));

    }

}
