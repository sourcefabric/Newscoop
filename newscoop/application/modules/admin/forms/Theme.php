<?php

class Admin_Form_Theme extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        
        $this->addElement('text', 'name', array(
            'label' => $translator->trans('Theme name', array(), 'themes'),
            'required' => True,
        ));

        $this->addElement('text', 'required-version', array(
            'label' => $translator->trans('Required Newscoop version', array(), 'themes'),
            'description' => $translator->trans( 'or higher' , array(), 'themes'),
            'class' => 'small',
            'readonly' => True,
        ));

        $this->addElement('text', 'theme-version', array(
            'label' => $translator->trans( 'Theme version' , array(), 'themes'),
            'class' => 'small',
            'readonly' => True,
        ));

        $this->setAttrib('autocomplete', 'off');
        $this->setAction('')->setMethod(Zend_Form::METHOD_POST);
    }
}
