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
        $user->setLabel(getGS('Username'))
            ->setRequired(true)
            ->setOrder(30);
        */
        $user = new Zend_Form_Element_Text('user');
        $user->setLabel(getGS('User id'))
            ->setRequired(false)
            ->setOrder(30);
        $this->addElement($user);

        $this->addElement('text', 'name', array(
            'label' => getGS('Name'),
            'required' => false,
            'filters' => array(
                'stringTrim',
            ),
            'validators' => array(
                array('stringLength', false, array(1, 128)),
            ),
            'errorMessages' => array(getGS('Value is not $1 characters long', '1-128')),
            'order' => 40,
        ));

        $this->addElement('text', 'email', array(
            'label' => getGS('E-mail'),
            'required' => false,
            'order' => 50,
        ));

        $this->addElement('text', 'url', array(
            'label' => getGS('Website'),
            'required' => false,
            'order' => 60,
        ));

        $this->addDisplayGroup(array(
            'name',
            'email',
            'url'
        ), 'commenter_info', array(
            'legend' => getGS('Show commenter details'),
            'class' => 'toggle',
            'order' => 70,
        ));

        $this->addDisplayGroup(array(
            'user',
        ), 'commenter', array(
            'legend' => getGS('Show commenter'),
            'class' => 'toggle',
            'order' => 20,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
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
