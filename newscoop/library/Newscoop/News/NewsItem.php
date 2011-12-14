<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * NewsItem
 * @Document
 */
class NewsItem extends Item
{
    /**
     * @EmbedOne(targetDocument="ContentSet")
     * @var Newscoop\News\ContentSet
     */
    protected $contentSet;

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\NewsItem
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $item = parent::createFromXml($xml);
        $item->contentSet = ContentSet::createFromXml($xml->contentSet);
        return $item;
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
