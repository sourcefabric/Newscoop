<?php
/**
 * Commenter form
 */
class Admin_Form_Commenter extends Zend_Form
{
    public function init()
    {
        /*
        $user = new Zend_Form_Element_Select('user');
        $user->setLabel($translator->trans('Username'))
            ->setRequired(true)
            ->setOrder(30);
        */
        $translator = \Zend_Registry::get('container')->getService('translator');
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

        $this->addDisplayGroup(array(
            'name',
            'email',
            'url'
        ), 'commenter_info', array(
            'legend' => $translator->trans('Show commenter details', array(), 'comments'),
            'class' => 'toggle',
            'order' => 70,
        ));

        $this->addDisplayGroup(array(
            'user',
        ), 'commenter', array(
            'legend' => $translator->trans('Show commenter', array(), 'comments'),
            'class' => 'toggle',
            'order' => 20,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'order' => 99,
        ));
    }

    /**
     * Set default values by entity
     *
     * @param $commenter
     * @return void
     */
    public function setFromEntity($commenter)
    {
        $this->setDefaults(array(
            'user' => $commenter->getUserId(),
            'name' => $commenter->getName(),
            'email' => $commenter->getEmail(),
            'url'   => $commenter->getUrl()
        ));

    }

}
