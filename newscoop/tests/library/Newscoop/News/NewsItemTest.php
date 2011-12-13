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
    const TEXT_XML = 'textNewsItem.xml';
    const UPDATED_XML = 'updatedTextNewsItem.xml';
    const PICTURE_XML = 'pictureNewsItem.xml';

    /** @var Newscoop\News\NewsItem */
    protected $item;

    /** @var SimpleXMLElement */
    protected $xml;

    public function setUp()
    {
        $this->xml = simplexml_load_file(APPLICATION_PATH . '/../tests/fixtures/' . self::TEXT_XML);
        $this->item = NewsItem::createFromXml($this->xml->itemSet->newsItem);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\News\NewsItem', $this->item);
    }

    public function testGetId()
    {
        $this->assertEquals('tag:example.com,0000:newsml_TRE7B30AO', $this->item->getId());
    }

    public function testGetVersion()
    {
        $this->assertEquals(1827540795, $this->item->getVersion());
    }

    public function testGetStandard()
    {
        $this->assertEquals('NewsML-G2', $this->item->getStandard());
        $this->assertEquals('2.9', $this->item->getStandardVersion());
    }

    public function testGetConformance()
    {
        $this->assertEquals('power', $this->item->getConformance());
    }

    public function testGetCreated()
    {
        $this->assertInstanceOf('DateTime', $this->item->getCreated());
    }

    public function testGetRightsInfo()
    {
        $rightsInfo = $this->item->getRightsInfo();
        $this->assertEquals(1, count($rightsInfo));
        $this->assertInstanceOf('Newscoop\News\RightsInfo', $rightsInfo[0]);
        $this->assertEquals('Foo Bar', $rightsInfo[0]->getCopyrightHolder());
        $this->assertEquals('(c) Copyright Foo Bar 2011.', $rightsInfo[0]->getCopyrightNotice());
    }

    public function testGetCatalogs()
    {
        $catalogRefs = $this->item->getCatalogRefs();
        $this->assertEquals(1, count($catalogRefs));
        $this->assertInstanceOf('Newscoop\News\CatalogRef', $catalogRefs[0]);
        $this->assertEquals('http://www.iptc.org/std/catalog/catalog.IPTC-G2-Standards_3.xml', $catalogRefs[0]->getHref());
    }

    public function testGetItemMeta()
    {
        $itemMeta = $this->item->getItemMeta();
        $this->assertInstanceOf('Newscoop\News\ItemMeta', $itemMeta);
        $this->assertEquals('icls:text', $itemMeta->getItemClass());
        $this->assertEquals('example.com', $itemMeta->getProvider());
        $this->assertEquals(date_create('2011-12-06T09:14:50.000Z')->getTimestamp(), $itemMeta->getVersionCreated()->getTimestamp());
        $this->assertEquals(date_create('2011-12-04T13:13:31.000Z')->getTimestamp(), $itemMeta->getFirstCreated()->getTimestamp());
        $this->assertEquals('stat:usable', $itemMeta->getPubStatus());
        $this->assertEquals('itemRole:N', $itemMeta->getRole());
        $this->assertEquals('S&P piles pressure on Franco-German EU budget plan', $itemMeta->getTitle());
    }

    public function testGetContentMeta()
    {
        $contentMeta = $this->item->getContentMeta();
        $this->assertInstanceOf('Newscoop\News\ContentMeta', $contentMeta);
        $this->assertEquals('4', $contentMeta->getUrgency());
        $this->assertEquals('US-EUROZONE', $contentMeta->getSlugline());
        $this->assertEquals('S&P piles pressure on Franco-German EU budget plan', $contentMeta->getHeadline());
        $this->assertEquals('2011-12-06 09:14:50 GMT+00:00', $contentMeta->getDateline());
        $this->assertEquals('Foo Bar and John Doe', $contentMeta->getBy());
        $this->assertEquals('Example creditline', $contentMeta->getCreditline());
        $this->assertEquals('US-EUROZONE:S&P piles pressure on Franco-German EU budget plan', $contentMeta->getDescription());

        $subjects = $contentMeta->getSubjects();
        $this->assertEquals(7, count($subjects));
        $this->assertEquals('MCC:a', $subjects[0]->getQCode());
        $this->assertNull($subjects[0]->getType());
        $this->assertNull($subjects[0]->getName());
        $this->assertEquals('cptType:5', $subjects[6]->getType());
        $this->assertEquals('testSub', $subjects[6]->getName());
    }

    public function testGetContentSet()
    {
        $contentSet = $this->item->getContentSet();
        $this->assertInstanceOf('Newscoop\News\ContentSet', $contentSet);
        $this->assertContains("<p>Content text</p>", $contentSet->getInlineContent());
        $this->assertContains("<p>Next paragraph</p>", $contentSet->getInlineContent());
    }

    public function testGetContentSetRemoteContent()
    {
        $xml = simplexml_load_file(APPLICATION_PATH . '/../tests/fixtures/' . self::PICTURE_XML);
        $item = NewsItem::createFromXml($xml->itemSet->newsItem);
        $contentSet = $item->getContentSet();
        $remoteContent = $contentSet->getRemoteContent();
        $this->assertEquals(3, count($remoteContent));
        $this->assertInstanceOf('Newscoop\News\RemoteContent', $remoteContent[0]);
        $this->assertEquals('tag:example.com,0000:binary_LM1E7C611BX01-BASEIMAGE', $remoteContent[0]->getResidref());
        $this->assertEquals('http://content.example.com/auth-server/content/tag:example.com,0000:newsml_LM1E7C611BX01:1536672970/tag:example.com,0000:binary_LM1E7C611BX01-BASEIMAGE', $remoteContent[0]->getHref());
        $this->assertEquals(827687, $remoteContent[0]->getSize());
        $this->assertEquals('rend:baseImage', $remoteContent[0]->getRendition());
        $this->assertEquals('image/jpeg', $remoteContent[0]->getContentType());
        $this->assertEquals('fmt:jpegBaseline', $remoteContent[0]->getFormat());
        $this->assertEquals('G3:IIM:FH:BaseImageGenerator', $remoteContent[0]->getGenerator());
        $this->assertEquals(2313, $remoteContent[0]->getWidth());
        $this->assertEquals(3500, $remoteContent[0]->getHeight());
        $this->assertEquals(17478, $contentSet->getRemoteContent('rend:thumbnail')->getSize());
    }
}
