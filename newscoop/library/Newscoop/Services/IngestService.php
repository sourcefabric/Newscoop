<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\Ingest\Feed,
    Newscoop\Entity\Ingest\Feed\Entry,
    Newscoop\Ingest\Parser\NewsMlParser,
    Newscoop\Ingest\Publisher,
    Newscoop\Services\Ingest\PublisherService;

/**
 * Ingest service
 */
class IngestService
{
    const IMPORT_DELAY = 180;

    /** @var array */
    private $config = array();

    /** Doctrine\ORM\EntityManager */
    private $em;

    /** @var Newscoop\Services\Ingest\PublisherService */
    private $publisher;

    /** @var array */
    private $feeds;

    /**
     * @param array $config
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct($config, EntityManager $em, PublisherService $publisher)
    {
        $this->config = $config;
        $this->em = $em;
        $this->publisher = $publisher;
    }

    /**
     * Add feed
     *
     * @param Newscoop\Entity\Ingest\Feed $feed
     * @return void
     */
    public function addFeed(Feed $feed)
    {
        $this->em->persist($feed);
        $this->em->flush();
        $this->feeds = null;
    }

    /**
     * Get feeds
     *
     * @return array
     */
    public function getFeeds()
    {
        if ($this->feeds === null) {
            $this->feeds = $this->em->getRepository('Newscoop\Entity\Ingest\Feed')
                ->findAll();
        }

        return $this->feeds;
    }

    /**
     * Update all feeds
     *
     * @return void
     */
    public function updateAll()
    {
        foreach ($this->getFeeds() as $feed) {
            $this->update($feed);
        }

        $this->em->flush();
    }

    /**
     * Update feed
     *
     * @param Newscoop\Entity\Ingest\Feed $feed
     * @return void
     */
    private function update(Feed $feed)
    {
        foreach (glob($this->config['path'] . '/*.xml') as $file) {
            if (strpos($file, '_phd') !== false) {
                continue;
            }

            if ($feed->getUpdated() && $feed->getUpdated()->getTimestamp() > filemtime($file)) {
                continue;
            }

            if (time() < filemtime($file) + self::IMPORT_DELAY) {
                continue;
            }

            $handle = fopen($file, 'r');
            if (flock($handle, LOCK_EX | LOCK_NB)) {
                $parser = new NewsMlParser($file);
                $entry = Entry::create($parser);
                $feed->addEntry($entry);
                flock($handle, LOCK_UN);
                fclose($handle);
            } else {
                continue;
            }
        }

        $feed->setUpdated(new \DateTime());
        $this->em->persist($feed);
    }

    /**
     * Find by id
     *
     * @param int $id
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function find($id)
    {
        return $this->getEntryRepository()
            ->find($id);
    }

    /**
     * Find by given criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = array(), $limit = 25, $offset = 0)
    {
        return $this->getEntryRepository()
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Publish entry
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return void
     */
    public function publish(Entry $entry)
    {
        $this->publisher->publish($entry);
        $entry->setPublished(new \DateTime());
        $this->em->persist($entry);
        $this->em->flush();
    }

    /**
     * Get feed entry repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getEntryRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Ingest\Feed\Entry');
    }
}
