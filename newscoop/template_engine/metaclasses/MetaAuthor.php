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
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'email' => 'email'
    );

    /** @var array */
    private static $m_defaultCustomProperties = array(
        'name' => 'getName',
        'biography' => 'getBiography',
        'picture' => 'getImage',
        'type' => 'getType',
        'defined' => 'defined',
        'user' => 'getUser',
        'has_url' => 'hasUrl',
        'url' => 'getUrl',
        'posts_count' => 'getPostsCount',
    );

    /** @var AuthorBiography **/
    private $m_biography = null;

    /** @var MetaUser */
    private $user;

    /** @var string */
    private $url;

    public function __construct($p_idOrName = null, $p_type = null)
    {
        $this->m_properties = self::$m_baseProperties;
        $this->m_customProperties = self::$m_defaultCustomProperties;

        $this->m_dbObject = new Author($p_idOrName, $p_type);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Author();
        }
    }


    protected function getName($p_format = '%_FIRST_NAME %_LAST_NAME')
    {
        return $this->m_dbObject->getName($p_format);
    }


    protected function getBiography()
    {
        if (is_null($this->m_biography)) {
            $language = (int) CampTemplate::singleton()->context()->language->number;
            $this->m_biography = new MetaAuthorBiography($this->m_dbObject->getId(), $language);
        }
        return $this->m_biography;
    }


    protected function getImage()
    {
        return new MetaImage($this->m_dbObject->getImage());
    }


    protected function getType()
    {
        return $this->m_dbObject->getAuthorType()->getName();
    }

    /**
     * Get user related to author
     *
     * @return MetaUser
     */
    public function getUser()
    {
        if ($this->user === null) {
            $user = \Zend_Registry::get('container')->getService('user')->findByAuthor($this->m_dbObject->getId());
            $this->user = new \MetaUser($user);
        }

        return $this->user;
    }

    /**
     * Test if author has profile url
     *
     * @return bool
     */
    public function hasUrl()
    {
        return $this->getUrl() !== '';
    }

    /**
     * Get author profile url
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->url === null) {
            $user = $this->getUser();
            if (!$user->defined || !$user->is_active) {
                $this->url = '';
            } else {
                $this->url = \Zend_Registry::get('view')->url(array(
                    'username' => $user->uname,
                ), 'user');
            }
        }

        return $this->url;
    }

    /**
     * Get author posts count
     *
     * @return int
     */
    protected function getPostsCount()
    {
        if (!$this->m_dbObject->exists()) {
            return 0;
        }

        return \Zend_Registry::get('container')->getService('article.authors')
            ->getAuthorPostsCount($this->m_dbObject->getId());
    }
}
