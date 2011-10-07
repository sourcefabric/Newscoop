<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

/**
 * Blog service
 */
class BlogService
{
    const SEPARATOR = ',';

    /** @var array */
    private $config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Test if user is blogger
     *
     * @param Newscoop\Entity\User $user
     * @return bool
     */
    public function isBlogger(User $user)
    {
        foreach ($user->getGroups() as $group) {
            if ($this->config['role'] == $group->getId()) {
                if (count($user->getGroups()) == 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get blog info article
     *
     * @param Newscoop\Entity\User $user
     * @return Article|null
     */
    public function getBlogInfo(User $user)
    {
        $articles = \Article::GetArticles($this->config['publication'], $this->config['issue'], null, null, null, false, array(
            "Type = '" . $this->config['type'] . "'",
        ));

        foreach ($articles as $article) {
            $data = $article->getArticleData();
            $authors = explode(self::SEPARATOR, $data->getFieldValue('loginname'));
            if (in_array($user->getUsername(), $authors)) {
                return $article;
            }
        }

        return null;
    }

    /**
     * Get blog section for given user
     *
     * @param Newscoop\Entity\User $user
     * @return Section|null
     */
    public function getSection(User $user)
    {
        $blogInfo = $this->getBlogInfo($user);
        return isset($blogInfo) ? $blogInfo->getSection() : null;
    }

    /**
     * Create blog article
     *
     * @param string $title
     * @param Section $section
     * @return Article
     */
    public function createBlog($title, \Section $section)
    {
        $article = new \Article($section->getLanguageId());
        $article->create($this->config['article_type'], $title, $section->getPublicationId(), $section->getIssueNumber(), $section->getSectionNumber());
        return $article;
    }

    /**
     * Test if blogger is allowed to perform action
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return bool
     */
    public function isAllowed(\Zend_Controller_Request_Abstract $request, User $user = null)
    {
        if (in_array($request->getControllerName(), array('blog', 'auth'))) {
            return TRUE;
        }

        if ($request->isXmlHttpRequest()) {
            return TRUE;
        }

        if ($request->getParam('controller') == 'ad.php') {
            return TRUE;
        }

        if ($request->getParam('controller') == 'articles' && in_array($request->getParam('action'), array('edit.php', 'do_article_action.php', 'preview.php', 'locations')) && isset($user)) {
            $section = $this->getSection($user);
            if ($section->getSectionNumber() == $request->getParam('f_section_number')
                && $section->getPublicationId() == $request->getParam('f_publication_id')
                && $section->getIssueNumber() == $request->getParam('f_issue_number')
                && $section->getLanguageId() == $request->getParam('f_language_id')) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Test if author is blog author
     *
     * @param Author $author
     * @param Article $blogInfo
     * @return bool
     */
    public function isBlogAuthor(\Author $author, \Article $blogInfo)
    {
        return in_array($author->getId(), array_map(function($blogAuthor) {
            return $blogAuthor->getId();
        }, \ArticleAuthor::GetAuthorsByArticle($blogInfo->getArticleNumber(), $blogInfo->getLanguageId())));
    }
}
