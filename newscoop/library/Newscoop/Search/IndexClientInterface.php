<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

use Symfony\Component\DependencyInjection\Container;
use Newscoop\Search\ServiceInterface;
use Newscoop\Search\DocumentInterface;

/**
 * Index Interface
 */
interface IndexClientInterface
{
    /**
     * Initialize class
     *
     * @param Symfony\Component\DependencyInjection\Container $service
     */
    public function __construct(Container $container);

    /**
     * Add given item
     *
     * @param array $item
     *
     * @return boolean
     */
    public function add(array $item);

    /**
     * Update given item
     *
     * @param array $item
     *
     * @return boolean
     */
    public function update(array $item);

    /**
     * Delete given article by id
     *
     * @param String $itemId
     *
     * @return boolean
     */
    public function delete($itemId);

    /**
     * Commit changes to index
     *
     * @return boolean
     */
    public function flush();

    /**
     * Delete all indexed data
     *
     * @return boolean
     */
    public function deleteAll();

    /**
     * Checks whether the index client is enabled for the current service
     *
     * @return boolean
     */
    public function isEnabled($clientName);

    /**
     * Checks whether the subtype is indexable.
     *
     * @param  string $serviceName Name of the service
     * @param  string $item        Subtype of item to check
     *
     * @return boolean
     */
    public function isTypeIndexable($serviceName, $itemSubType);

    /**
     * Set service for the current client
     *
     * @param ServiceInterface $service
     */
    public function setService(ServiceInterface $service);

    /**
     * Set item. This method gives the possibility for the indexing client
     * to access extra data in regards to the default indexable content.
     *
     * @param DocumentInterface $item
     */
    public function setItem(DocumentInterface $item);
}
