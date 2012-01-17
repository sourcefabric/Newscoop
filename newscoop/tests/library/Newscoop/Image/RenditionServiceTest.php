<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class RenditionServiceTest extends \TestCase
{
    /** @var Newscoop\Image\RenditionService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    public function setUp()
    {
        $this->orm = $this->setUpOrm('Newscoop\Image\Image', 'Newscoop\Image\ArticleRendition');
        $this->service = new RenditionService($this->orm);
    }

    public function tearDown()
    {
        $this->tearDownOrm($this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\RenditionService', $this->service);
    }

    public function testArticleRendition()
    {
        $image = new Image('path');
        $this->orm->persist($image);
        $this->orm->flush($image);

        $rendition = new Rendition(200, 200, 'fit', 'thumbnail');

        $this->assertNull($this->service->getArticleRendition(12, $rendition));
        $this->assertEmpty($this->service->getArticleRenditions(12));

        $this->service->setArticleRendition(12, $rendition, $image);

        $articleRendition = $this->service->getArticleRendition(12, $rendition);
        $this->assertNotNull($articleRendition);
        $this->assertEquals('images/path', $articleRendition->getImage()->getPath());

        $this->assertEquals(1, count($this->service->getArticleRenditions(12)));
        $this->assertTrue(array_key_exists('thumbnail', $this->service->getArticleRenditions(12)));

    }

    public function testArticleRenditionTwice()
    {
        $image1 = new Image('path1');
        $image2 = new Image('path2');
        $this->orm->persist($image1);
        $this->orm->persist($image2);
        $this->orm->flush();

        $rendition = new Rendition(200, 200, 'fit');

        $this->service->setArticleRendition(12, $rendition, $image1);
        $this->service->setArticleRendition(12, $rendition, $image2);
        $this->assertContains('path2', $this->service->getArticleRendition(12, $rendition)->getImage()->getPath());
    }
}
