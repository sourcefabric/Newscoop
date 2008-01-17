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
        $this->m_customProperties['article_index'] = 'getArticleIndex';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['imageurl'] = 'getImageUrl';
        $this->m_customProperties['thumbnailurl'] = 'getThumbnailUrl';
    } // fn __construct


    public function getYear()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['year'];
    }


    public function getMonth()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['mon'];
    }


    public function getWeekDay()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['wday'];
    }


    public function getMonthDay()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['mday'];
    }


    public function getYearDay()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['yday'];
    }


    public function getHour()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['hours'];
    }


    public function getMinute()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['minutes'];
    }


    public function getSecond()
    {
        $timestamp = strtotime($this->m_dbObject->getProperty('Date'));
        $date_time = getdate($timestamp);
        return $date_time['seconds'];
    }


    public function getImageUrl()
    {
        $url = $this->m_dbObject->getImageUrl(); 
        return $url;   
    }


    public function getThumbnailUrl()
    {
        $url = $this->m_dbObject->getThumbnailUrl(); 
        return $url;   
    }


    /**
     * Returns the index of the current image inside the article.
     * If the image doesn't belong to the article returns null.
     *
     * @return int
     */
    public function getArticleIndex() {
        return CampTemplate::singleton()->context()->article->image_index;
    }

} // class MetaSection

?>