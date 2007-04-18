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


/**
 * @package Campsite
 */
final class CampContext {
    //
    private $m_articleId = null;
    //
    private $m_publicationId = null;
    //
    private $m_issueNr = null;
    //
    private $m_sectionNr = null;


    public function __construct() {} // fn __construct


    public function setArticleId($p_articleId)
    {
        if (empty($p_articleId)) {
            return false;
        }

        $this->m_articleId = $p_articleId;
    }


    function getArticleId()
    {
        return $this->m_articleId;
    }

} // class CampContext

?>