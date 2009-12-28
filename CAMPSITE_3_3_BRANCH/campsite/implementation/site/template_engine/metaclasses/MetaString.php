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
final class MetaString
{
    private $m_value = null;

	public function __construct($p_value)
    {
        $this->setValue($p_value);
    } // fn __construct

    public function setValue($p_value)
    {
        if (!MetaString::IsValid($p_value)) {
            throw new InvalidValueException($p_value, MetaString::GetTypeName());
        }
        $this->m_value = ''.$p_value;
    }

    public function getValue()
    {
        return $this->m_value;
    }

    public static function IsValid($p_value)
    {
        return is_scalar($p_value);
    }

    public static function GetTypeName()
    {
        return 'string';
    }

} // class MetaString

?>