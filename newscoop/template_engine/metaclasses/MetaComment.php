<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Phorum_message.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');
require_once($GLOBALS['g_campsiteDir'].'/include/pear/Date.php');

/**
 * @package Campsite
 */
final class MetaComment extends MetaDbObject {
	
	private $m_realName = false;


    public function __construct($p_messageId = null)
    {
        $this->m_dbObject = new Phorum_message($p_messageId);
        if (!$this->m_dbObject->exists() && !is_null($p_messageId)) {
            $this->m_dbObject = new Phorum_message();
        }

        $this->m_properties['identifier'] = 'message_id';
        $this->m_properties['nickname'] = 'author';
        $this->m_properties['reader_email'] = 'email';
        $this->m_properties['subject'] = 'subject';
        $this->m_properties['content'] = 'body';
        $this->m_properties['level'] = 'thread_depth';

        $this->m_customProperties['real_name'] = 'getRealName';
        $this->m_customProperties['anonymous_author'] = 'isAuthorAnonymous';
        $this->m_customProperties['submit_date'] = 'getSubmitDate';
        $this->m_customProperties['article'] = 'getArticle';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    protected function getRealName()
    {
    	if ($this->m_realName === false) {
        	$userId = $this->m_dbObject->getUserId();
            if ($userId > 0) {
                $userObj = new User($userId);
                $this->m_realName = $userObj->getRealName();
            } else {
            	$this->m_realName = null;
            }
    	}
    	return $this->m_realName;
    }

    
    protected function isAuthorAnonymous()
    {
    	$this->getRealName();
    	return is_null($this->m_realName);
    }


    protected function getSubmitDate()
    {
        $date = new Date($this->m_dbObject->getCreationDate());
        return $date->getDate();
    }


    protected function getArticle()
    {
    	$article = ArticleComment::GetArticleOf($this->m_dbObject->getMessageId());
    	if (is_null($article)) {
    		return new MetaArticle();
    	}
    	return new MetaArticle($article->getLanguageId(), $article->getArticleNumber());
    }


    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
                        . OF_OBJECT_STRING . ' comment';
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }

} // class MetaComment

?>