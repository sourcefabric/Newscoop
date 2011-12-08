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
     * @param mixed $limit
     * @param mixed $offset
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->om->getRepository('Newscoop\News\NewsItem')
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Save Item
     *
     * @param Newscoop\News\Item $item
     * @return void
     */
    public function save(Item $item)
    {
        if ($item instanceof NewsItem) {
            $persisted = $this->om->find('Newscoop\News\NewsItem', $item->getId());
        } else {
            $persisted = $this->om->find('Newscoop\News\PackageItem', $item->getId());
        }

        if ($persisted !== null) {
            if ($persisted->getVersion() >= $item->getVersion()) {
                return;
            } else {
                $this->om->remove($persisted);
            }
        }

        $this->om->persist($item);
        $this->om->flush();
    }
}
