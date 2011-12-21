<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class SettingsTest extends \TestCase
{
    /** @var Doctrine\ODM\MongoDB\DocumentManager */
    protected $odm;

    /** @var Newscoop\News\SettingsService */
    protected $service;

    public function setUp()
    {
        $this->odm = $this->setUpOdm();
        $this->service = new SettingsService($this->odm);
    }

    public function tearDown()
    {
        $this->tearDownOdm($this->odm);
    }

    public function testFind()
    {
        $settings = $this->service->find('ingest');
        $this->assertInstanceOf('Newscoop\News\Settings', $settings);
    }

    public function testGetDefaults()
    {
        $settings = $this->service->find('ingest');
        $this->assertEquals('newsml', $settings->getArticleTypeName());
        $this->assertNull($settings->getPublicationId());
        $this->assertNull($settings->getSectionNumber());
    }

    public function testSave()
    {
        $settings = $this->service->find('ingest');
        $this->service->save(array(
            'article_type' => 'news',
            'publication' => '1',
            'section' => '10',
        ), $settings);

        $this->assertEquals('news', $settings->getArticleTypeName());
        $this->assertEquals(1, $settings->getPublicationId());
        $this->assertEquals(10, $settings->getSectionNumber());
    }
}
