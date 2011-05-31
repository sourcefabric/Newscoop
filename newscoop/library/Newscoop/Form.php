<?php

/**
 * @author Mihai Nistor <mihai.nistor@gmail.com>
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop;

use
    Newscoop\Form\Decorator,
    Zend_Form;

class Form extends Zend_Form
{

    /**
     * Add some CSRF protection
     * CSRF equals Cross-site request forgery
     */
    protected function addSecurity()
    {
        $this->addElement('hash', 'csrf',
                array(
            'ignore' => true,
        ));
    }

    public function setAjax()
    {
        $decorator = new Decorator\Input();
        $this->setDecorators(array($decorator));
        /*
        $this->setElementDecorators(array(
            array('ViewHelper'),
            array('Label'),
            array('Errors'),
                //array('Submit'),
        ));
         */
    }

    public function setTable()
    {
        $this->setElementDecorators(array(
            'viewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));
        $this->setDecorators(array(
            'FormElements',
            array(array('data' => 'HtmlTag'), array('tag' => 'table')),
            'Form'
        ));
    }

    public function init()
    {
        $this->setMethod('post');
        $this->addSecurity();
    }

}
