<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\Ingest\Feed,
    Doctrine\ORM\EntityManager;

/**
 * Ingest service
 */
class IngestService
{
    /** @var array */
    private $config = array();

    /** Doctrine\ORM\EntityManager */
    private $em;

    /** @var array */
    private $feeds;

    /**
     * @param array $config
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct($config, EntityManager $em)
    {
        $this->config = $config;
        $this->em = $em;
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
            $feed->update();
            $this->em->persist($feed);
        }

        $this->em->flush();
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
        return $this->em->getRepository('Newscoop\Entity\Ingest\Feed\Entry')
            ->findBy($criteria, $orderBy, $limit, $offset);
    }
}
