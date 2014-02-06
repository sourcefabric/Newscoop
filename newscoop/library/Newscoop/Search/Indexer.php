<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Symfony\Component\DependencyInjection\Container;
use Newscoop\Search\IndexClientInterface;
use Newscoop\Search\ServiceInterface;
use Newscoop\Search\RepositoryInterface;

/**
 * Indexer
 */
class Indexer
{
    /**
     * @var Newscoop\Search\IndexClientInterface
     */
    protected $container;

    /**
     * @var Newscoop\Search\ServiceInterface
     */
    protected $service;

    /**
     * @var Newscoop\Search\RepositoryInterface
     */
    protected $repository;

    /**
     * Array containing object of Newscoop\Search\IndexClientInterface
     *
     * @var Array
     */
    protected $indexClients;

    /**
     * @param Newscoop\Search\IndexClientInterface $index
     * @param Newscoop\Search\ServiceInterface $service
     * @param Newscoop\Search\RepositoryInterface $repository
     */
    public function __construct(
        Container $container,
        ServiceInterface $service,
        RepositoryInterface $repository = null
    )
    {
        $this->container = $container;
        $this->service = $service;
        $this->repository = $repository;
        $this->indexClients = $this->getIndexClients();
    }

    /**
     * Update index
     *
     * @param mixed $count Number of items to index
     * @param array $filter Filter for the batch results
     *
     * @return void
     */
    public function update($count = 50, $filter = null)
    {
        $items = $this->repository->getBatch($count, $filter);

        foreach ($this->indexClients AS $client) {

            $client->setService($this->service);

            foreach ($items as $item) {

                $client->setItem($item);

                if ($this->service->isIndexable($item)) {

                    $client->add($this->service->getDocument($item));
                } else if ($this->service->isIndexed($item)) {

                    $client->delete($this->service->getDocumentId($item));
                }
            }

            $client->flush();
        }
        $this->repository->setIndexedNow($items);
    }

    /**
     * Delete event listener
     *
     * @param sfEvent $event
     * @return void
     */
    public function delete(\sfEvent $event)
    {
        if ($this->service->isIndexed($event['entity'])) {
            foreach ($this->indexClients AS $client) {
                $client->delete($this->service->getDocumentId($event['entity']));
                $client->flush();
            }
        }
    }

    /**
     * Delete all docs
     *
     * @return void
     */
    public function deleteAll()
    {
        foreach ($this->indexClients AS $client) {
            $client->deleteAll();
        }
        $this->repository->setIndexedNull();
    }

    /**
     * Get installed indexing clients
     *
     * @return array List of installed indexClients
     */
    private function getIndexClients()
    {
        $servicIds = $this->container->getServiceIds();
        $indexingServices = array();

        // TODO: Build in check to check if single indexing client has been set

        foreach ($servicIds AS $serviceId) {
            if (strpos($serviceId, 'index_client.') === false) continue;

            $indexingServices[$serviceId] = $this->container->get($serviceId);
        }

        return $indexingServices;
    }
}
