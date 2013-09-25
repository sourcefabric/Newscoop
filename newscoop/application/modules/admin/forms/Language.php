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
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->addElement('text', 'name', array(
            'required' => TRUE,
            'label' => $translator->trans('Name'),
        ));

        $this->addElement('text', 'native_name', array(
            'required' => TRUE,
            'label' => $translator->trans('Native Name', array(), 'languages'),
        ));

        $this->addElement('text', 'code_page', array(
            'required' => TRUE,
            'label' => $translator->trans('Code Page', array(), 'languages'),
        ));

        $this->addElement('text', 'code', array(
            'required' => TRUE,
            'label' => $translator->trans('Code'),
        ));

        $this->addElement('text', 'month1', array('label' => $translator->trans('January', array(), 'languages')));
        $this->addElement('text', 'month2', array('label' => $translator->trans('February', array(), 'languages')));
        $this->addElement('text', 'month3', array('label' => $translator->trans('March', array(), 'languages')));
        $this->addElement('text', 'month4', array('label' => $translator->trans('April', array(), 'languages')));
        $this->addElement('text', 'month5', array('label' => $translator->trans('May', array(), 'languages')));
        $this->addElement('text', 'month6', array('label' => $translator->trans('June', array(), 'languages')));
        $this->addElement('text', 'month7', array('label' => $translator->trans('July', array(), 'languages')));
        $this->addElement('text', 'month8', array('label' => $translator->trans('August', array(), 'languages')));
        $this->addElement('text', 'month9', array('label' => $translator->trans('September', array(), 'languages')));
        $this->addElement('text', 'month10', array('label' => $translator->trans('October', array(), 'languages')));
        $this->addElement('text', 'month11', array('label' => $translator->trans('November', array(), 'languages')));
        $this->addElement('text', 'month12', array('label' => $translator->trans('December', array(), 'languages')));

        $this->addDisplayGroup(array(
            'month1',
            'month2',
            'month3',
            'month4',
            'month5',
            'month6',
            'month7',
            'month8',
            'month9',
            'month10',
            'month11',
            'month12',
        ), 'months_longname', array(
            'legend' => $translator->trans('Edit month names', array(), 'languages'),
            'class' => 'toggle',
            'order' => 20,
        ));

        $this->addElement('text', 'short_month1', array('label' => $translator->trans('Jan', array(), 'languages')));
        $this->addElement('text', 'short_month2', array('label' => $translator->trans('Feb', array(), 'languages')));
        $this->addElement('text', 'short_month3', array('label' => $translator->trans('Mar', array(), 'languages')));
        $this->addElement('text', 'short_month4', array('label' => $translator->trans('Apr', array(), 'languages')));
        $this->addElement('text', 'short_month5', array('label' => $translator->trans('May', array(), 'languages')));
        $this->addElement('text', 'short_month6', array('label' => $translator->trans('Jun', array(), 'languages')));
        $this->addElement('text', 'short_month7', array('label' => $translator->trans('Jul', array(), 'languages')));
        $this->addElement('text', 'short_month8', array('label' => $translator->trans('Aug', array(), 'languages')));
        $this->addElement('text', 'short_month9', array('label' => $translator->trans('Sep', array(), 'languages')));
        $this->addElement('text', 'short_month10', array('label' => $translator->trans('Oct', array(), 'languages')));
        $this->addElement('text', 'short_month11', array('label' => $translator->trans('Nov', array(), 'languages')));
        $this->addElement('text', 'short_month12', array('label' => $translator->trans('Dec', array(), 'languages')));

        $this->addDisplayGroup(array(
            'short_month1',
            'short_month2',
            'short_month3',
            'short_month4',
            'short_month5',
            'short_month6',
            'short_month7',
            'short_month8',
            'short_month9',
            'short_month10',
            'short_month11',
            'short_month12',
        ), 'months_shortname', array(
            'legend' => $translator->trans('Edit short month names', array(), 'languages'),
            'class' => 'toggle',
            'order' => 40,
        ));

        $this->addElement('text', 'day1', array('label' => $translator->trans('Sunday', array(), 'languages')));
        $this->addElement('text', 'day2', array('label' => $translator->trans('Monday', array(), 'languages')));
        $this->addElement('text', 'day3', array('label' => $translator->trans('Tuesday', array(), 'languages')));
        $this->addElement('text', 'day4', array('label' => $translator->trans('Wednesday', array(), 'languages')));
        $this->addElement('text', 'day5', array('label' => $translator->trans('Thursday', array(), 'languages')));
        $this->addElement('text', 'day6', array('label' => $translator->trans('Friday', array(), 'languages')));
        $this->addElement('text', 'day7', array('label' => $translator->trans('Saturday', array(), 'languages')));

        $this->addDisplayGroup(array(
            'day1',
            'day2',
            'day3',
            'day4',
            'day5',
            'day6',
            'day7',
        ), 'days_name', array(
            'legend' => $translator->trans('Edit day names', array(), 'languages'),
            'class' => 'toggle',
            'order' => 60,
        ));

        $this->addElement('text', 'short_day1', array('label' => $translator->trans('Su', array(), 'languages')));
        $this->addElement('text', 'short_day2', array('label' => $translator->trans('Mo', array(), 'languages')));
        $this->addElement('text', 'short_day3', array('label' => $translator->trans('Tu', array(), 'languages')));
        $this->addElement('text', 'short_day4', array('label' => $translator->trans('We', array(), 'languages')));
        $this->addElement('text', 'short_day5', array('label' => $translator->trans('Th', array(), 'languages')));
        $this->addElement('text', 'short_day6', array('label' => $translator->trans('Fr', array(), 'languages')));
        $this->addElement('text', 'short_day7', array('label' => $translator->trans('Sa', array(), 'languages')));

        $this->addDisplayGroup(array(
            'short_day1',
            'short_day2',
            'short_day3',
            'short_day4',
            'short_day5',
            'short_day6',
            'short_day7',
        ), 'days_shortname', array(
            'legend' => $translator->trans('Edit short day names', array(), 'languages'),
            'class' => 'toggle',
            'order' => 70,
        ));

        $this->addElement('submit', 'submit', array(
            'label' => $translator->trans('Save'),
            'order' => 99,
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
            'month1' => $language->getMonth1(),
            'month2' => $language->getMonth2(),
            'month3' => $language->getMonth3(),
            'month4' => $language->getMonth4(),
            'month5' => $language->getMonth5(),
            'month6' => $language->getMonth6(),
            'month7' => $language->getMonth7(),
            'month8' => $language->getMonth8(),
            'month9' => $language->getMonth9(),
            'month10' => $language->getMonth10(),
            'month11' => $language->getMonth11(),
            'month12' => $language->getMonth12(),
            'short_month1' => $language->getShortMonth1(),
            'short_month2' => $language->getShortMonth2(),
            'short_month3' => $language->getShortMonth3(),
            'short_month4' => $language->getShortMonth4(),
            'short_month5' => $language->getShortMonth5(),
            'short_month6' => $language->getShortMonth6(),
            'short_month7' => $language->getShortMonth7(),
            'short_month8' => $language->getShortMonth8(),
            'short_month9' => $language->getShortMonth9(),
            'short_month10' => $language->getShortMonth10(),
            'short_month11' => $language->getShortMonth11(),
            'short_month12' => $language->getShortMonth12(),
            'day1' => $language->getDay1(),
            'day2' => $language->getDay2(),
            'day3' => $language->getDay3(),
            'day4' => $language->getDay4(),
            'day5' => $language->getDay5(),
            'day6' => $language->getDay6(),
            'day7' => $language->getDay7(),
            'short_day1' => $language->getShortDay1(),
            'short_day2' => $language->getShortDay2(),
            'short_day3' => $language->getShortDay3(),
            'short_day4' => $language->getShortDay4(),
            'short_day5' => $language->getShortDay5(),
            'short_day6' => $language->getShortDay6(),
            'short_day7' => $language->getShortDay7(),
        ));
    }
}
