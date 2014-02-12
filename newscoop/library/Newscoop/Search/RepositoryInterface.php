<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Search;

/**
 * Repository interface
 *
 * Provides an interface for repositories of which the entities are connected
 * to the  Search service.
 */
interface RepositoryInterface
{
    const BATCH_COUNT = 50;

    /**
     * Get items to process
     *
     * @param int $count Limit amount of results
     * @param array $filter Filter to apply to results
     *
     * @return array
     */
    public function getBatch($count = self::BATCH_COUNT, array $filter=null);

    /**
     * Set indexed to now for given items
     *
     * @param array $items
     * @return void
     */
    public function setIndexedNow(array $items);

    /**
     * Set indexed to null for specified items or all when value is null
     *
     * @param mixed $items
     *
     * @return void
     */
    public function setIndexedNull(array $items=null);
}
