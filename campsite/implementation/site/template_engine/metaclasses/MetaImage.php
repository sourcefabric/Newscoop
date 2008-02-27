<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/Image.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaImage extends MetaDbObject {

    private function InitProperties()
    {
        if (!is_null($this->m_properties)) {
            return;
        }
        $this->m_properties['number'] = 'Id';
        $this->m_properties['photographer'] = 'Photographer';
        $this->m_properties['place'] = 'Place';
        $this->m_properties['description'] = 'Description';
        $this->m_properties['date'] = 'Date';
    }


    public function __construct($p_imageId = null)
    {
        $this->m_dbObject = new Image($p_imageId);

        $this->InitProperties();
        $this->m_customProperties['year'] = 'getYear';
        $this->m_customProperties['mon'] = 'getMonth';
        $this->m_customProperties['wday'] = 'getWeekDay';
        $this->m_customProperties['mday'] = 'getMonthDay';
        $this->m_customProperties['yday'] = 'getYearDay';
        $this->m_customProperties['hour'] = 'getHour';
        $this->m_customProperties['min'] = 'getMinute';
        $this->m_customProperties['sec'] = 'getSecond';
        $this->m_customProperties['mon_name'] = 'getMonthName';
        $this->m_customProperties['wday_name'] = 'getWeekDayName';
        $this->m_customProperties['article_index'] = 'getArticleIndex';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    protected function getYear()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['year'];
    }


    protected function getMonth()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['mon'];
    }


    protected function getWeekDay()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['wday'];
    }


    protected function getMonthDay()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['mday'];
    }


    protected function getYearDay()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['yday'];
    }


    protected function getHour()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['hours'];
    }


    protected function getMinute()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['minutes'];
    }


    protected function getSecond()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['seconds'];
    }


    protected function getMonthName() {
        $dateTime = new MetaDateTime($this->m_dbObject->getProperty('Date'));
        return $dateTime->getMonthName();
    }


    protected function getWeekDayName() {
        $dateTime = new MetaDateTime($this->m_dbObject->getProperty('Date'));
        return $dateTime->getWeekDayName();
    }


    /**
     * Returns the index of the current image inside the article.
     * If the image doesn't belong to the article returns null.
     *
     * @return int
     */
    protected function getArticleIndex() {
        return CampTemplate::singleton()->context()->article->image_index;
    }

} // class MetaSection

?>