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
class NewsItem
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
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = (string) $id;
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
     * Get Feed
     *
     * @return Newscoop\News\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Create NewsItem from given xml
     *
     * @param SimpleXmlElement $xml
     * @return Newscoop\News\NewsItem
     */
    public static function createFromXml(\SimpleXmlElement $xml)
    {
        $item = new NewsItem($xml['guid']);
        return $item;
    }
}
