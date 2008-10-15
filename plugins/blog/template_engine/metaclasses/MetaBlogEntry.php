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

final class MetaBlogEntry extends MetaDbObject {

    private function InitProperties()
    {
        if (!is_null($this->m_properties)) {
            return;
        }

        $this->m_properties['identifier'] = 'entry_id';
        $this->m_properties['user_id'] = 'fk_user_id';
        $this->m_properties['published'] = 'published';
        $this->m_properties['released'] = 'released';
        $this->m_properties['status'] = 'status';
        $this->m_properties['title'] = 'title';
        $this->m_properties['name'] = 'title';
        $this->m_properties['content'] = 'content';
        //$this->m_properties['tags'] = 'tags';
        $this->m_properties['mood'] = 'mood';
        $this->m_properties['admin_status'] = 'admin_status';
        $this->m_properties['comments_online'] = 'comments_online';
        $this->m_properties['comments_offline'] = 'comments_offline';
        $this->m_properties['feature'] = 'feature';
    }


    public function __construct($p_entry_id=null)
    {
        $this->m_dbObject = new BlogEntry($p_entry_id);

        $this->InitProperties();
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct

} // class MetaBlog

?>