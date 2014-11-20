<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Entity\User\Ip;

/**
 * Ip Facade
 */
class IpFacade
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Newscoop\Entity\Repository\User\IpRepository
     */
    protected $repository;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository('Newscoop\Entity\User\Ip');
    }

    /**
     * Find ips by user
     *
     * @param Newscoop\Entity\User|int $user
     * @return array
     */
    public function findByUser($user)
    {
        return $this->repository->findByUser($user);
    }

    /**
     * Save ip
     *
     * @param array $values
     * @return Ip
     */
    public function save(array $values)
    {
        $ip = new Ip($values['ip'], $values['number']);

        if (array_key_exists('user', $values)) {
            $ip->setUser(is_numeric($values['user']) ? $this->em->getReference('Newscoop\Entity\User', $values['user']) : $values['user']);
        }

        $this->em->persist($ip);
        $this->em->flush($ip);
        return $ip;
    }

    /**
     * Delete ip
     *
     * @param array $id
     * @return void
     */
    public function delete(array $id)
    {
        $id['ip'] = ip2long($id['ip']);
        $this->em->remove($this->repository->findOneBy($id));
        $this->em->flush();
    }
}
