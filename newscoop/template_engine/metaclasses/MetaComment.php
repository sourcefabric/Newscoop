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
	private $m_nickName = false;
	private $m_readerEmail = false;
	private $m_textSubject = false;
	private $m_textContent = false;


    public function __construct($p_messageId = null)
    {
        $this->m_dbObject = new Phorum_message($p_messageId);
        if (!$this->m_dbObject->exists() && !is_null($p_messageId)) {
            $this->m_dbObject = new Phorum_message();
        }

        $this->m_properties['identifier'] = 'message_id';
        $this->m_properties['content_real'] = 'body';
        $this->m_properties['level'] = 'thread_depth';

        $this->m_customProperties['real_name'] = 'getRealName';
        $this->m_customProperties['anonymous_author'] = 'isAuthorAnonymous';
        $this->m_customProperties['submit_date'] = 'getSubmitDate';
        $this->m_customProperties['article'] = 'getArticle';
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['nickname'] = 'getNickname';
        $this->m_customProperties['reader_email'] = 'getReaderEmail';
        $this->m_customProperties['subject'] = 'getSubject';
        $this->m_customProperties['content'] = 'getContent';
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

    protected function getNickname()
    {
        if ($this->m_nickName === false)
        {
            $prop_nickname = $this->m_dbObject->getProperty('author');
            if (!$prop_nickname) {$prop_nickname = "";}
    
            $prop_nickname = str_replace("&", "&amp;", $prop_nickname);
            $prop_nickname = str_replace("<", "&lt;", $prop_nickname);
            $prop_nickname = str_replace(">", "&gt;", $prop_nickname);

            $this->m_nickName = $prop_nickname;
        }

    	return $this->m_nickName;
    }

    protected function getReaderEmail()
    {
        if ($this->m_readerEmail === false)
        {
            $prop_readeremail = $this->m_dbObject->getProperty('email');
            if (!$prop_readeremail) {$prop_readeremail = "";}
    
            $prop_readeremail = str_replace("&", "&amp;", $prop_readeremail);
            $prop_readeremail = str_replace("<", "&lt;", $prop_readeremail);
            $prop_readeremail = str_replace(">", "&gt;", $prop_readeremail);

            $this->m_readerEmail = $prop_readeremail;
        }

    	return $this->m_readerEmail;
    }

    protected function getSubject()
    {
        if ($this->m_textSubject === false)
        {
            $prop_subject = $this->m_dbObject->getProperty('subject');
            if (!$prop_subject) {$prop_subject = "";}
    
            $prop_subject = str_replace("&", "&amp;", $prop_subject);
            $prop_subject = str_replace("<", "&lt;", $prop_subject);
            $prop_subject = str_replace(">", "&gt;", $prop_subject);

            $this->m_textSubject = $prop_subject;
        }

    	return $this->m_textSubject;
    }

    protected function getContent()
    {
        if ($this->m_textContent === false)
        {
            $prop_content = $this->m_dbObject->getProperty('body');
            if (!$prop_content) {$prop_content = "";}
    
            $prop_content = str_replace("&", "&amp;", $prop_content);
            $prop_content = str_replace("<", "&lt;", $prop_content);
            $prop_content = str_replace(">", "&gt;", $prop_content);

            $this->m_textContent = $prop_content;
        }

    	return $this->m_textContent;
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
