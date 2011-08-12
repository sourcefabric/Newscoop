<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

/**
 * Audit service.
 */
class Audit
{
    /** @var Zend_Http_Client $client */ protected $client;

    /** @var Newscoop\Service\User */
    protected $userService;

    /**
     * @param Zend_Http_Client $client
     * @param Newscoop\Service\User $userService
     */
    public function __construct(\Zend_Http_Client $client, User $userService)
    {
        $this->client = $client;
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
        $user = isset($event['user']) ? $event['user'] : null; //$this->userService->getCurrentUser();
        $params = $event->getParameters();

        $auditEvent = array(
            'resource' => $resource,
            'action' => $action,
            'user' => $user,
            'resource_id' => !empty($params['id']) ? $params['id'] : null,
            'resource_diff' => !empty($params['diff']) ? $params['diff'] : null,
            'resource_title' => !empty($params['title']) ? $params['title'] : null,
            'created' => time(),
        );

        return $auditEvent;

        // TODO make it work with rest
        $this->client->setHeaders(array(
            'Content-Type' => 'text/json',
            'Accept' => 'text/json',
            'Accept-Charset' => 'utf-8',
        ))->setRawData(json_encode(array('Audit' => $auditEvent)));

        $response = $this->client->request('POST');
        var_dump($response->getStatus(), json_decode($response->getBody()));
    }

    /**
     * Find all records.
     *
     * @return array
     */
    public function findAll()
    {
        return array();
        // return $this->repository->findAll();
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
        return array();
        // return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }
}
