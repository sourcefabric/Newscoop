<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/Phorum_message.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');
require_once($g_documentRoot.'/include/pear/Date.php');

/**
 * @package Campsite
 */
final class MetaComment extends MetaDbObject {

    private function InitProperties()
    {
        if (!is_null($this->m_properties)) {
            return;
        }
        $this->m_properties['identifier'] = 'message_id';
        $this->m_properties['nickname'] = 'author';
        $this->m_properties['reader_email'] = 'email';
        $this->m_properties['subject'] = 'subject';
        $this->m_properties['content'] = 'body';
        $this->m_properties['level'] = 'thread_depth';
    }


    public function __construct($p_messageId = null)
    {
        $this->m_dbObject = new Phorum_message($p_messageId);
        if (!$this->m_dbObject->exists()) {
            $this->m_dbObject = new Phorum_message();
        }
        
        $this->InitProperties();
        $this->m_customProperties['submit_date'] = 'getSubmitDate';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    protected function getSubmitDate() {
        $date = new Date($this->m_dbObject->getCreationDate());
        return $date->getDate();
    }


    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
                        . OF_OBJECT_STRING . ' comment';
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }


} // class MetaComment

?>