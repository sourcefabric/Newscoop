<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * PackageItem
 * @Document(collection="news_item")
 * @InheritanceType("SINGLE_COLLECTION")
 * @DiscriminatorField(fieldName="type")
 * @DiscriminatorMap({"package"="PackageItem", "news"="NewsItem"})
 */
class PackageItem extends Item
{
    /**
     * @EmbedOne(targetDocument="GroupSet")
     * @var Newscoop\News\GroupSet
     */
    protected $groupSet;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        parent::__construct($xml);
        $this->groupSet = new GroupSet($xml->groupSet);
    }

    /**
     * Get group set
     *
     * @return Newscoop\News\GroupSet
     */
    public function getGroupSet()
    {
        return $this->groupSet;
    }
}
