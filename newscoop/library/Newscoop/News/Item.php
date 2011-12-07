<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * anyItem
 * @MappedSuperclass
 */
abstract class Item
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
     * @String
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
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        if (empty($xml['guid'])) {
            throw new \InvalidArgumentException("Guid can't be empty");
        }

        $this->id = (string) $xml['guid'];
        $this->version =  isset($xml['version']) ? (string) $xml['version'] : '1';
        $this->standard = (string) $xml['standard'];
        $this->standardVersion = (string) $xml['standardversion'];
        $this->conformance = isset($xml['conformance']) ? (string) $xml['conformance'] : 'core';
        $this->created = new \DateTime();

        $this->setRightsInfo($xml);
        $this->itemMeta = new ItemMeta($xml->itemMeta);
        $this->contentMeta = new ContentMeta($xml->contentMeta);
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
     * Get version
     *
     * @return string
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
            $this->rightsInfo->add(new RightsInfo($rightsInfoXml));
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
}
