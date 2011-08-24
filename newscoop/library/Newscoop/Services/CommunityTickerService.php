<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\Events\CommunityTickerEvent;

/**
 * Community Ticker service
 */
class CommunityTickerService
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
     * @param sfEvent $event
     * @return void
     */
    public function update(\sfEvent $event)
    {
        $params = $event->getParameters();

        if (empty($params['user'])) {
            $params['user'] = null;
        }

        $this->getRepository()->save(new CommunityTickerEvent(), array(
            'event' => $event->getName(),
            'user' => isset($params['user']) ? $params['user'] : null,
            'params' => $params,
        ));

        $this->em->flush();
    }

    /**
     * Find events
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findAll($limit, $offset = 0)
    {
        return $this->getRepository()->findBy(array(), array('id' => 'desc'), $limit, $offset);
    }

    /**
     * Find user events
     *
     * @param int $user
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findByUser($userId, $limit, $offset = 0)
    {
        return $this->getRepository()->findBy(array(
            'user' => $userId,
        ), array('id' => 'desc'), $limit, $offset);
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
