<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\User,
    Newscoop\Entity\Author;

/**
 * Author service
 */
class AuthorService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /** @var Doctrine\ORM\EntityRepository */
    private $repository;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository('Newscoop\Entity\Author');
    }

    /**
     * Get author options
     *
     * @return array
     */
    public function getOptions()
    {
        $authors = array();
        foreach ($this->repository->findAll() as $author) {
            $authors[$author->getId()] = $author->getFullName();
        }

        return $authors;
    }

    /**
     * Set user author
     *
     * @param int $authorId
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function setAuthorUser($authorId, User $user)
    {
        if (empty($authorId)) {
            $author = $this->repository->findOneBy(array(
                'user' => $user->getId(),
            ));
            $author->setUser(null);
        } else {
            $author = $this->repository->find($authorId);
            $author->setUser($user);
        }

        $this->em->persist($author);
        $this->em->flush();
    }

    /**
     * Get author user
     *
     * @param int $authorId
     * @return Newscoop\Entity\User
     */
    public function getAuthorUser($authorId)
    {
        return $this->repository->find($authorId)->getUser();
    }
}
