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
final class MetaDate
{
    private $m_value = null;

    private $m_year = null;

    private $m_month = null;

    private $m_monthDay = null;

	public function __construct($p_value)
    {
        $this->setValue($p_value);
    } // fn __construct

    public function setValue($p_value)
    {
        if (!MetaDate::IsValid($p_value)) {
            throw new InvalidValueException($p_value, MetaDate::GetTypeName());
        }
        $this->m_value = trim($p_value);
        list($this->m_year, $this->m_month, $this->m_monthDay) = preg_split('/-/', $this->m_value);
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

    public static function IsValid($p_value)
    {
        $p_value = trim($p_value);
        if (preg_match('/^[\d]{4,4}-[\d]{1,2}-[\d]{1,2}$/', $p_value) == 0) {
            return false;
        }

        list($year, $month, $monthDay) = preg_split('/-/', $p_value);

        if ($month < 1 || $month > 12) {
            return false;
        }

        $lastMonthDay = strftime('%d', mktime(0, 0, 0, $month+1, 0, $year));
        if ($monthDay < 1 || $monthDay > $lastMonthDay) {
            return false;
        }
        return true;
    }

    public static function GetTypeName()
    {
        return 'date';
    }

} // class MetaDate

?>