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

require_once($g_documentRoot.'/classes/Attachment.php');
require_once($g_documentRoot.'/classes/Exceptions.php');


/**
 * @package Campsite
 */
final class MetaAttachment {
    //
    private $m_data = null;
    //
    private $m_instance = false;
    //
    private $m_baseFields = array(
                                  'file_name',
                                  'mime_type',
                                  'extension',
                                  'fk_description_id',
                                  'size_in_bytes'
                                  );


    public function __construct($p_attachId)
    {
        $attachObj = new Attachment($p_attachId);

        if (!is_object($attachObj) || !$attachObj->exists()) {
            return false;
        }
        foreach ($attachObj->m_data as $key => $value) {
            if (in_array($key, $this->m_baseFields)) {
                $this->m_data[$key] = $value;
            }
        }
        $this->m_instance = true;
    } // fn __construct


    public function __get($p_property)
    {
        if (!is_array($this->m_data)) {
            return false;
        }
        if (!array_key_exists($p_property, $this->m_data)) {
            return false;
        }

        return $this->m_data[$p_property];
    } // fn __get


    public function __set($p_property, $p_value)
    {
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


    public function defined()
    {
        return $this->m_instance;
    } // fn defined

} // class MetaAttachment

?>