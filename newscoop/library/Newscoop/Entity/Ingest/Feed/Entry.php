<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest\Feed;

use Newscoop\Entity\Ingest\Feed;

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
     * @Column(type="datetime", nullable=True)
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
     * @param string $title
     * @param string $content
     */
    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
        $this->updated = new \DateTime();
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
}
