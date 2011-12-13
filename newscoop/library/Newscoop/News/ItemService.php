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
        $this->repository = $this->om->getRepository('Newscoop\News\Item');
    }

    /**
     * Find item by given id
     *
     * @param string $id
     * @return Newscoop\News\Item
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Find items by set of criteria
     *
     * @param array $criteria
     * @param mixed $orderBy
     * @param int $limit
     * @param int $offset
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = 25, $offset = 0)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Save item
     *
     * @param Newscoop\News\Item $item
     * @return void
     */
    public function save(Item $item)
    {
        $persisted = $this->repository->find($item->getId());
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
