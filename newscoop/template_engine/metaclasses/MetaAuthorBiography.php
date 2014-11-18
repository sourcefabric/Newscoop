<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/AuthorBiography.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaAuthorBiography extends MetaDbObject
{
    /** @var array */
    private static $m_baseProperties = array(
        'text' => 'biography',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
    );

    /** @var array */
    private static $m_defaultCustomProperties = array(
        'name' => 'getName',
        'defined' => 'defined'
    );

    /**
     * @param int
     * @param int
     * @param integer $p_language
     */
    public function __construct($p_id = NULL, $p_language = NULL)
    {
        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;

        $this->m_dbObject = new AuthorBiography($p_id, $p_language);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new AuthorBiography();
        }
    }

    protected function getName($p_format = '%_FIRST_NAME %_LAST_NAME')
    {
        return $this->m_dbObject->getName($p_format);
    }
}

?>
