<?php

class Admin_Form_Theme extends Zend_Form
{
    public function init()
    {
        $this->setAttrib( "autocomplete", "off" );
        $reqVer = $this->addElement('text', 'required-version', array
        (
            'label'       => getGS( 'Required Newscoop version' ),
            'description' => getGS( 'or higher' ),
            'class'		  => 'small',
        	'required'    => true,
            'readonly'	  => true
        ));

        $this->addElement( 'text', 'theme-version', array
        (
            'label'       => getGS( 'Theme version' ),
        	'class'		  => 'small',
        	'required'    => true,
        	'readonly'	  => true
        ));
        $this->setAction('')->setMethod( Zend_Form::METHOD_POST );
    }
}