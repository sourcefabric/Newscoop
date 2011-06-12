<?php
namespace Newscoop\Form\Decorator;

use Zend_Form_Decorator_HtmlTag;

class HtmlTag extends Zend_Form_Decorator_HtmlTag
{
    public function getOptions()
    {
        $options = parent::getOptions();
        if (null !== ($element = $this->getElement())) {
            $attribs = $element->getAttribs();
            $options = array_merge($attribs, $options);
            $this->setOptions($options);
        }
        return $options;
    }
}
