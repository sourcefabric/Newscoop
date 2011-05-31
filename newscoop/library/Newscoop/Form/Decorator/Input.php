<?php

/**
 * @author Mihai Nistor <mihai.nistor@gmail.com>
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
/**
 * Simple decorator for an ajax input
 */

namespace Newscoop\Form\Decorator;

use Zend_From,
    Zend_Form_Decorator_Abstract;

class Input extends Zend_Form_Decorator_Abstract
{

    protected $_format = '<label for="%s">%s</label>
                          <input id="%s" name="%s" type="text" value="{{%s}}"/>';

    public function render($content)
    {
        $element = $this->getElement();
        echo "gete:";
        echo get_class($element);
        echo "instance: ",var_dump(is_a($element,'Zend_Form'));
        /*
        $name = htmlentities($element->getFullyQualifiedName());
        $label = htmlentities($element->getLabel());
        $id = htmlentities($element->getId());
        $value = htmlentities($element->getValue());

        $markup = sprintf($this->_format, $id, $label, $id, $name, $name);
        return $markup;
         *
         */
        return $content;
    }

}
