<?php
/**
 * @package Newscoop
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAuthor.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Newscoop
 */
final class MetaArticleAuthor extends MetaDbObject
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
        'type' => 'getType'
    );

    /** @var AuthorType */
    private $m_type = NULL;


    /**
     * Class constructor
     *
     * @param ArticleAuthor
     */
    public function __construct(ArticleAuthor $p_dbObject = null)
    {
        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;

        if (!is_null($p_dbObject)) {
            $this->m_dbObject = new Author($p_dbObject->getAuthorId());
            $this->m_type = $p_dbObject->getType();
        } else {
            $this->m_dbObject = new Author();
        }
    }

    /**
     * @param string
     * @return string
     */
    protected function getName($p_format = '%_FIRST_NAME %_LAST_NAME')
    {
    	return $this->m_dbObject->getName($p_format);
    }

    /**
     * @return string
     */
    protected function getType()
    {
        $authorType = NULL;
        if (!is_null($this->m_type) && $this->m_type->exists()) {
            $authorType = $this->m_type->getName();
        }
        return $authorType;
    }
}

?>