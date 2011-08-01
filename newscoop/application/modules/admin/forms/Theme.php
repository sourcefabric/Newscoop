<?php

class Admin_Form_Theme extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'name', array(
            'label' => getGS('Theme name'),
            'required' => True,
        ));

        $reqVer = $this->addElement('text', 'required-version', array
        (
            'label'       => getGS( 'Required Newscoop version' ),
            'description' => getGS( 'or higher' ),
            'class'		  => 'small',
        ));

        $this->addElement( 'text', 'theme-version', array
        (
            'label'       => getGS( 'Theme version' ),
        	'class'		  => 'small',
        ));

        $this->setAttrib( "autocomplete", "off" );
        $this->setAction('')->setMethod( Zend_Form::METHOD_POST );
    }
}
