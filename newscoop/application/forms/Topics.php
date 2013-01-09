<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Application_Form_Topics extends Zend_Form
{
    public function init()
    {
        $this->addElement('multiCheckbox', 'topics', array(
            'required' => false,
        ));

        $this->addElement('multiCheckbox', 'selected', array(
            'required' => false,
        ));

        $this->addElement('hidden', 'languageId', array(
            'required' => true,
        ));
    }
}
