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
final class MetaTime
{
    private $m_value = null;

    private $m_hour = null;

    private $m_minute = null;

    private $m_second = null;

	public function __construct($p_value)
    {
        $this->setValue($p_value);
    } // fn __construct

    public function setValue($p_value)
    {
        if (!MetaTime::IsValid($p_value)) {
            throw new InvalidValueException($p_value, MetaTime::GetTypeName());
        }
        $this->m_value = trim($p_value);
        $timeComponents = preg_split('/:/', $p_value);
        $this->m_hour = $timeComponents[0];
        $this->m_minute = $timeComponents[1];
        $this->m_second = isset($timeComponents[2]) ? $timeComponents[2] : 0;
    }

    public function getValue()
    {
        return $this->m_value;
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
        
        // curtime() is an value which have to be computed
        if (strtolower($p_value) == 'curtime()') {
            return true;   
        }
        
        if (preg_match('/^[\d]{1,2}:[\d]{1,2}(:[\d]{1,2})?$/', $p_value) == 0) {
            return false;
        }

        $timeComponents = preg_split('/:/', $p_value);
        $hour = $timeComponents[0];
        $minute = $timeComponents[1];
        $second = isset($timeComponents[2]) ? $timeComponents[2] : 0;

        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59
                || $second < 0 || $second > 59) {
            return false;
        }

        return true;
    }

    public static function GetTypeName()
    {
        return 'time';
    }

} // class MetaTime

?>