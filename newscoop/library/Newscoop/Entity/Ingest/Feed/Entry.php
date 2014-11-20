<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest\Feed;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Entity\Ingest\Feed;
use Newscoop\Ingest\Parser;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\Ingest\Feed\EntryRepository")
 * @ORM\Table(name="ingest_feed_entry")
 */
class Entry
{
    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Ingest\Feed", inversedBy="entries")
     * @var Newscoop\Entity\Ingest\Feed
     */
    protected $feed;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $updated;

    /**
     * @ORM\Column(type="string", nullable=True)
     * @var string
     */
    protected $author;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $content;

    /**
     * @ORM\Column(type="text", nullable=True)
     * @var string
     */
    protected $summary;

    /**
     * @ORM\Column(type="string", nullable=True)
     * @var string
     */
    protected $category;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=True)
     * @var DateTime
     */
    protected $published;

    /**
     * @ORM\Column(type="datetime", nullable=True)
     * @var DateTime
     */
    protected $embargoed;

    /**
     * @ORM\Column(type="string", nullable=True)
     * @var string
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", nullable=True)
     * @var int
     */
    protected $priority;

    /**
     * @ORM\Column(type="string", nullable=True)
     * @var string
     */
    protected $date_id;

    /**
     * @ORM\Column(type="string", nullable=True)
     * @var string
     */
    protected $news_item_id;

    /**
     * @ORM\Column(type="array", nullable=True)
     * @var array
     */
    protected $attributes = array();

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
     * @param \DateTime $published
     * @return Entry
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
     * Get feed
     *
     * @return Newscoop\Entity\Ingest\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Set feed
     *
     * @param  $feed
     * @return Entry
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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get embargoed
     *
     * @return DateTime
     */
    public function getEmbargoed()
    {
        return $this->embargoed;
    }

    /**
     * Set article number
     *
     * @param int $articleNumber
     * @return Entry
     */
    public function setArticleNumber($articleNumber)
    {
        $this->setAttribute('article_number', (int) $articleNumber);
        return $this;
    }

    /**
     * Get article number
     *
     * @return int
     */
    public function getArticleNumber()
    {
        return $this->getAttribute('article_number');
    }

    /**
     * Update entry
     *
     * @param Parser $parser
     * @return Entry
     */
    public function update(Parser $parser)
    {
        $this->updated = $parser->getUpdated();
        $this->title = $parser->getTitle();
        $this->content = $parser->getContent();
        $this->priority = $parser->getPriority();
        $this->summary = (string) $parser->getSummary();
        $this->status = (string) $parser->getStatus();
        $this->embargoed = $parser->getLiftEmbargo();
        self::setAttributes($this, $parser);
        self::setImages($this, $parser);
        return $this;
    }

    /**
     * Entry factory
     *
     * @param Parser $parser
     * @return Entry
     */
    public static function create(Parser $parser)
    {
        $entry = new self($parser->getTitle(), $parser->getContent());
        $entry->created = $parser->getCreated() ?: $entry->created;
        $entry->updated = $parser->getUpdated() ?: $entry->updated;
        $entry->priority = (int) $parser->getPriority();
        $entry->summary = (string) $parser->getSummary();
        $entry->date_id = (string) $parser->getDateId();
        $entry->news_item_id = (string) $parser->getNewsItemId();
        $entry->status = (string) $parser->getStatus();
        $entry->embargoed = $parser->getLiftEmbargo();
        self::setAttributes($entry, $parser);
        self::setImages($entry, $parser);
        return $entry;
    }

    /**
     * Set entry attributes
     *
     * @param Entry $entry
     * @param Parser $parser
     */
    private static function setAttributes(self $entry, Parser $parser)
    {
        $entry->setAttribute('service', (string) $parser->getService());
        $entry->setAttribute('language', (string) $parser->getLanguage());
        $entry->setAttribute('subject', (string) $parser->getSubject());
        $entry->setAttribute('country', (string) $parser->getCountry());
        $entry->setAttribute('product', (string) $parser->getProduct());
        $entry->setAttribute('subtitle', (string) $parser->getSubtitle());
        $entry->setAttribute('provider_id', (string) $parser->getProviderId());
        $entry->setAttribute('revision_id', (string) $parser->getRevisionId());
        $entry->setAttribute('location', (string) $parser->getLocation());
        $entry->setAttribute('provider', (string) $parser->getProvider());
        $entry->setAttribute('source', (string) $parser->getSource());
        $entry->setAttribute('catch_line', (string) $parser->getCatchLine());
        $entry->setAttribute('catch_word', (string) $parser->getCatchWord());
        $entry->setAttribute('authors', (string) $parser->getAuthors());
    }

    /**
     * Set entry images
     *
     * @param Entry $entry
     * @param Parser $parser
     */
    private static function setImages(self $entry, Parser $parser)
    {
        $images = array();
        $parserImages = $parser->getImages();
        if (is_array($parserImages)) {
            foreach ($parserImages as $image) {
                $images[basename($image->getPath())] = $image->getTitle();
            }
        }

        $entry->setAttribute('images', $images);
    }


    /**
     * Set attribute
     *
     * @param string $name
     * @param integer $value
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
