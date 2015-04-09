<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric ź.u.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesBundle\Services;

use Newscoop\Entity\Article;
use Newscoop\Entity\User;
use Doctrine\ORM\EntityManager;
use Newscoop\ArticlesBundle\Entity\EditorialComment;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EditorialCommentsService
{
    /**
     * Entity Manager
     * @var Doctrine\ORM\EntityManage
     */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em [description]
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Create new editorial comment for Article
     *
     * @param string                   $commentContent
     * @param Article                  $article
     * @param User                     $user
     * @param EditorialComment\null    $parrentComment
     */
    public function create($commentContent, Article $article, User $user, $parrentComment = null)
    {
        $editorialComment = new EditorialComment();
        $editorialComment->setComment($commentContent);
        $editorialComment->setUser($user);
        $editorialComment->setArticle($article);

        if ($parrentComment) {
            $editorialComment->setParent($parrentComment);
        }

        $this->em->persist($editorialComment);
        $this->em->flush();

        return $editorialComment;
    }

    /**
     * Edit existing editorial comment message
     * @param string           $commentContent
     * @param EditorialComment $comment
     * @param User             $user
     */
    public function edit($commentContent, EditorialComment $comment, User $user)
    {
        if ($comment->getUser()->getId() == $user->getId() || $user->isAdmin()) {
            $comment->setComment($commentContent);
        } else {
            throw new AccessDeniedHttpException("User is not allowed to edit someone else comment");
        }

        $this->em->flush();

        return true;
    }

    /**
     * Resolve existing editorial comment
     * @param EditorialComment $comment
     * @param User             $user
     */
    public function resolve(EditorialComment $comment, User $user, $value = true)
    {
        if ($comment->getUser()->getId() == $user->getId() || $user->isAdmin()) {
            $comment->setResolved($value);
        } else {
            throw new AccessDeniedHttpException("User is not allowed to resolve comment");
        }

        $this->em->flush();

        return true;
    }

    /**
     * Remove (soft) existing editorial comment
     * @param EditorialComment $comment
     * @param User             $user
     */
    public function remove(EditorialComment $comment, User $user)
    {
        if ($comment->getUser()->getId() == $user->getId() || $user->isAdmin()) {
            $comment->setIsActive(false);
        } else {
            throw new AccessDeniedHttpException("User is not allowed to remove comment");
        }

        $this->em->flush();

        return true;
    }
}
