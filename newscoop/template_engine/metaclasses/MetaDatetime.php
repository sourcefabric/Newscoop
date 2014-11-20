<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/Exceptions.php');


/**
 * @package Campsite
 */
final class MetaDatetime
{
    private $m_value = null;

    private $m_date = null;

    private $m_time = null;

    public function __construct($p_value)
    {
    	$p_value = trim($p_value);
    	if (!empty($p_value)) {
            $this->setValue($p_value);
    	}
    } // fn __construct

    /**
     * @param string $p_value
     */
    public function setValue($p_value)
    {
        if (!self::IsValid($p_value)) {
            throw new InvalidValueException($p_value, self::GetTypeName());
        }
        $this->m_value = trim($p_value);
        $datetimeParts = preg_split('/[\s]+/', $this->m_value);
        $date = $datetimeParts[0];
        $time = isset($datetimeParts[1]) ? $datetimeParts[1] : '00:00:00';

        $this->m_date = new MetaDate($date);
        $this->m_time = new MetaTime($time);
    }

    public function getValue()
    {
        return $this->m_value;
    }

    public function getYear()
    {
        return $this->m_date->getYear();
    }

    public function getMonth()
    {
        return $this->m_date->getMonth();
    }

    public function getMonthName() {
        return $this->m_date->getMonthName();
    }

    public function getMonthDay()
    {
        return $this->m_date->getMonthDay();
    }

    public function getWeekDay() {
        return $this->m_date->getWeekDay();
    }

    public function getWeekDayName() {
        return $this->m_date->getWeekDayName();
    }

    public function getHour()
    {
        return $this->m_time->getHour();
    }

    public function getMinute()
    {
        return $this->m_time->getMinute();
    }

    public function getSecond()
    {
        return $this->m_time->getSecond();
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

} // class MetaDatetime

?>