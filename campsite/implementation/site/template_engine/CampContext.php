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

// Meta classes
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaLanguage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaPublication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaIssue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaSection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaArticle.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaImage.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaAttachment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaAudioclip.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaComment.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaTopic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaUser.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaTemplate.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/template_engine/MetaSubscription.php');

define('INVALID_OBJECT_STRING', 'invalid object');


/**
 * @package Campsite
 */
final class CampContext {
    //
    private $m_objects = array('publication',
                               'issue',
                               'section',
                               'article',
                               'language',
                               'image',
                               'attachment',
                               'audioclip',
                               'comment',
                               'topic',
                               'user',
                               'template',
                               'subscription'
                               );


    /**
     *
     */
    public function __construct()
    {

    } // fn __construct


    /**
     *
     */
    public function __get($p_object)
    {
        try {
            return $this->getObject($p_object);
        } catch (InvalidObjectException $e) {
            $this->trigger_invalid_object_error($p_object);
            return null;
        }
    } // fn __get


    /**
     *
     */
    public function __set($p_object, $p_value)
    {
        if (!in_array($p_object, $this->m_objects)
                || !is_object($p_value)
                || !$p_value->defined()) {
            return false;
        }

        $this->$p_object = $p_value;
    } // fn __set


    /**
     *
     */
    protected function getObject($p_object)
    {
        if (!is_array($this->m_objects)
            || !in_array($p_object, $this->m_objects)) {
            throw new InvalidObjectException($p_object);
        }
        if (!is_object($this->$p_object)) {
            return null;
        }

        return $this->$p_object;
    } // fn getObject


    /**
     *
     */
    final protected function trigger_invalid_object_error($p_object)
    {
		CampTemplate::singleton()->trigger_error(INVALID_OBJECT_STRING . " $p_object ");
    } // fn trigger_invalid_object_error

} // class CampContext

?>