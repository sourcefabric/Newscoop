<?php
/**
 * @package Newscoop
 * @subpackage Languages
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Language;

/**
 * Language form
 */
class Admin_Form_Language extends Zend_Form
{
    public function init()
    {
        $this->addElement('text', 'name', array(
            'required' => TRUE,
            'label' => getGS('Name'),
        ));

        $this->addElement('text', 'native_name', array(
            'required' => TRUE,
            'label' => getGS('Native Name'),
        ));

        $this->addElement('text', 'code_page', array(
            'required' => TRUE,
            'label' => getGS('Code Page'),
        ));

        $this->addElement('text', 'code', array(
            'required' => TRUE,
            'label' => getGS('Code'),
        ));

        $this->addElement('submit', 'submit', array(
            'label' => getGS('Save'),
        ));
    }

    /**
     * Set default values by entity
     *
     * @param Newscoop\Entity\Language $language
     * @return void
     */
    public function setDefaultsFromEntity(Language $language)
    {
        $this->setDefaults(array(
            'name' => $language->getName(),
            'native_name' => $language->getNativeName(),
            'code_page' => $language->getCodePage(),
            'code' => $language->getCode(),
        ));
    }
}
