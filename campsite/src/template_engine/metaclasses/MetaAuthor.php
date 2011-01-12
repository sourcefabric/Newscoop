<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Author.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaAuthor extends MetaDbObject
{
    /** @var array */
    private static $m_baseProperties = array(
        'identifier' => 'id',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'email' => 'email'
    );

    /** @var array */
    private static $m_defaultCustomProperties = array(
        'name' => 'getName',
        'type' => 'getType',
        'defined' => 'defined'
    );

    public function __construct($p_idOrName = NULL, $p_type = NULL)
    {
        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;

        $this->m_dbObject = new Author($p_idOrName, $p_type);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Author();
        }
    } // fn __construct


    protected function getName($p_format = '%_FIRST_NAME %_LAST_NAME')
    {
    	return $this->m_dbObject->getName($p_format);
    }


    public static function getType()
    {
        return $this->m_dbObject->getAuthorType();
    }
}

?>