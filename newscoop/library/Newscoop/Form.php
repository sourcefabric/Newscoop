<?php

/**
 * @author Mihai Nistor <mihai.nistor@gmail.com>
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop;

use Newscoop\Form\Decorator,
    Newscoop\Form\Element\OldHash,
    SecurityToken,
    Zend_Form,
    Zend_Form_Decorator_Form;

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
            'salt' => 'unique'
        ));
    }

    /**
     * Add some security
     * S
     */
    protected function addOldSecurity()
    {
        $security = new OldHash('csrf');
        $this->addElement($security);
    }

    public function setSimpleDecorate()
    {


        $htmlTag = new Decorator\HtmlTag(array('tag' => 'div'));
        $label = new Decorator\Label();
        $this->setDisplayGroupDecorators(array(
            array('FormElements'),
            $htmlTag
        ));

        $this->setDecorators(array(
            'FormElements',
            'Form'
        ));
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            $label,
        ));
        return $this;
    }

    /**
     * Set the form for ajax template with the subObject namespace
     *
     * @param array $subObject
     */
    public function setTemplate($subObject = array())
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            switch (get_class($element)) {
                case 'Zend_Form_Element_Text':
                case 'Zend_Form_Element_Textarea':
                case 'Zend_Form_Element_Hidden':
                    $prefix = count($subObject) ? implode('.', $subObject) . '.' : '';
                    $element->setValue('{{' . $prefix . $element->getName() . '}}');
                    break;
            }
        }
        return $this;
    }

    public function init()
    {
        $this->setMethod('post');
        $this->addOldSecurity();
    }

}
