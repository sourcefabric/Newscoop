<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\AuditEvent,
    Newscoop\Entity\Repository\AuditRepository;

/**
 * Audit service
 */
class AuditService
{
    /** @var Newscoop\Entity\Repository\AuditRepository */
    protected $repository;

    /** @var Newscoop\Service\User */
    protected $userService;

    /**
     * @param Newscoop\Entity\Repository\AuditRepository $repository
     * @param Newscoop\Service\User $userService
     */
    public function __construct(AuditRepository $repository, UserService $userService)
    {
        $this->repository = $repository;
        $this->userService = $userService;
    }

    /**
     * Update audit.
     *
     * @param sfEvent $event
     * @return void
     */
    public function update(\sfEvent $event)
    {
        list($resource, $action) = explode('.', $event->getName());
        $user = isset($event['user']) ? $event['user'] : $this->userService->getCurrentUser();
        $params = $event->getParameters();

        $auditEvent = new AuditEvent();
        $values = array(
            'user' => $user,
            'action' => $action,
            'resource_id' => !empty($params['id']) ? $params['id'] : null,
            'resource_type' => $resource,
            'resource_diff' => !empty($params['diff']) ? $params['diff'] : null,
            'resource_title' => !empty($params['title']) ? $params['title'] : null,
        );

        $this->repository->save($auditEvent, $values);
    }

    /**
     * Find all records.
     *
     * @return array
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Find records by set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }
}
