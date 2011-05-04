<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository\User;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\User\Ip,
    Newscoop\Entity\User\Subscriber;

/**
 * IP address repository
 */
class IpRepository extends EntityRepository
{
    /**
     * Save ip
     *
     * @param Newscoop\Entity\User\Ip $ip
     * @param Newscoop\Entity\User\Subscriber $subscriber
     * @param array $values
     * @return void
     */
    public function save(Ip $ip, Subscriber $subscriber, array $values)
    {
        $em = $this->getEntityManager();

        $ip->setSubscriber($subscriber)
            ->setIp($values['ip'])
            ->setNumber($values['number'])
            ->setSubscriber($subscriber);

        $em->persist($ip);
    }

    /**
     * Delete ip
     *
     * @param string $ip
     * @param Newscoop\Entity\User\Subscriber $subscriber
     */
    public function delete($ip, Subscriber $subscriber)
    {
        $em = $this->getEntityManager();

        $ip = $this->findOneBy(array(
            'ip' => ip2long($ip),
            'subscriber' => $subscriber->getId(),
        ));

        $em->remove($ip);
    }
}
