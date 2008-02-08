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

require_once($g_documentRoot.'/template_engine/classes/Exceptions.php');


/**
 * @package Campsite
 */
final class MetaDateTime
{
    private $m_value = null;

    private $m_year = null;

    private $m_month = null;

    private $m_monthDay = null;

    private $m_hour = null;

    private $m_minute = null;

    private $m_second = null;

    public function __construct($p_value)
    {
        $this->setValue($p_value);
    } // fn __construct

    public function setValue($p_value)
    {
        if (!MetaDateTime::IsValid($p_value)) {
            throw new InvalidValueException($p_value, MetaDateTime::GetTypeName());
        }
        $this->m_value = trim($p_value);
        $datetimeParts = preg_split('/[\s]+/', $this->m_value);
        $date = $datetimeParts[0];
        $time = isset($datetimeParts[1]) ? $datetimeParts[1] : '00:00:00';

        $dateObject = new MetaDate($date);
        $timeObject = new MetaTime($time);
        $this->m_year = $dateObject->getYear();
        $this->m_month = $dateObject->getMonth();
        $this->m_monthDay = $dateObject->getMonthDay();
        $this->m_hour = $timeObject->getHour();
        $this->m_minute = $timeObject->getMinute();
        $this->m_second = $timeObject->getSecond();
    }

    public function getValue()
    {
        return $this->m_value;
    }

    public function getYear()
    {
        return $this->m_year;
    }

    public function getMonth()
    {
        return $this->m_month;
    }

    public function getMonthDay()
    {
        return $this->m_monthDay;
    }

    public function getHour()
    {
        return $this->m_hour;
    }

    public function getMinute()
    {
        return $this->m_minute;
    }

    public function getSecond()
    {
        return $this->m_second;
    }

    public static function IsValid($p_value)
    {
        $p_value = trim($p_value);
        
        // now() is an value which have to be computed
        if (strtolower($p_value) == 'now()') {
            return true;   
        }
        
        $datetimeParts = preg_split('/[\s]+/', $p_value);
        $date = $datetimeParts[0];
        $time = isset($datetimeParts[1]) ? $datetimeParts[1] : '00:00:00';

        if (!MetaDate::IsValid($date)) {
            return false;
        }

        if (!MetaTime::IsValid($time)) {
            return false;
        }

        return true;
    }

    public static function GetTypeName()
    {
        return 'datetime';
    }

} // class MetaDateTime

?>