<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <sebastian.goebel@web.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

final class MetaBlog extends MetaDbObject {

    private function InitProperties()
    {
        if (!is_null($this->m_properties)) {
            return;
        }
        $this->m_properties['identifier'] = 'blog_id';
        $this->m_properties['language_id'] = 'fk_language_id';
        $this->m_properties['user_id'] = 'fk_user_id';
        $this->m_properties['title'] = 'title';
        $this->m_properties['name'] = 'title';
        $this->m_properties['date'] = 'date';
        $this->m_properties['info'] = 'info';
        $this->m_properties['images'] = 'images';
        $this->m_properties['admin_remark'] = 'admin_remark';
        $this->m_properties['request_text'] = 'request_text';
        $this->m_properties['status'] = 'status';
        $this->m_properties['admin_status'] = 'admin_status';
        $this->m_properties['entries_online'] = 'entries_online';
        $this->m_properties['entries_offline'] = 'entries_offline';
        $this->m_properties['comments_online'] = 'comments_online';
        $this->m_properties['comments_offline'] = 'comments_offline';
        $this->m_properties['feature'] = 'feature';
    }


    public function __construct($p_blog_id=null)
    {
        $this->m_dbObject = new Blog($p_blog_id);

        $this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
        $this->m_customProperties['language'] = 'getLanguage';
        $this->m_customProperties['user'] = 'getUser';
        $this->m_customProperties['entries'] = 'getEntriesCount';
        $this->m_customProperties['captcha_enabled'] = 'getCaptchaEnabled';
        $this->m_customProperties['comment_mode'] = 'getCommentMode';
        
    } // fn __construct
    
    public function getLanguage()
    {
        $MetaLanguage = new MetaLanguage($this->language_id);
        return $MetaLanguage;   
    }
    
    public function getUser()
    {
        $MetaUser = new MetaUser($this->user_id);
        return $MetaUser;   
    }
            
    public function getEntriesCount()
    {
        return $this->entries_online + $this->entries_offline;
    }
            
    public function getCommentsCount()
    {
        return $this->comments_online + $this->comments_offline;
    }
    
    public function getCaptchaEnabled()
    {
        return (SystemPref::Get("PLUGIN_BLOGCOMMENT_USE_CAPTCHA") == 'Y');
    }
    
    public function getCommentMode()
    {
        return SystemPref::Get("PLUGIN_BLOGCOMMENT_MODE");
    }
} // class MetaBlog

?>