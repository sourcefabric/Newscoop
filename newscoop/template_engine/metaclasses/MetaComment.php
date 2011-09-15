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
final class MetaComment extends MetaDbObject {

	private $m_realName = false;

    public function __construct($p_messageId = null)
    {
        global $controller;
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
        if(is_null($p_messageId))
            $this->m_dbObject = $repository->getPrototype();
        else
            $this->m_dbObject = $repository->find($p_messageId);

        $this->m_properties['id'] = 'id';
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

        $this->m_skipFilter = array('content_real');
    } // fn __construct

    /**
     * @return string
     */
    public function __toString()
    {
        $date = $this->getSubmitDate();
        $subject = $this->getSubject();
        $message = $this->getMessage();

        //want to get the name from this article here and return it with everything else..
        //$article = $this->getArticle();

        $html = '<p class="comment_date">%s</p>
                <p class="comment_subject">%s</p>
                <p class="comment_message">%s</p>';

        $langid = $this->m_dbObject->getLanguage()->getId();
        $article_num = $this->m_dbObject->getThread()->getId();

        return sprintf($html, $date, $subject, $message);
    }

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
    	return new MetaArticle( $this->m_dbObject->getLanguage()->getId(), $this->m_dbObject->getThread()->getId() );
    }


    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
                        . OF_OBJECT_STRING . ' comment';
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }

} // class MetaComment

?>