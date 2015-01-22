<?php
/**
 * @package Newscoop
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
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
    const CRON_NAME = 'Indexer';

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
     * Name of the indexer
     *
     * @var string
     */
    protected $name;

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
     * @param bool $enabled
     */
    public function __construct(
        Container $container,
        ServiceInterface $service,
        RepositoryInterface $repository = null,
        $indexerName
    )
    {
        $this->container = $container;
        $this->service = $service;
        $this->repository = $repository;
        $this->name = $indexerName;
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
        $logger = $this->container->get('logger');

        $batches = ($count <= self::BATCH_MAX) ? 1 : ceil($count/self::BATCH_MAX);

        for ($i = 1; $i <= $batches; $i++)  {

            $batchCount = ($i == $batches && ($count%self::BATCH_MAX) > 0)
                ? ($count%self::BATCH_MAX) : self::BATCH_MAX;
            $items = $this->repository->getBatch($batchCount, $filter);

            // TODO: Built in check if $items are less then $batchcount, we could stop iterating

            foreach ($this->indexClients as $client) {

                if ($client->isEnabled($this->name)) {

                    $client->setService($this->service);

                    foreach ($items as $item) {

                        $client->setItem($item);

                        if ($client->isTypeIndexable($this->name, $this->service->getSubType($item))) {

                            if ($this->service->isIndexable($item)) {

                                try {
                                    $client->add($this->service->getDocument($item));
                                } catch(Exception $e) {
                                    $itemData = $this->service->getDocument($item);
                                    $logger->error('Could not (completely) add item ('.$itemData['id'].') to indexing client. ('.__CLASS__ .' - '. $e->getMessage() .')');
                                }
                            } elseif ($this->service->isIndexed($item)) {

                                try {
                                    $client->delete($this->service->getDocumentId($item));
                                } catch(Exception $e) {
                                    $logger->error('Could not (completely) delete item to indexing client. ('.__CLASS__ .' - '. $e->getMessage() .')');
                                }
                            }
                        }
                    }

                    $client->flush();
                }
            }

            $this->repository->setIndexedNow($items);
        }
    }

    /**
     * Delete event listener
     *
     * @return void
     */
    public function delete($event)
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

        foreach ($servicIds as $serviceId) {

            if (strpos($serviceId, 'index_client.') === false) {
                 continue;
            }

            $indexingServices[$serviceId] = $this->container->get($serviceId);
        }

        return $indexingServices;
    }
}
