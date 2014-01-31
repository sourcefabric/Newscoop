<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaComment extends MetaDbObject
{
    /** @var array */
    private static $m_baseProperties = array(
        'id' => 'id',
        'recommended' => 'recommended',
    );

	private $m_realName = false;

    public function __construct($p_messageId = null)
    {
        $container = \Zend_Registry::get('container');
        $repository = $container->getService('em')->getRepository('Newscoop\Entity\Comment');
        if(is_null($p_messageId))
            $this->m_dbObject = $repository->getPrototype();
        else
            $this->m_dbObject = $repository->find($p_messageId);

        $this->m_properties = self::$m_baseProperties;

        $this->m_customProperties['level'] = 'getThreadDepth';
        $this->m_customProperties['identifier'] = 'getId';
        $this->m_customProperties['subject'] = 'getSubject';
        $this->m_customProperties['content'] = 'getMessage';
        $this->m_customProperties['content_real'] = 'getMessage';
        $this->m_customProperties['nickname'] = 'getCommenter';
        $this->m_customProperties['reader_email'] = 'getEmail';

        $this->m_customProperties['real_name'] = 'getRealName';
        $this->m_customProperties['anonymous_author'] = 'isAuthorAnonymous';
        $this->m_customProperties['submit_date'] = 'getSubmitDate';
        $this->m_customProperties['article'] = 'getArticle';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['user'] = 'getUser';
        $this->m_customProperties['parent'] = 'getParent';
        $this->m_customProperties['has_parent'] = 'hasParent';
        $this->m_customProperties['thread_level'] = 'threadLevel';

        $this->m_skipFilter = array('content_real');
    } // fn __construct

    protected function getThreadDepth()
    {
        return $this->m_dbObject->getThreadLevel();
    }

    protected function getId()
    {
        return $this->m_dbObject->getId();
    }

    protected function getRealName()
    {
    	if ($this->m_realName === false) {
            	$this->m_realName = $this->m_dbObject->getRealName();
    	}
    	return $this->m_realName;
    }

    protected function getCommenter()
    {
        return $this->m_dbObject->getCommenter()->getName();
    }

    protected function getEmail()
    {
        return $this->m_dbObject->getCommenter()->getEmail();
    }

    protected function isAuthorAnonymous()
    {
    	$this->getRealName();
    	return is_null($this->m_realName);
    }


    protected function getSubmitDate()
    {
        return $this->m_dbObject->getTimeCreated()->format('Y-m-d H:i:s');
    }

    protected function getSubject()
    {
        return $this->m_dbObject->getSubject();
    }

    protected function getMessage()
    {
        return $this->m_dbObject->getMessage();
    }

    protected function getArticle()
    {
        //TODO remove this when the composite key stuff is done.
        return new MetaArticle( $this->m_dbObject->getLanguage()->getId(), $this->m_dbObject->getArticleNumber() );
    	//return new MetaArticle( $this->m_dbObject->getLanguage()->getId(), $this->m_dbObject->getThread()->getId() );
    }


    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
                        . OF_OBJECT_STRING . ' comment';
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }

    /**
     * Get user
     *
     * @return MetaUser
     */
    public function getUser()
    {
        $user = $this->m_dbObject->getCommenter()->getUser();
        return new \MetaUser($user);
    }

    public function getParent()
    {
        $parent = $this->m_dbObject->getParent();
        if ($parent instanceof \Newscoop\Entity\Comment) {
            $parent = $this->m_dbObject->getParent()->getId();
        } else {
            $parent = 0;
        }
        return $parent;
    }

    public function hasParent()
    {
        if ($this->getParent() != 0) {
            return true;
        }
        return false;
    }

    public function threadLevel()
    {
        return $this->m_dbObject->getThreadLevel();
    }
}
