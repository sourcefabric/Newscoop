<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * Item Service
 */
class ItemService
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;

    /**
     * @var Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * @var Doctrine\Common\Persistence\ObjectManager @om
     */
    public function __construct(\Doctrine\Common\Persistence\ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Find feeds by set of criteria
     *
     * @param array $criteria
     * @param mixed $orderBy
     * @param int $limit
     * @param int $offset
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = 25, $offset = 0)
    {
        $qb = $this->om->createQueryBuilder('Newscoop\News\NewsItem');

        $criteria['type'] = array('news', 'package');
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $qb->field($field)->in($value);
            } else {
                $qb->field($field)->equals($value);
            }
        }

        if (is_array($orderBy)) {
            $qb->sort($orderBy);
        }

        return $qb
            ->limit($limit)
            ->skip($offset)
            ->getQuery()
            ->execute();
    }

    /**
     * Save Item
     *
     * @param Newscoop\News\Item $item
     * @return void
     */
    public function save(Item $item)
    {
        $persisted = $this->om->find($item instanceof NewsItem ? 'Newscoop\News\NewsItem' : 'Newscoop\News\PackageItem', $item->getId());
        if ($persisted !== null) {
            if ($item->getVersion() < $persisted->getVersion()) {
                return;
            } else { // @todo handle append signal
                $this->om->remove($persisted);
                $this->om->flush();
            }
        }

        if ($item->isCanceled()) {
            return;
        }

        $this->om->persist($item);
        $this->om->flush();
    }
}
