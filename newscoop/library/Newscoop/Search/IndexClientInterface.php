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
use Newscoop\Search\QueryInterface;

/**
 * Index Interface
 */
interface IndexClientInterface
{
    /**
     * Initialize class
     *
     * @return void
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
     * Find article numbers for given query
     *
     * @param QueryInterface $query
     * @return object
     */
    public function find(QueryInterface $query);

    /**
     * Set service for
     *
     * @param ServiceInterface $service
     * @return void
     */
    public function setService(ServiceInterface $service);

    /**
     * Set item. This method gives the possibility for the indexing client
     * to access extra data in regards to the default indexable content;
     *
     * @param DocumentInterface $item
     * @return void
     */
    public function setItem(DocumentInterface $item);
}
