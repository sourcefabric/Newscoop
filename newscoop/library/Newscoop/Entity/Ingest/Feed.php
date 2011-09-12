<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Entity\Ingest\Feed\Entry,
    Newscoop\Ingest\Parser\NewsMlParser;

/**
 * @Entity
 * @Table(name="ingest_feed")
 */
class Feed
{
    const TIME_DELAY = 180;

    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string")
     * @var string
     */
    private $title;

    /**
     * @Column(type="datetime", nullable=True)
     * @var DateTime
     */
    private $updated;

    /**
     * @OneToMany(targetEntity="Newscoop\Entity\Ingest\Feed\Entry", mappedBy="feed", cascade={"persist"})
     * @var array
     */
    private $entries;

    /**
     * @var array
     */
    public $config;

    /**
     * @param string $title
     */
    public function __construct($title)
    {
        $this->title = $title;
        $this->entries = new ArrayCollection();
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set config
     *
     * @param array $config
     * @return Newscoop\Entity\Ingest\Feed
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Update feed
     *
     * @return void
     */
    public function update()
    {
        foreach (glob($this->config['path'] . '/*.xml') as $file) {
            if (strpos($file, '_phd') !== false) {
                continue;
            }

            if ($this->updated && $this->updated->getTimestamp() > filemtime($file)) {
                continue;
            }

            if (time() < filemtime($file) + self::TIME_DELAY) {
                continue;
            }

            $handle = fopen($file, 'r');
            if (flock($handle, LOCK_EX | LOCK_NB)) {
                $parser = new NewsMlParser($file);
                $entry = Entry::create($parser);
                $this->addEntry($entry);
                flock($handle, LOCK_UN);
                fclose($handle);
            } else {
                continue;
            }
        }

        $this->updated = new \DateTime();
    }

    /**
     * Add entry
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return void
     */
    private function addEntry(Entry $entry)
    {
        $this->entries->add($entry);
        $entry->setFeed($this);
    }

    /**
     * Get entries
     *
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }
}
