<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Blog service
 */
class BlogService
{
    const SEPARATOR = ',';

    /** @var array */
    protected $config = array();

    /** @var array */
    protected $getArticleActions = array(
        'edit.php',
        'preview.php',
        'locations',
        'images',
        'topics',
        'files',
        'comments',
        'autopublish.php',
        'do_unlock.php',
        'context_box',
    );

    /** @var array */
    protected $postArticleActions = array(
        'images',
        'topics',
        'files',
        'comments',
        'do_article_action.php',
        'autopublish_do_add.php',
        'context_box',
    );

    protected $em;

    /**
     * @param array $config
     */
    public function __construct(array $config, EntityManager $em)
    {
        $this->config = $config;
        $this->em = $em;
    }

    /**
     * Test if user is blogger
     *
     * @param  Newscoop\Entity\User $user
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
     * @param  Newscoop\Entity\User $user
     * @return Article|null
     */
    public function getBlogInfo(User $user)
    {
        $articles = \Article::GetArticles($this->config['publication'], $this->config['issue'], null, null, null, false, array(
            "Type = '" . $this->config['type'] . "'",
        ));

        foreach ($articles as $article) {
            $data = $article->getArticleData();
            $authors = array_map('trim', explode(self::SEPARATOR, $data->getFieldValue('loginname')));
            if (in_array($user->getUsername(), $authors)) {
                return $article;
            }
        }

        return null;
    }

    /**
     * Get blog section for given user
     *
     * @param  Newscoop\Entity\User $user
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
     * @param  string  $title
     * @param  \Section $section
     * @return \Article
     */
    public function createBlog($title, \Section $section)
    {
        $article = new \Article($section->getLanguageId());
        $article->create($this->config['article_type'], $title, $section->getPublicationId(), $section->getIssueNumber(), $section->getSectionNumber());

        $qb = $this->em->createQueryBuilder();
        $qb ->select('atf')
            ->from('\Newscoop\Entity\ArticleTypeField', 'atf')
            ->where("atf.typeHack = ?1 AND atf.name IS NOT NULL AND atf.name = 'NULL'")
            ->setParameter(1, $this->config['article_type']);

        $articleTypeField = $qb->getQuery()->getOneOrNullResult();
        $article->setCommentsEnabled($articleTypeField->getCommentsEnabled());

        return $article;
    }

    /**
     * Test if blogger is allowed to perform action
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return bool
     */
    public function isAllowed(\Zend_Controller_Request_Abstract $request, User $user = null)
    {
        if (in_array($request->getControllerName(), array('blog', 'auth', 'image', 'slideshow', 'media'))) {
            return TRUE;
        }

        if ($request->isXmlHttpRequest()) {
            return TRUE;
        }

        if ($request->getParam('controller') == 'ad.php') {
            return TRUE;
        }

        if ($request->isPost() && $request->getParam('controller') == 'articles' && in_array($request->getParam('action'), $this->postArticleActions)) {
            if ($this->isRequestedArticleEditable($request, $user)) {
                return TRUE;
            }
        }

        if ($request->isGet() && $request->getParam('controller') == 'articles' && in_array($request->getParam('action'), $this->getArticleActions) && isset($user)) {
            if ($this->isRequestedArticleEditable($request, $user)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Test if author is blog author
     *
     * @param  \Author  $author
     * @param  \Article $blogInfo
     * @return bool
     */
    public function isBlogAuthor(\Author $author, \Article $blogInfo)
    {
        return in_array($author->getId(), array_map(function ($blogAuthor) {
            return $blogAuthor->getId();
        }, \ArticleAuthor::GetAuthorsByArticle($blogInfo->getArticleNumber(), $blogInfo->getLanguageId())));
    }

    /**
     * Test if requested article is editable by user
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @param  null|User             $user
     * @return bool
     */
    private function isRequestedArticleEditable(\Zend_Controller_Request_Abstract $request, User $user)
    {
        $article = new \Article($request->getParam('f_language_selected'), $request->getParam('f_article_number'));

        return $this->isUsersArticle($article, $user);
    }

    /**
     * Test if given article is from users blog
     *
     * @param  \Article              $article
     * @param  Newscoop\Entity\User $user
     * @return bool
     */
    public function isUsersArticle(\Article $article, User $user)
    {
        $section = $this->getSection($user);

        return $section->getSectionNumber() == $article->getSectionNumber()
            && $section->getPublicationId() == $article->getPublicationId()
            && $section->getIssueNumber() == $article->getIssueNumber()
            && $section->getLanguageId() == $article->getLanguageId();
    }
}
