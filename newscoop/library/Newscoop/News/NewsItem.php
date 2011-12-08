<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * NewsItem
 * @Document(collection="news_item")
 * @InheritanceType("SINGLE_COLLECTION")
 * @DiscriminatorField(fieldName="type")
 * @DiscriminatorMap({"package"="PackageItem", "news"="NewsItem"})
 */
class NewsItem extends Item
{
    /**
     * @EmbedOne(targetDocument="ContentSet")
     * @var Newscoop\News\ContentSet
     */
    protected $contentSet;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        parent::__construct($xml);
        $this->contentSet = new ContentSet($xml->contentSet);
    }

    /**
     * Get content set
     *
     * @return Newscoop\News\ContentSet
     */
    public function getContentSet()
    {
        return $this->contentSet;
    }
}
