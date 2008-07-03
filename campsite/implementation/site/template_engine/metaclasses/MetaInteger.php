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
final class MetaInteger
{
    private $m_value = null;

	public function __construct($p_value)
    {
        $this->setValue($p_value);
    } // fn __construct

    public function setValue($p_value)
    {
        if (!MetaInteger::IsValid($p_value)) {
            throw new InvalidValueException($p_value, MetaInteger::GetTypeName());
        }
        $this->m_value = preg_replace('[\s]', '', $p_value);
    }

    public function getValue()
    {
        return $this->m_value;
    }

    public static function IsValid($p_value)
    {
        $p_value = preg_replace('[\s]', '', $p_value);
        if (preg_match('/^([-+]?[\s]*[\d]+)$/', $p_value) > 0) {
            return true;
        }
        return false;
    }

    public static function GetTypeName()
    {
        return 'integer';
    }

} // class MetaInteger

?>