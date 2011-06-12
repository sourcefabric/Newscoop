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
        $this->setAction( '' )
            ->setMethod( Zend_Form::METHOD_POST )
            ->addElement( 'file', 'browse', array
            (
                'label'       => getGS( 'Browse for the theme' ),
            	'required'    => true,
            ))
            ->addElement( 'submit', 'submit-button', array
            (
                'label'		=> 'Upload'
            ) );
    }
}