<?php
/**
 * Ban Commenter form
 */
class Admin_Form_Ban extends Zend_Form
{
    /**
     * Where is keept the input of the name
     *
     * @var Zend_Form_Element_Checkbox
     */
    private $name;

    /**
     * Where is keept the input of the email
     *
     * @var Zend_Form_Element_Checkbox
     */
    private $email;

    /**
     * Where is keept the input of the ip
     *
     * @var Zend_Form_Element_Checkbox
     */
    private $ip;

    public function init()
    {
        $this->name = new Zend_Form_Element_Checkbox('name');
        $this->name->setLabel(getGS('Name').":")
                   ->setOrder(10)
                   ->setRequired(false);

        $this->addElement($this->name);

        $this->email = new Zend_Form_Element_Checkbox('email');
        $this->email->setLabel(getGS('Email').":")
                   ->setOrder(20)
                   ->setRequired(false);

        $this->addElement($this->email);

        $this->ip = new Zend_Form_Element_Checkbox('ip');
        $this->ip->setLabel(getGS('Ip').":")
                   ->setOrder(30)
                   ->setRequired(false);

        $this->addElement($this->ip);
        /*
        $this->addDisplayGroup(array(
            'name',
            'email',
            'ip'
        ), 'commenter_ban', array(
            'legend' => getGS('Show commenter details'),
            'class' => 'toggle',
            'order' => 70,
        ));
        */
        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
            'order' => 99,
        ));

    }

    /**
     * Set values
     *
     * @param $commenter
     * @param $values
     */
    public function setValues($p_commenter, $p_values)
    {
        $this->name->setLabel(getGS('Name').":".$p_commenter->getName())
                   ->setChecked($p_values['name']);

        $this->email->setLabel(getGS('Email').":".$p_commenter->getEmail())
                    ->setChecked($p_values['email']);

        $this->ip->setLabel(getGS('Ip').":".$p_commenter->getIp())
                 ->setChecked($p_values['ip']);
    }

}
