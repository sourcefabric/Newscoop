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
        $this->orm = $this->setUpOrm('Newscoop\Image\Image', 'Newscoop\Image\ArticleImageRendition', 'Newscoop\Image\ArticleImage');
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
        $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new Image('default'));
        $rendition = new Rendition(200, 200, 'fit', 'thumbnail');
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertTrue(isset($renditions[$rendition]));
        $this->assertContains('default', $renditions[$rendition]->getImage()->getPath());
    }

    public function testArticleRenditions()
    {
        $imageDefault = $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new Image('default'));
        $imageTest = $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new Image('test'));
        $renditionThumbnail = new Rendition(200, 200, 'fit', 'thumbnail');
        $renditionLandscape = new Rendition(400, 300, 'fit', 'landscape');

        $this->assertInstanceOf('Newscoop\Image\ArticleImageRendition', $this->service->setRenditionImage($renditionLandscape, $imageTest));
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);

        $this->assertContains('default', $renditions[$renditionThumbnail]->getImage()->getPath());
        $this->assertTrue($renditions[$renditionThumbnail]->isDefault());

        $this->assertContains('test', $renditions[$renditionLandscape]->getImage()->getPath());
        $this->assertFalse($renditions[$renditionLandscape]->isDefault());
    }

    public function testArticleRenditionTwice()
    {
        $image1 = $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new Image('first'), 1);
        $image2 = $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new Image('second'), 2);

        $rendition = new Rendition(200, 200, 'fit', 'thumb');

        $this->service->setRenditionImage($rendition, $image1);
        $this->service->setRenditionImage($rendition, $image2);

        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertContains('second', $renditions[$rendition]->getImage()->getPath());
    }
}
