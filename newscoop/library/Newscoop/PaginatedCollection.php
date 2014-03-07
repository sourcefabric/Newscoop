<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use IteratorAggregate;
use ArrayIterator;
use Doctrine\ORM\AbstractQuery;

/**
 */
class PaginatedCollection implements IteratorAggregate
{
    /**
     * @var int
     */
    public $prevPageOffset;

    /**
     * @var int
     */
    public $nextPageOffset;

    /**
     * @var int
     */
    public $currentPageOffset;

    /**
     * @var Traversable
     */
    protected $result;

    /**
     * @param Doctrine\ORM\AbstractQuery $query
     */
    public function __construct(AbstractQuery $query)
    {
        $this->result = $query->setMaxResults($query->getMaxResults() + 1)->getResult();
        $this->prevPageOffset = $query->getFirstResult() ? max(0, $query->getFirstResult() - $query->getMaxResults() + 1) : null;
        $this->nextPageOffset = count($this->result) === $query->getMaxResults() ? $query->getMaxResults() + $query->getFirstResult() - 1 : null;
        $this->currentPageOffset = $query->getFirstResult();
        if ($this->nextPageOffset) {
            array_pop($this->result);
        }
    }

    /**
     * Get iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->result);
    }
}
