<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\AuditEvent;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 * Audit service
 */
class AuditService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Newscoop\Services\UserService */
    protected $userService;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Newscoop\Service\User $userService
     */
    public function __construct(EntityManager $em, UserService $userService)
    {
        $this->em = $em;
        $this->userService = $userService;
    }

    /**
     * Update audit
     *
     * @param GenericEvent $event
     * @return void
     */
    public function update(GenericEvent $event)
    {
        try {
            $user = isset($event['user']) ? $event['user'] : $this->userService->getCurrentUser();
            if (!is_int($user)) {
                $user = $user->getId();
            }
        } catch (\Exception $e) {
            $user = null;
        }

        list($resource, $action) = explode('.', $event->getName());
        $params = $event->getArguments();
        $values = array(
            'user' => $user,
            'action' => $action,
            'resource_id' => !empty($params['id']) ? $params['id'] : null,
            'resource_type' => $resource,
            'resource_diff' => !empty($params['diff']) ? $params['diff'] : null,
            'resource_title' => !empty($params['title']) ? $params['title'] : null,
        );

        $this->em->getRepository('Newscoop\Entity\AuditEvent')->save(new AuditEvent(), $values);
        $this->em->flush();
    }

    /**
     * Find all records
     *
     * @return array
     */
    public function findAll()
    {
        return $this->em->getRepository('Newscoop\Entity\AuditEvent')->findAll();
    }

    /**
     * Find records by set of criteria
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->em->getRepository('Newscoop\Entity\AuditEvent')->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function countBy(array $criteria)
    {
        return $this->em->getRepository('Newscoop\Entity\AuditEvent')->countBy($criteria);
    }

    public function getResourceTypes()
    {
        $resources = $this->em->getRepository('Newscoop\Entity\AuditEvent')
            ->createQueryBuilder('ae')
            ->select('DISTINCT(ae.resource_type) as type')
            ->orderBy('ae.resource_type')
            ->getQuery()
            ->getScalarResult();

        return array_map(function($row) {
            return $row['type'];
        }, $resources);
    }

    public function getActionTypes()
    {
        return array('create', 'delete', 'update');
    }
}
