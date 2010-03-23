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
final class MetaOrder
{
    private $m_value = null;

	public function __construct($p_value)
    {
        $this->setValue($p_value);
    } // fn __construct

    public function setValue($p_value)
    {
        if (!MetaOrder::IsValid($p_value)) {
            throw new InvalidValueException($p_value, MetaOrder::GetTypeName());
        }
        $this->m_value = trim($p_value);
    }

    public function getValue()
    {
        return $this->m_value;
    }

    public static function IsValid($p_value)
    {
        $p_value = trim($p_value);
        return strtolower($p_value) == 'asc' || strtolower($p_value) == 'desc';
    }

    public static function GetTypeName()
    {
        return 'order';
    }

} // class MetaOrder

?>