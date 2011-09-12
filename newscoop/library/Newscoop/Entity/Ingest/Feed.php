<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest;

use Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Entity\Ingest\Feed\Entry;

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
     * Set updated
     *
     * @param DateTime $updated
     * @return Newscoop\Entity\Ingest\Feed
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
        return $this;
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
     * Add entry
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @return void
     */
    public function addEntry(Entry $entry)
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
