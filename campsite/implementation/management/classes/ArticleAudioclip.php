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

require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/Audioclip.php');

/**
 * @package Campsite
 */
class ArticleAudioclip extends DatabaseObject {
    var $m_keyColumnNames = array('fk_article_number', 'fk_audioclip_gunid');
    var $m_dbTableName = 'ArticleAudioclips';
    var $m_columnNames = array('fk_article_number',
                               'fk_audioclip_gunid',
                               'fk_language_id',
                               'order_no');

    /**
     * The article audioclip table links together articles with Audioclips.
     *
     * @param int $p_articleNumber
     * @param int $p_audioclipGunId
     *
     * @return object ArticleAudioclip
     */
    function ArticleAudioclip($p_articleNumber = null, $p_audioclipGunId = null)
    {
        if (is_numeric($p_articleNumber)) {
            $this->m_data['fk_article_number'] = $p_articleNumber;
        }
        if (!is_null($p_audioclipGunId)) {
            $this->m_data['fk_audioclip_gunid'] = $p_audioclipGunId;
        }
        if (!is_null($p_articleNumber) && !is_null($p_audioclipGunId)) {
            $this->fetch();
        }
    } // constructor


    /**
     * @return int
     */
    function getArticleNumber()
    {
        return $this->m_data['fk_article_number'];
    } // fn getArticleNumber


    /**
     * @return string
     */
    function getAudioclipGunId()
    {
        return $this->m_data['fk_audioclip_gunid'];
    } // fn getAudioclipGunId


    /**
     * @return int
     */
    function getLanguageId()
    {
        return $this->m_data['fk_language_id'];
    } // fn getLanguageId


    /*
     * @return int
     */
    function getAudioclipOrder()
    {
        return $this->m_data['order_no'];
    } // fn getAudioclipOrder


    /**
     * Sets the order for the article audioclip
     *
     * @param int $p_orderNo
     *      The order number to be set
     */
    function setOrder($p_orderNo)
    {
        global $g_ado_db;

        if (!$this->m_exists) {
            return false;
        }
        $queryStr = "UPDATE ".$this->m_dbTableName."
                     SET order_no = '".intval($p_orderNo)."' "
                   ."WHERE fk_article_number = '".$this->getArticleNumber()."' "
                   ."AND fk_audioclip_gunid = '".$g_ado_db->escape($this->getAudioclipGunId())."'";
        $g_ado_db->Execute($queryStr);
    } // fn setOrder


    /**
     * Get all the audioclips that belong to this article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     *
     * @return array $returnArray
     *      An array of AudioclipMetadataEntry objects
     */
    function GetAudioclipsByArticleNumber($p_articleNumber, $p_languageId = null)
    {
        global $g_ado_db;

        if (is_null($p_languageId)) {
            $langConstraint = "FALSE";
        } else {
            $langConstraint = "fk_language_id=$p_languageId";
        }
        $queryStr = "SELECT fk_audioclip_gunid
                     FROM ArticleAudioclips
                     WHERE fk_article_number = '$p_articleNumber'
                     AND (fk_language_id IS NULL OR $langConstraint)
                     ORDER BY order_no";
        $rows = $g_ado_db->GetAll($queryStr);
        $returnArray = array();
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $returnArray[] =& new Audioclip($row['fk_audioclip_gunid']);
            }
        }

		return $returnArray;
    } // fn GetAudioclipsByArticleNumber


    /**
     * This is called when an audioclip file is deleted.
     * It will disassociate the audioclip from all articles.
     *
     * @param int $p_gunId
     *
     * @return void
     */
    function OnAudioclipDelete($p_gunId)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAudioclips 
                     WHERE fk_audioclip_gunid = '$p_gunId'";
        $g_ado_db->Execute($queryStr);
    } // fn OnAudioclipDelete


    /**
     * Remove audioclip pointers for the given article.
     *
     * @param int $p_articleNumber
     *
     * @return void
     */
    function OnArticleDelete($p_articleNumber)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAudioclips 
                     WHERE fk_article_number = '$p_articleNumber'";
        $g_ado_db->Execute($queryStr);
    } // fn OnArticleDelete


    /**
     * Copy all the pointers for the given article.
     *
     * @param int $p_srcArticleNumber
     * @param int $p_destArticleNumber
     *
     * @return void
     */
    function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
    {
        global $g_ado_db;

        $queryStr = "SELECT fk_audioclip_gunid, order_no 
                     FROM ArticleAudioclips 
                     WHERE fk_article_number='$p_srcArticleNumber'";
        $rows = $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $queryStr = "INSERT IGNORE INTO ArticleAudioclips 
                         (fk_article_number, fk_audioclip_gunid, order_no) 
                         VALUES ('$p_destArticleNumber', '"
                        .$row['fk_audioclip_gunid']."', '"
                        .$row['order_no']."')";
            $g_ado_db->Execute($queryStr);
        }
    } // fn OnArticleCopy

} // class ArticleAudioclip

?>