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
use Exception;

/**
 * Indexer
 */
class Indexer
{
    const BATCH_MAX = 200;

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
     * @param ServiceInterface $service
     * @param RepositoryInterface $repository
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
     * @param integer $count Number of items to index
     * @param array $filter Filter for the batch results
     *
     * @return void
     */
    public function update($count = 50, $filter = null)
    {
        $logger = $this->container->get('logger');

        $batches = ($count <= self::BATCH_MAX) ? 1 : ceil($count/self::BATCH_MAX);

        for ($i = 1; $i <= $batches; $i++)  {

            $batchCount = ($i == $batches && ($count%self::BATCH_MAX) > 0)
                ? ($count%self::BATCH_MAX) : self::BATCH_MAX;
            $items = $this->repository->getBatch($batchCount, $filter);

            // TODO: Built in check if $items are less then $batchcount, we could stop iterating

            foreach ($this->indexClients as $client) {

                $client->setService($this->service);

                foreach ($items as $item) {

                    $client->setItem($item);

                    if ($this->service->isIndexable($item)) {

                        try {
                            $client->add($this->service->getDocument($item));
                        } catch(Exception $e) {
                            $itemData = $this->service->getDocument($item);
                            $logger->error('Could not (completely) add item ('.$itemData['id'].') to indexing client. ('.__CLASS__ .' - '. $e->getMessage() .')');
                        }
                    } else if ($this->service->isIndexed($item)) {

                        try {
                            $client->delete($this->service->getDocumentId($item));
                        } catch(Exception $e) {
                            $logger->error('Could not (completely) delete item to indexing client. ('.__CLASS__ .' - '. $e->getMessage() .')');
                        }
                    }
                }

                $client->flush();
            }

            $this->repository->setIndexedNow($items);
        }
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
            foreach ($this->indexClients as $client) {
                $client->delete($this->service->getDocumentId($event['entity']));
                $client->flush();
            }
        }
    }

    /**
     * Clear all indexed timestamps
     *
     * @return void
     */
    public function clearAll()
    {
        $this->repository->setIndexedNull();
    }

    /**
     * Delete all docs from indexing clients
     *
     * @return void
     */
    public function deleteAll()
    {
        foreach ($this->indexClients as $client) {
            $client->deleteAll();
        }
        $this->clearAll();
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

        foreach ($servicIds as $serviceId) {

            if (strpos($serviceId, 'index_client.') === false) {
                 continue;
            }

            $indexingServices[$serviceId] = $this->container->get($serviceId);
        }

        return $indexingServices;
    }
}
