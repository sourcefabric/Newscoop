<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest\Feed;

use Newscoop\Entity\Ingest\Feed,
    Newscoop\Ingest\Parser;

/**
 * @Entity
 * @Table(name="ingest_feed_entry")
 */
class Entry
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Ingest\Feed")
     * @var Newscoop\Entity\Ingest\Feed
     */
    private $feed;

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
     * @Column(type="string", nullable=True)
     * @var string
     */
    private $author;

    /**
     * @Column(type="text")
     * @var string
     */
    private $content;

    /**
     * @Column(type="text", nullable=True)
     * @var string
     */
    private $summary;

    /**
     * @Column(type="string", nullable=True)
     * @var string
     */
    private $category;

    /**
     * @Column(type="datetime")
     * @var DateTime
     */
    private $created;

    /**
     * @Column(type="datetime", nullable=True)
     * @var DateTime
     */
    private $published;

    /**
     * @Column(type="integer", nullable=True)
     * @var int
     */
    private $priority;

    /**
     * @Column(type="string")
     * @var string
     */
    private $service;

    /**
     * @param string $title
     * @param string $content
     */
    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
        $this->created = $this->updated = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param DateTime $published
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function setPublished(\DateTime $published)
    {
        $this->published = $published;
        return $this;
    }

    /**
     * Get published
     *
     * @return DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Test if is published
     *
     * @return bool
     */
    public function isPublished()
    {
        return isset($this->published);
    }

    /**
     * Set created
     *
     * @param DateTime $created
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param DateTime $updated
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Set priority
     *
     * @param int $priority
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function setPriority($priority)
    {
        $this->priority = (int) $priority;
        return $this;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set service
     *
     * @param string $service
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function setService($service)
    {
        $this->service = (string) $service;
        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set feed
     *
     * @param Newscoop\Entity\Ingest\Feed $feed
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function setFeed(Feed $feed)
    {
        $this->feed = $feed;
        return $this;
    }

    /**
     * Entry factory
     *
     * @param Newscoop\Ingest\Parser $parser
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public static function create(Parser $parser)
    {
        $entry = new self($parser->getTitle(), $parser->getContent());
        $entry->setCreated($parser->getCreated());
        $entry->setUpdated($parser->getUpdated());
        $entry->setPriority($parser->getPriority());
        return $entry;
    }
}
