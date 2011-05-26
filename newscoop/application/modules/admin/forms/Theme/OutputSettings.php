<?php 

class Admin_Form_Theme_OutputSettings extends Zend_Form
{
    public function init()
    {
        $this
            ->setAttrib( "autocomplete", "off" )
            ->setAction( '' )
            ->setMethod( Zend_Form::METHOD_POST )
            ->addElement( 'select', 'front-page', array
            (
                'label'       => getGS( 'Front page template' ),
            	'required'    => true,
            ))
            ->addElement( 'select', 'section-page', array
            (
    			'label'       => getGS( 'Section page template' ),
            	'required'    => true,
            ))
            ->addElement( 'select', 'article-page', array
            (
    			'label'       => getGS( 'Article page template' ),
            	'required'    => true,
            ))
            ->addElement( 'hidden', 'output', array
            (
            	'required'    => true,
            ));
    }
}