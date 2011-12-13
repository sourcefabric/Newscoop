<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * anyItem
 * @Document(collection="news_item")
 * @InheritanceType("SINGLE_COLLECTION")
 * @DiscriminatorField(fieldName="type")
 * @DiscriminatorMap({"package"="PackageItem", "news"="NewsItem"})
 */
class Item
{
    /**
     * @Id(strategy="NONE")
     * @var string
     */
    protected $id;

    /**
     * @String
     * @var string
     */
    protected $guid;

    /**
     * @ReferenceOne(targetDocument="ReutersFeed")
     * @var Newscoop\News\ReutersFeed
     */
    protected $feed;

    /**
     * @Int
     * @var string
     */
    protected $version;

    /**
     * @String
     * @var string
     */
    protected $standard;

    /**
     * @String
     * @var string
     */
    protected $standardVersion;

    /**
     * @String
     * @var string
     */
    protected $conformance;

    /**
     * @EmbedMany(targetDocument="RightsInfo")
     * @var Doctrine\Common\Collections\Collection
     */
    protected $rightsInfo;

    /**
     * @EmbedOne(targetDocument="ItemMeta")
     * @var Newscoop\News\ItemMeta
     */
    protected $itemMeta;

    /**
     * @EmbedOne(targetDocument="ContentMeta")
     * @var Newscoop\News\ContentMeta
     */
    protected $contentMeta;

    /**
     * @Date
     * @var DateTime
     */
    protected $created;

    /**
     * @EmbedMany(targetDocument="CatalogRef")
     * @var Doctrine\Common\Collections\Collection
     */
    protected $catalogRefs;

    /**
     * @param string $id
     * @param int $version
     */
    protected function __construct($id, $version = 1)
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
}
