<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * Feed Service
 */
class FeedService
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
     * @var Newscoop\News\ItemService
     */
    protected $itemService;

    /**
     * @var Doctrine\Common\Persistence\ObjectManager @om
     * @var Newscoop\News\ItemService $itemService
     */
    public function __construct(\Doctrine\Common\Persistence\ObjectManager $om, ItemService $itemService)
    {
        $this->om = $om;
        $this->repository = $this->om->getRepository('Newscoop\News\ReutersFeed');
        $this->itemService = $itemService;
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
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Update all feeds
     *
     * @return void
     */
    public function updateAll()
    {
        foreach ($this->repository->findAll() as $feed) {
            $feed->update($this->om, $this->itemService);
        }
    }

    /**
     * Save feed
     *
     * @param array $values
     * @return Newscoop\News\Feed
     */
    public function save(array $values)
    {
        if (!array_key_exists('type', $values)) {
            throw new \InvalidArgumentException("Feed type not specified");
        }

        $feed = $this->repository->findBy($values);
        if (count($feed)) {
            return $feed[0];
        }

        switch ($values['type']) {
            case 'reuters':
                $feed = new ReutersFeed($values['config']);
                break;

            default:
                throw new \InvalidArgumentException("Feed type '$values[type]' not implemented");
        }

        $this->om->persist($feed);
        $this->om->flush();

        return $feed;
    }
}
