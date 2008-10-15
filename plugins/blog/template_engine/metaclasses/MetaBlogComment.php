<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <sebastian.goebel@web.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

final class MetaBlogComment extends MetaDbObject {

    private function InitProperties()
    {
        if (!is_null($this->m_properties)) {
            return;
        }
        $this->m_properties['identifier'] = 'comment_id';
        $this->m_properties['entry_id'] = 'fk_entry_id';
        $this->m_properties['blog_id'] = 'fk_blog_id';
        $this->m_properties['user_id'] = 'fk_user_id';
        $this->m_properties['user_name'] = 'user_name';
        $this->m_properties['user_email'] = 'user_email';
        $this->m_properties['published'] = 'published';
        $this->m_properties['title'] = 'title';
        $this->m_properties['name'] = 'title';
        $this->m_properties['content'] = 'content';
        $this->m_properties['mood'] = 'mood';
        $this->m_properties['status'] = 'status';
        $this->m_properties['admin_status'] = 'admin_status';
        $this->m_properties['feature'] = 'feature';
    }


    public function __construct($p_comment_id=null)
    {
        $this->m_dbObject = new BlogComment($p_comment_id);

        $this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct

} // class MetaBlog

?>