<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Events\CommunityTickerEvent;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 * Community Feed Service
 */
class CommunityFeedService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Update community ticker
     *
     * @param GenericEvent $event
     * @return void
     */
    public function update(GenericEvent $event)
    {
        $params = $event->getArguments();

        $user = array_key_exists('user', $params) ? $params['user'] : null;
        unset($params['user']);

        $this->getRepository()->save(new CommunityTickerEvent(), array(
            'event' => $event->getName(),
            'user' => $user,
            'params' => $params,
        ));

        $this->em->flush();
    }

    /**
     * Find by criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy, $limit, $offset)
    {
        return $this->getRepository()
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Count by criteria
     *
     * @param array $criteria
     * @return int
     */
    public function countBy(array $criteria)
    {
        return count($this->getRepository()->findBy($criteria));
    }

    /**
     * Get repository
     *
     * @return Newscoop\Entity\Repository\Events\CommunityTickerEventRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Events\CommunityTickerEvent');
    }
}
