<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Article action helper
 */
class Action_Helper_Article extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Get article edit link
     *
     * @param Article $article
     * @return string
     */
    public function getEditLink($article)
    {
        $params = array(
            'f_publication_id' => $article->getPublicationId(),
            'f_issue_number' => $article->getIssueNumber(),
            'f_section_number' => $article->getSectionNumber(),
            'f_article_number' => $article->getArticleNumber(),
            'f_language_id' => $article->getLanguageId(),
            'f_language_selected' => $article->getLanguageId(),
        );

        $paramsStrings = array();
        foreach ($params as $key => $val) {
            $paramsStrings[] = "$key=$val";
        }

		return '/admin/articles/edit.php?' . implode('&', $paramsStrings);
    }
}
