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
     * @Column(type="datetime")
     * @var DateTime
     */
    private $updated;

    /**
     * @OneToMany(targetEntity="Newscoop\Entity\Ingest\Feed\Entry", mappedBy="feed", cascade={"persist"})
     * @var array
     */
    private $entries;

    /**
     * @var string
     * @todo use some file manager
     */
    public $path = '/../tests/ingest';

    /**
     * @param string $title
     */
    public function __construct($title)
    {
        $this->title = $title;
        $this->updated = new \DateTime();
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
     * Update feed
     *
     * @return void
     */
    public function update()
    {
        foreach (glob(APPLICATION_PATH . $this->path . '/*.xml') as $file) {
            if (strpos($file, '_phd') !== false) {
                continue;
            }

            // @todo lock file
            $parser = new NewsMlParser($file);
            $entry = Entry::create($parser);
            $this->addEntry($entry);
            // @todo unlock file
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
