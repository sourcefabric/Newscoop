<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\Article;
/**
 * Anchor for deleteing view helper
 */
class Admin_View_Helper_LinkArticle extends Zend_View_Helper_Abstract
{
    /**
     * Render actions
     *
     * @param array $actions
     * @return void
     */
    public function linkArticle( Article $p_article)
    {
        $params = array(
                    'f_publication_id'      => $p_article->getPublicationId(),
                    'f_issue_number'        => $p_article->getIssueId(),
                    'f_section_number'      => $p_article->getSectionId(),
                    'f_article_number'      => $p_article->getId(),
                    'f_language_id'         => $p_article->getLanguageId(),
                    'f_language_selected'   => $p_article->getLanguageId(),
                  );
        return http_build_query($params);
    }
}
