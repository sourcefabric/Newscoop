<?php

/**
 * @author Mihai Nistor <mihai.nistor@gmail.com>
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Form\Element;

/**
 * Hash implementation from the old cod
 */
use Zend_Form_Element_Xhtml,
    SecurityToken;

class OldHash extends Zend_Form_Element_Xhtml
{

    /**
     * Use formHidden view helper by default
     * @var string
     */
    public $helper = 'formHidden';
    /**
     * Actual hash used.
     *
     * @var mixed
     */
    protected $_hash;

    /**
     * Constructor
     *
     * Creates session namespace for CSRF token, and adds validator for CSRF
     * token.
     *
     * @param  string|array|Zend_Config $spec
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);

        $this->setAllowEmpty(false)
                ->setRequired(true)
                ->initCsrfValidator();
    }

    /**
     * Initialize CSRF validator
     *
     * Creates Session namespace, and initializes CSRF token in session.
     * Additionally, adds validator for validating CSRF token.
     *
     * @return Zend_Form_Element_Hash
     */
    public function initCsrfValidator()
    {
        $this->addValidator('Identical', true,
                array(SecurityToken::ValueParameter()));
        return $this;
    }

    /**
     * Retrieve CSRF token
     *
     * If no CSRF token currently exists, generates one.
     *
     * @return string
     */
    public function getHash()
    {
        if (null === $this->_hash) {
            $this->_hash = SecurityToken::ValueParameter();
        }
        return $this->_hash;
    }

    /**
     * Override getLabel() to always be empty
     *
     * @return null
     */
    public function getLabel()
    {
        return null;
    }

    /**
     * Render CSRF token in form
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        $this->setValue($this->getHash());
        return parent::render($view);
    }

}
