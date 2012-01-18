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
    const ARTICLE_NUMBER = 123;

    /** @var Newscoop\Image\RenditionService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    /** @var Newscoop\Image\ImageService */
    protected $imageService;

    public function setUp()
    {
        $this->orm = $this->setUpOrm('Newscoop\Image\LocalImage', 'Newscoop\Image\ArticleRendition', 'Newscoop\Image\ArticleImage');
        $this->imageService = new ImageService(array(), $this->orm);
        $this->service = new RenditionService($this->orm, $this->imageService);
    }

    public function tearDown()
    {
        $this->tearDownOrm($this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\RenditionService', $this->service);
    }

    public function testArticleRenditionNoDefault()
    {
        $rendition = new Rendition(200, 200, 'fit', 'thumbnail');
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertFalse(isset($renditions[$rendition]));
    }

    public function testArticleRenditionWithDefault()
    {
        $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new LocalImage('default'));
        $rendition = new Rendition(200, 200, 'fit', 'thumbnail');
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertTrue(isset($renditions[$rendition]));
        $this->assertContains('default', $renditions[$rendition]->getImage()->getPath());
    }

    public function testArticleRenditions()
    {
        $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new LocalImage('default'));
        $this->orm->persist($imageTest = new LocalImage('test'));
        $this->orm->flush();

        $renditionThumbnail = new Rendition(200, 200, 'fit', 'thumbnail');
        $renditionLandscape = new Rendition(400, 300, 'fit', 'landscape');

        $this->assertInstanceOf('Newscoop\Image\ArticleRendition', $this->service->setArticleRendition(self::ARTICLE_NUMBER, $renditionLandscape, $imageTest));
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);

        $this->assertInstanceOf('Newscoop\Image\ArticleRendition', $renditions[$renditionThumbnail]);
        $this->assertContains('default', $renditions[$renditionThumbnail]->getImage()->getPath());
        $this->assertTrue($renditions[$renditionThumbnail]->isDefault());

        $this->assertInstanceOf('Newscoop\Image\ArticleRendition', $renditions[$renditionLandscape]);
        $this->assertContains('test', $renditions[$renditionLandscape]->getImage()->getPath());
        $this->assertFalse($renditions[$renditionLandscape]->isDefault());
    }

    public function testArticleRenditionTwice()
    {
        $this->orm->persist($image1 = new LocalImage('first'));
        $this->orm->persist($image2 = new LocalImage('second'));
        $this->orm->flush();

        $rendition = new Rendition(200, 200, 'fit', 'thumb');

        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image1);
        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image2);

        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertContains('second', $renditions[$rendition]->getImage()->getPath());
    }

    public function testSaveRenditionSpecs()
    {
        $this->orm->persist($image = new LocalImage('test'));
        $this->orm->flush();

        $this->service->setArticleRendition(self::ARTICLE_NUMBER, new Rendition(300, 300, 'crop_0_15_300_315', 'test'), $image);
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $rendition = $renditions['test']->getRendition();

        $this->assertEquals('test', $rendition->getName());
        $this->assertEquals(300, $rendition->getWidth());
        $this->assertEquals(300, $rendition->getHeight());
        $this->assertEquals('crop_0_15_300_315', $rendition->getSpecs());
    }
}
