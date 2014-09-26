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
use \Doctrine\ORM\EntityManager;
use Newscoop\ArticlesBundle\Entity\EditorialComment;

class EditorialCommentsService
{
    protected $em;
    protected $userService;

    /**
     * @param Doctrine\ORM\EntityManager $em [description]
     */
    public function __construct(EntityManager $em, $userService)
    {
        $this->em = $em;
        $this->userService = $userService;
    }

    public function create($commentContent, Article $article, $user, $parrentComment = null)
    {

        // TODO: Add use for class
        $editorialComment = new EditorialComment();
        $editorialComment->setComment($commentContent);
        $editorialComment->setUser($user);
        $editorialComment->setArticle($article);

        $this->em->persist($editorialComment);
        $this->em->flush();

        return true;
    }
}
