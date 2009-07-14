<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaLanguage extends MetaDbObject {

    public function __construct($p_languageId = null)
    {
		$this->m_dbObject = new Language($p_languageId);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Language();
        }

        $this->m_properties['name'] = 'OrigName';
        $this->m_properties['number'] = 'Id';
        $this->m_properties['english_name'] = 'Name';
        $this->m_properties['code'] = 'Code';
	$this->m_properties['month1'] = 'Month1';
	$this->m_properties['month2'] = 'Month2';
	$this->m_properties['month3'] = 'Month3';
	$this->m_properties['month4'] = 'Month4';
	$this->m_properties['month5'] = 'Month5';
	$this->m_properties['month6'] = 'Month6';
	$this->m_properties['month7'] = 'Month7';
	$this->m_properties['month8'] = 'Month8';
	$this->m_properties['month9'] = 'Month9';
	$this->m_properties['month10'] = 'Month10';
	$this->m_properties['month11'] = 'Month11';
	$this->m_properties['month12'] = 'Month12';
	$this->m_properties['short_month1'] = 'ShortMonth1';
	$this->m_properties['short_month2'] = 'ShortMonth2';
	$this->m_properties['short_month3'] = 'ShortMonth3';
	$this->m_properties['short_month4'] = 'ShortMonth4';
	$this->m_properties['short_month5'] = 'ShortMonth5';
	$this->m_properties['short_month6'] = 'ShortMonth6';
	$this->m_properties['short_month7'] = 'ShortMonth7';
	$this->m_properties['short_month8'] = 'ShortMonth8';
	$this->m_properties['short_month9'] = 'ShortMonth9';
	$this->m_properties['short_month10'] = 'ShortMonth10';
	$this->m_properties['short_month11'] = 'ShortMonth11';
	$this->m_properties['short_month12'] = 'ShortMonth12';
	$this->m_properties['weekday1'] = 'WDay1';
	$this->m_properties['weekday2'] = 'WDay2';
	$this->m_properties['weekday3'] = 'WDay3';
	$this->m_properties['weekday4'] = 'WDay4';
	$this->m_properties['weekday5'] = 'WDay5';
	$this->m_properties['weekday6'] = 'WDay6';
	$this->m_properties['weekday7'] = 'WDay7';
	$this->m_properties['short_weekday1'] = 'ShortWDay1';
	$this->m_properties['short_weekday2'] = 'ShortWDay2';
	$this->m_properties['short_weekday3'] = 'ShortWDay3';
	$this->m_properties['short_weekday4'] = 'ShortWDay4';
	$this->m_properties['short_weekday5'] = 'ShortWDay5';
	$this->m_properties['short_weekday6'] = 'ShortWDay6';
	$this->m_properties['short_weekday7'] = 'ShortWDay7';

	$this->m_customProperties['defined'] = 'defined';
    } // fn __construct

} // class MetaLanguage

?>