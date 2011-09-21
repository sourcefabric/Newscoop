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
    Newscoop\Ingest\Parser,
    Newscoop\Ingest\Parser\NewsMlParser,
    Newscoop\Ingest\Publisher,
    Newscoop\Services\Ingest\PublisherService;

/**
 * Ingest service
 */
class IngestService
{
    const IMPORT_DELAY = 180;
    const MODE_SETTING = 'IngestAutoMode';

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
            $this->updateFeed($feed);
        }

        $this->getEntryRepository()->liftEmbargo();
        $this->em->flush();
    }

    /**
     * Update feed
     *
     * @param Newscoop\Entity\Ingest\Feed $feed
     * @return void
     */
    private function updateFeed(Feed $feed)
    {
        foreach (glob($this->config['path'] . '/*.xml') as $file) {
            if ($feed->getUpdated() && $feed->getUpdated()->getTimestamp() > filectime($file) + self::IMPORT_DELAY) {
                continue;
            }

            if (time() < filectime($file) + self::IMPORT_DELAY) {
                continue;
            }

            $handle = fopen($file, 'r');
            if (flock($handle, LOCK_EX | LOCK_NB)) {
                $parser = new NewsMlParser($file);
                if (!$parser->isImage()) {
                    if ($parser->getRevisionId() > 1) {
                        $entry = $this->getPrevious($parser, $feed);
                        switch ($parser->getInstruction()) {
                            case 'Rectify':
                            case 'Update':
                                $entry->update($parser);
                                $this->updatePublished($entry);
                                $this->em->persist($entry);
                                break;

                            case 'Delete':
                                $this->deletePublished($entry);
                                $feed->removeEntry($entry);
                                $this->em->remove($entry);
                                break;

                            default:
                                throw new \InvalidArgumentException("Instruction '{$parser->getInstruction()}' not implemented.");
                                break;
                        }
                    } else {
                        $entry = $this->getPrevious($parser, $feed);
                        if ($this->isAutoMode()) {
                            $this->publish($entry);
                        }
                        $this->em->persist($entry);
                    }
                }

                flock($handle, LOCK_UN);
                fclose($handle);
                $this->em->flush();
            } else {
                continue;
            }
        }

        $feed->setUpdated(new \DateTime());
        $this->em->persist($feed);
    }

    /**
     * Get previous version of entry
     *
     * @param Newscoop\Ingest\Parser $parser
     * @param Newscoop\Entity\Ingest\Feed $feed
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function getPrevious(Parser $parser, Feed $feed)
    {
        $previous = array_shift($this->getEntryRepository()->findBy(array(
            'date_id' => $parser->getDateId(),
            'news_item_id' => $parser->getNewsItemId(),
        )));

        if (empty($previous)) {
            $previous = Entry::create($parser);
            $feed->addEntry($previous);
        }

        return $previous;
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
     * Test if mode is automatic
     *
     * @return bool
     */
    public function isAutoMode()
    {
        return (bool) \SystemPref::Get(self::MODE_SETTING);
    }

    /**
     * Switch mode
     *
     * @return void
     */
    public function switchAutoMode()
    {
        \SystemPref::Set(self::MODE_SETTING, !\SystemPref::Get(self::MODE_SETTING));
    }

    /**
     * Publish entry
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @param string $workflow
     * @return Article
     */
    public function publish(Entry $entry, $workflow = 'Y')
    {
        $article = $this->publisher->publish($entry, $workflow);
        $entry->setPublished(new \DateTime());
        $this->em->persist($entry);
        $this->em->flush();
        return $article;
    }

    /**
     * Updated published entry
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return void
     */
    private function updatePublished(Entry $entry)
    {
        $this->publisher->update($entry);
    }

    /**
     * Delete published entry
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return void
     */
    private function deletePublished(Entry $entry)
    {
        $this->publisher->delete($entry);
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
