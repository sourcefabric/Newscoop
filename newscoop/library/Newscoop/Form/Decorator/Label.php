<?php
namespace Newscoop\Form\Decorator;

use Zend_Form_Decorator_Label;


class Label extends Zend_Form_Decorator_Label
{
    /**
     * Element types that doesn't show a label
     * @var array
     */
    public $_hiddenLabels = array(
        'Zend_Form_Element_Hash',
        'Zend_Form_Element_Hidden',
        'Zend_Form_Element_Button',
        'Zend_Form_Element_Reset',
        'Zend_Form_Element_Submit',
    );
    /**
     * Render a label
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {

        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $label     = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $tagClass  = $this->getTagClass();
        $id        = $this->getId();
        $class     = $this->getClass();
        $options   = $this->getOptions();


        if (empty($label) && empty($tag)) {
            return $content;
        }

        if(in_array(get_class($element),$this->_hiddenLabels)) {
            $label = '';
        }
        elseif (!empty($label)) {
            $options['class'] = $class;
            $label = $view->formLabel($element->getFullyQualifiedName(), trim($label), $options);
        } else {
            $label = '&#160;';
        }

        if (null !== $tag) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
            if (null !== $this->_tagClass) {
                $decorator->setOptions(array('tag'   => $tag,
                                             'id'    => $id . '-label',
                                             'class' => $tagClass));
            } else {
                $decorator->setOptions(array('tag'   => $tag,
                                             'id'    => $id . '-label'));
            }

            $label = $decorator->render($label);
        }

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }
    }
}
