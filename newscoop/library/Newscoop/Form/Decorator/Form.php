<?php

/**
 * @author Mihai Nistor <mihai.nistor@gmail.com>
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Form\Decorator;

use Zend_From,
    Zend_Form_Decorator_Form;

class Form extends Zend_Form_Decorator_Form
{

    /**
     * Override the getOptions from the parent
     *
     * setting the action to the template action
     * @return array
     */
    public function getOptions()
    {

        parent::getOptions();
        $name = '';
        if (null !== ($element = $this->getElement())) {
            $name = $element->getFullyQualifiedName();
            $this->_options['name'] = $name;
        }

        if (isset($this->_options['action'])) {
            $this->_options['action'] = "{{" . $name . ".action}}";
        }
        return $this->_options;
    }

    /**
     * Render a form
     *
     * Replaces $content entirely from currently set element.
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $form = $this->getElement();
        $view = $form->getView();
        if (null === $view) {
            return $content;
        }

        $helper = $this->getHelper();
        $attribs = $this->getOptions();
        $name = '';
        return $view->$helper($name, $attribs, $content);
    }

}
