<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * anyItem
 * @ODM\Document(collection="news_item")
 * @ODM\InheritanceType("SINGLE_COLLECTION")
 * @ODM\DiscriminatorField(fieldName="type")
 * @ODM\DiscriminatorMap({"package"="PackageItem", "news"="NewsItem"})
 */
abstract class Item
{
    /**
     * @ODM\Id(strategy="NONE")
     * @var string
     */
    protected $id;

    /**
     * @ODM\String
     * @var string
     */
    protected $guid;

    /**
     * @ODM\ReferenceOne(targetDocument="ReutersFeed")
     * @var Newscoop\News\ReutersFeed
     */
    protected $feed;

    /**
     * @ODM\Int
     * @var string
     */
    protected $version;

    /**
     * @ODM\String
     * @var string
     */
    protected $standard;

    /**
     * @ODM\String
     * @var string
     */
    protected $standardVersion;

    /**
     * @ODM\String
     * @var string
     */
    protected $conformance;

    /**
     * @ODM\EmbedMany(targetDocument="RightsInfo")
     * @var Doctrine\Common\Collections\Collection
     */
    protected $rightsInfo;

    /**
     * @ODM\EmbedOne(targetDocument="ItemMeta")
     * @var Newscoop\News\ItemMeta
     */
    protected $itemMeta;

    /**
     * @ODM\EmbedOne(targetDocument="ContentMeta")
     * @var Newscoop\News\ContentMeta
     */
    protected $contentMeta;

    /**
     * @ODM\Date
     * @var DateTime
     */
    protected $created;

    /**
     * @ODM\EmbedMany(targetDocument="CatalogRef")
     * @var Doctrine\Common\Collections\Collection
     */
    protected $catalogRefs;

    /**
     * @ODM\Date
     * @var DateTime
     */
    protected $published;

    /**
     * @param string $id
     * @param int $version
     */
    public function __construct($id, $version = 1)
    {
        $this->id = (string) $id;
        $this->version = max(1, (int) $version);
    }

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\Item
     */
    protected static function createFromXml(\SimpleXMLElement $xml)
    {
        if (empty($xml['guid'])) {
            throw new \InvalidArgumentException("Guid can't be empty");
        }

        $item = new static($xml['guid'], $xml['version']);

        $item->standard = (string) $xml['standard'];
        $item->standardVersion = (string) $xml['standardversion'];
        $item->conformance = isset($xml['conformance']) ? (string) $xml['conformance'] : 'core';
        $item->created = new \DateTime();

        $item->setRightsInfo($xml);
        $item->itemMeta = ItemMeta::createFromXml($xml->itemMeta);
        $item->contentMeta = ContentMeta::createFromXml($xml->contentMeta);
        $item->setCatalogRefs($xml);
        return $item;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set feed
     *
     * @param Newscoop\News\Feed $feed
     * @return void
     */
    public function setFeed(Feed $feed)
    {
        $this->feed = $feed;
    }

    /**
     * Get feed
     *
     * @return Newscoop\News\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Get version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get standard
     *
     * @return string
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Get standard version
     *
     * @return string
     */
    public function getStandardVersion()
    {
        return $this->standardVersion;
    }

    /**
     * Get conformance
     *
     * @return string
     */
    public function getConformance()
    {
        return $this->conformance;
    }

    /**
     * Set rights info
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    private function setRightsInfo(\SimpleXMLElement $xml)
    {
        $this->rightsInfo = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($xml->rightsInfo as $rightsInfoXml) {
            $this->rightsInfo->add(RightsInfo::createFromXml($rightsInfoXml));
        }
    }

    /**
     * Get rights info
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRightsInfo()
    {
        return $this->rightsInfo;
    }

    /**
     * Set item meta
     *
     * @param Newscoop\News\ItemMeta $itemMeta
     * @return void
     */
    public function setItemMeta(ItemMeta $itemMeta)
    {
        $this->itemMeta = $itemMeta;
    }

    /**
     * Get item meta
     *
     * @return Newscoop\News\ItemMeta
     */
    public function getItemMeta()
    {
        return $this->itemMeta;
    }

    /**
     * Get content meta
     *
     * @return Newscoop\News\ContentMeta
     */
    public function getContentMeta()
    {
        return $this->contentMeta;
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
     * Test if is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return isset($this->itemMeta) && $this->itemMeta->getPubStatus() === ItemMeta::STATUS_CANCELED;
    }

    /**
     * Set catalog refs
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    public function setCatalogRefs(\SimpleXMLElement $xml)
    {
        $this->catalogRefs = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($xml->catalogRef as $catalogRefXml) {
            $this->catalogRefs->add(new CatalogRef($catalogRefXml['href']));
        }
    }

    /**
     * Get catalog references
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCatalogRefs()
    {
        return $this->catalogRefs;
    }

    /**
     * Set published
     *
     * @param DateTime $published
     * @return void
     */
    public function setPublished(\DateTime $published)
    {
        $this->published = $published;
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
        return $this->published !== null;
    }
}
