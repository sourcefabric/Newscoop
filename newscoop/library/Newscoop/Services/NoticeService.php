<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\Common\Persistence\ObjectManager,
Newscoop\Entity\Notice,
Newscoop\Persistence\ObjectRepository;

/**
 * User service
 */
class NoticeService implements ObjectRepository
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    private $em;

    /** @var \Newscoop\Entity\Repository\NoticeRepository */
    private $repository;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Zend_Auth $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Find user
     *
     * @param int $id
     * @return Newscoop\Entity\Notice
     */
    public function find($id)
    {
        return $this->getRepository()
            ->find($id);
    }

    /**
     * Find all users
     *
     * @return mixed
     */
    public function findAll($hydration = 1)
    {
        return $this->getRepository()->getNotices($hydration);
    }

    /**
     * Find by given criteria
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return mixed
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find one by given criteria
     *
     * @param array $criteria
     * @return Newscoop\Entity\User
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()
            ->findOneBy($criteria);
    }

    /**
     * Get repository for user entity
     *
     * @return Newscoop\Entity\Repository\UserRepository
     */
    private function getRepository()
    {
        if (null === $this->repository) {
            $this->repository = $this->em->getRepository('Newscoop\Entity\Notice');
        }

        return $this->repository;
    }
}
