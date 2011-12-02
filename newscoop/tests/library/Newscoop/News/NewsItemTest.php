<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class NewsItemTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\News\NewsItem */
    protected $item;

    /** @var SimpleXMLElement */
    protected $xml;

    public function setUp()
    {
        $this->xml = new \SimpleXMLElement('<newsItem guid="testguid"></newsItem>'); 
        $this->item = new NewsItem($this->xml);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\News\NewsItem', $this->item);
    }
}
