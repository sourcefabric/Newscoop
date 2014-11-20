<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
/**
 * Anchor for deleteing view helper
 */
class Admin_View_Helper_LinkArticleObj extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @return string
     */
    public function linkArticleObj($p_article)
    {
        $params = array(

                    'f_publication_id'      => $p_article->getPublicationId(),
                    'f_issue_number'        => $p_article->getIssueNumber(),
                    'f_section_number'      => $p_article->getSectionNumber(),
                    'f_article_number'      => $p_article->getArticleNumber(),
                    'f_language_id'         => $p_article->getLanguageId(),
                    'f_language_selected'   => $p_article->getLanguageId(),
                  );
        return http_build_query($params);
    }
}
