<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
class Admin_Form_Theme_Upload extends Zend_Form
{
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        
        $this->setAction( '' )
            ->setMethod( Zend_Form::METHOD_POST )
            ->addElement( 'file', 'browse', array
            (
                'label'       => $translator->trans( 'Browse for the theme' , array(), 'themes'),
            	'required'    => true,
            ))
            ->addElement( 'submit', 'submit-button', array
            (
                'label'		=> 'Upload'
            ) );
    }
}