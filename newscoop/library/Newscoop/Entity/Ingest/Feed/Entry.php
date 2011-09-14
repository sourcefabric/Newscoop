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
     * @Column(type="string", nullable=True)
     * @var string
     */
    private $date_id;

    /**
     * @Column(type="string", nullable=True)
     * @var string
     */
    private $news_item_id;

    /**
     * @Column(type="array", nullable=True)
     * @var array
     */
    private $attributes = array();

    /**
     * @param string $title
     * @param string $content
     */
    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
        $this->created = new \DateTime();
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
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
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
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
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
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->getAttribute('service');
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Get language code
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->getAttribute('language');
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->getAttribute('subject');
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getAttribute('country');
    }

    /**
     * Get product
     *
     * @return string
     */
    public function getProduct()
    {
        return $this->getAttribute('product');
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->getAttribute('subtitle');
    }

    /**
     * Get provider id
     *
     * @return string
     */
    public function getProviderId()
    {
        return $this->getAttribute('provider_id');
    }

    /**
     * Get date id
     *
     * @return string
     */
    public function getDateId()
    {
        return $this->date_id;
    }

    /**
     * Get news item id
     *
     * @return string
     */
    public function getNewsItemId()
    {
        return $this->news_item_id;
    }

    /**
     * Get revision id
     *
     * @return string
     */
    public function getRevisionId()
    {
        return $this->getAttribute('revision_id');
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->getAttribute('location');
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->getAttribute('provider');
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->getAttribute('source');
    }

    /**
     * Get catch line
     *
     * @return string
     */
    public function getCatchLine()
    {
        return $this->getAttribute('catch_line');
    }

    /**
     * Get catch word
     *
     * @return string
     */
    public function getCatchWord()
    {
        return $this->getAttribute('catch_word');
    }

    /**
     * Get authors
     *
     * @return string
     */
    public function getAuthors()
    {
        return $this->getAttribute('authors');
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        return $this->getAttribute('images');
    }

    /**
     * Update entry
     *
     * @param Newscoop\Ingest\Parser $parser
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    public function update(Parser $parser)
    {
        $this->updated = $parser->getUpdated();
        $this->title = $parser->getTitle();
        $this->content = $parser->getContent();
        $this->priority = $parser->getPriority();
        $this->summary = (string) $parser->getSummary();
        $this->setAttribute('revision_id', (string) $parser->getRevisionId());
        $this->setAttribute('service', (string) $parser->getService());
        $this->setAttribute('language', (string) $parser->getLanguage());
        $this->setAttribute('subject', (string) $parser->getSubject());
        $this->setAttribute('country', (string) $parser->getCountry());
        $this->setAttribute('product', (string) $parser->getProduct());
        $this->setAttribute('subtitle', (string) $parser->getSubtitle());
        $this->setAttribute('provider_id', (string) $parser->getProviderId());
        $this->setAttribute('revision_id', (string) $parser->getRevisionId());
        $this->setAttribute('location', (string) $parser->getLocation());
        $this->setAttribute('provider', (string) $parser->getProvider());
        $this->setAttribute('source', (string) $parser->getSource());
        $this->setAttribute('catch_line', (string) $parser->getCatchLine());
        $this->setAttribute('catch_word', (string) $parser->getCatchWord());
        $this->setAttribute('authors', (string) $parser->getAuthors());
        self::setImages($this, $parser);
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
        $entry->created = $parser->getCreated() ?: $entry->created;
        $entry->updated = $parser->getUpdated() ?: $entry->updated;
        $entry->priority = (int) $parser->getPriority();
        $entry->summary = (string) $parser->getSummary();
        $entry->setAttribute('service', (string) $parser->getService());
        $entry->setAttribute('language', (string) $parser->getLanguage());
        $entry->setAttribute('subject', (string) $parser->getSubject());
        $entry->setAttribute('country', (string) $parser->getCountry());
        $entry->setAttribute('product', (string) $parser->getProduct());
        $entry->setAttribute('subtitle', (string) $parser->getSubtitle());
        $entry->setAttribute('provider_id', (string) $parser->getProviderId());
        $entry->date_id = (string) $parser->getDateId();
        $entry->news_item_id = (string) $parser->getNewsItemId();
        $entry->setAttribute('revision_id', (string) $parser->getRevisionId());
        $entry->setAttribute('location', (string) $parser->getLocation());
        $entry->setAttribute('provider', (string) $parser->getProvider());
        $entry->setAttribute('source', (string) $parser->getSource());
        $entry->setAttribute('catch_line', (string) $parser->getCatchLine());
        $entry->setAttribute('catch_word', (string) $parser->getCatchWord());
        $entry->setAttribute('authors', (string) $parser->getAuthors());
        self::setImages($entry, $parser);
        return $entry;
    }

    /**
     * Set entry images
     *
     * @param Newscoop\Entity\Ingest\Feed\Entry $entry
     * @param Newscoop\Ingest\Parser $parser
     */
    private static function setImages(self $entry, Parser $parser)
    {
        $images = array();
        $parserImages = $parser->getImages();
        if (is_array($parserImages)) {
            foreach ($parserImages as $image) {
                $images[] = basename($image->getPath());
            }
        }

        $entry->setAttribute('images', $images);
    }


    /**
     * Set attribute
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    private function setAttribute($name, $value)
    {
        if (!is_array($this->attributes)) {
            $this->attributes = array();
        }

        $this->attributes[$name] = $value;
    }

    /**
     * Get attribute
     *
     * @param string $name
     * @return mixed
     */
    private function getAttribute($name)
    {
        if (!is_array($this->attributes)) {
            $this->attributes = array();
        }

        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : null;
    }
}
