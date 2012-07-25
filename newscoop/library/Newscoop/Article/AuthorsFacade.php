<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Article;

use Doctrine\ORM\EntityManager;
use Newscoop\Services\UserService;
use Newscoop\Services\BlogService;
use Newscoop\Entity\ArticleAuthor;

/**
 */
class AuthorsFacade
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var Newscoop\Services\UserService
     */
    private $userService;

    /**
     * @var Newscoop\Services\BlogService
     */
    private $blogService;

    /**
     * @var bool
     */
    private $currentUserIsBlogger;

    /**
     * @var object
     */
    private $blogInfo;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Services\UserService $userService
     * @param Newscoop\Services\BlogService $blogService
     */
    public function __construct(EntityManager $em, UserService $userService, BlogService $blogService)
    {
        $this->em = $em;
        $this->userService = $userService;
        $this->blogService = $blogService;
    }

    /**
     * Update article authors
     *
     * @param Article $article
     * @param array $authors
     * @param array $authorTypes
     * @return void
     */
    public function updateArticleAuthors($article, $authors, $authorTypes)
    {
        $this->removeArticleAuthors($article);

        foreach ($authors as $i => $author) {
            $authorObj = new \Author($author);
            if (!$authorObj->exists() && strlen(trim($author)) > 0) {
                if ($this->isCurrentUserBlogger()) {
                    continue;
                }

                $authorData = \Author::ReadName($author);
                $authorObj->create($authorData);
            } elseif ($this->isCurrentUserBlogger()) {
                if (!$this->blogService->isBlogAuthor($authorObj, $this->getBlogInfo())) {
                    continue;
                }
            }

            // Sets the author type selected
            $authorObj->setType($authorTypes[$i]);

            // Links the author to the article
            if ($authorObj->getId() != 0) {
                $articleAuthorObj = new ArticleAuthor(
                    $article->getArticleNumber(),
                    $article->getLanguageId(),
                    $authorObj->getId(),
                    $authorTypes[$i],
                    $i + 1
                );

                $this->em->persist($articleAuthorObj);
            }
        }

        $this->em->flush();
    }

    /**
     * Remove article authors
     *
     * @param Article $article
     * @return void
     */
    protected function removeArticleAuthors($article)
    {
        $articleAuthors = $this->em->getRepository('Newscoop\Entity\ArticleAuthor')->findBy(array(
            'articleNumber' => $article->getNumber(),
            'languageId' => $article->getLanguageId(),
        ));

        foreach ($articleAuthors as $articleAuthor) {
            $this->em->remove($articleAuthor);
        }

        $this->em->flush();
    }

    /**
     * Test if current user is blogger
     *
     * @return bool
     */
    private function isCurrentUserBlogger()
    {
        if ($this->currentUserIsBlogger === null) {
            $this->currentUserIsBlogger = $this->blogService->isBlogger($this->userService->getCurrentUser());
        }

        return $this->currentUserIsBlogger;
    }

    /**
     * Get bloginfo for current user
     *
     * @return object
     */
    private function getBlogInfo()
    {
        if ($this->blogInfo === null) {
            $this->blogInfo = $this->blogService->getBlogInfo($this->userService->getCurrentUser());
        }

        return $this->blogInfo;
    }
}
