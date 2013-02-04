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
        $this->orm = $this->setUpOrm('Newscoop\Image\LocalImage', 'Newscoop\Image\ArticleRendition', 'Newscoop\Image\ArticleImage', 'Newscoop\Image\Rendition', 'Newscoop\Package\Package');
        $this->imageService = new ImageService(array(), $this->orm);
        $this->service = new RenditionService(array(
            'theme_path' => '/../tests/fixtures/themes',
        ), $this->orm, $this->imageService);
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
        $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new LocalImage(self::PICTURE_LANDSCAPE));
        $rendition = new Rendition(200, 200, 'fit', 'thumbnail');
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertTrue(isset($renditions[$rendition]));
        $this->assertContains(self::PICTURE_LANDSCAPE, $renditions[$rendition]->getImage()->getPath());
    }

    public function testArticleRenditions()
    {
        $this->imageService->addArticleImage(self::ARTICLE_NUMBER, new LocalImage(self::PICTURE_LANDSCAPE));
        $this->orm->persist($imageTest = new LocalImage(self::PICTURE_PORTRAIT));
        $this->orm->flush();

        $this->orm->persist($renditionThumbnail = new Rendition(200, 200, 'fit', 'thumbnail'));
        $this->orm->persist($renditionLandscape = new Rendition(200, 200, 'fill', 'landscape'));
        $this->orm->flush();

        $this->assertInstanceOf('Newscoop\Image\ArticleRendition', $this->service->setArticleRendition(self::ARTICLE_NUMBER, $renditionLandscape, $imageTest));
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);

        $this->assertInstanceOf('Newscoop\Image\ArticleRendition', $renditions[$renditionThumbnail]);
        $this->assertContains(self::PICTURE_LANDSCAPE, $renditions[$renditionThumbnail]->getImage()->getPath());
        $this->assertTrue($renditions[$renditionThumbnail]->isDefault());

        $this->assertInstanceOf('Newscoop\Image\ArticleRendition', $renditions[$renditionLandscape]);
        $this->assertContains(self::PICTURE_PORTRAIT, $renditions[$renditionLandscape]->getImage()->getPath());
        $this->assertFalse($renditions[$renditionLandscape]->isDefault());
    }

    public function testArticleRenditionTwice()
    {
        $this->orm->persist($image1 = new LocalImage(self::PICTURE_LANDSCAPE));
        $this->orm->persist($image2 = new LocalImage(self::PICTURE_PORTRAIT));
        $this->orm->flush();

        $this->orm->persist($rendition = new Rendition(200, 200, 'fit', 'thumb'));
        $this->orm->flush();

        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image1);
        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image2);

        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertContains(self::PICTURE_PORTRAIT, $renditions[$rendition]->getImage()->getPath());
    }

    public function testSaveRenditionSpecs()
    {
        $this->orm->persist($image = new LocalImage(self::PICTURE_LANDSCAPE));
        $this->orm->flush();
        
        $this->orm->persist($rendition = new Rendition(300, 300, 'crop_0_15_300_315', 'test'));
        $this->orm->flush();

        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image);
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $rendition = $renditions['test']->getRendition();

        $this->assertEquals('test', $rendition->getName());
        $this->assertEquals(300, $rendition->getWidth());
        $this->assertEquals(300, $rendition->getHeight());
        $this->assertEquals('crop_0_15_300_315', $rendition->getSpecs());
    }

    public function testUnsetArticleRendition()
    {
        $this->orm->persist($rendition = new Rendition(300, 300, 'crop_0_15_300_315', 'test'));
        $this->orm->persist($image = new LocalImage(self::PICTURE_LANDSCAPE));
        $this->orm->flush();

        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image);
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertTrue(isset($renditions['test']));

        $this->service->unsetArticleRendition(self::ARTICLE_NUMBER, 'test');
        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertFalse(isset($renditions['test']));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetArticleRenditionSmallPicture()
    {
        $this->orm->persist($image = new LocalImage(self::PICTURE_LANDSCAPE));
        $this->orm->flush($image);

        $rendition = new Rendition(900, 600, 'test');
        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image);
    }

    public function testGetRenditions()
    {
        $renditions = $this->service->getRenditions();
        $this->assertEquals(5, count($renditions));
        $this->assertEquals(400, $renditions['landscape']->getWidth());
        $this->assertEquals(300, $renditions['landscape']->getHeight());
        $this->assertEquals('crop', $renditions['landscape']->getSpecs());
        $this->assertEquals('landscape', $renditions['landscape']->getName());
    }

    public function testGetOptions()
    {
        $options = $this->service->getOptions();
        $this->assertEquals(5, count($options));
        $this->assertArrayHasKey('landscape', $options);
        $this->assertInstanceOf('Newscoop\Image\Rendition', $this->service->getRendition('landscape'));
    }

    public function testSetRenditionImageSpecs()
    {
        $this->orm->persist($rendition = new Rendition(200, 200, 'crop', 'rend'));
        $this->orm->persist($image = new LocalImage(self::PICTURE_LANDSCAPE));
        $this->orm->flush();

        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image, '0_0_200_200');

        $renditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $articleRendition = $renditions[$rendition];

        $this->assertEquals($rendition->getName(), $articleRendition->getRendition()->getName());
        $this->assertEquals('0_0_200_200', $articleRendition->getImageSpecs());
        $this->assertEquals('crop_0_0_200_200', $articleRendition->getRendition()->getSpecs());
        $this->assertEquals('crop', $rendition->getSpecs());
    }

    public function testOrder()
    {
        $order = array('preview', 'square', 'portrait', 'landscape', 'thumbnail');
        $this->service->getRenditions(); // load into db
        $this->service->setRenditionsOrder($order);
        $renditions = $this->service->getRenditions();
        $this->assertEquals($order, array_keys($renditions));
    }

    public function testLabels()
    {
        $renditions = $this->service->getRenditions();
        foreach ($renditions as $renditionName => $rendition) {
            $this->assertEquals($renditionName, $rendition->getLabel());
        }

        $this->service->setRenditionsLabels(array(
            'preview' => 'Test',
        ));

        $this->assertTrue(isset($renditions['preview']));
        $this->assertEquals('Test', $renditions['preview']->getLabel());
    }

    public function testReloadRenditions()
    {
        $this->service->getRenditions();

        $rendition = new Rendition(300, 200, 'crop', 'old');
        $rendition2 = new Rendition(300, 200, 'crop', 'packaged');
        $image = new LocalImage(self::PICTURE_PORTRAIT);
        $this->orm->persist($rendition);
        $this->orm->persist($rendition2);
        $this->orm->persist($image);
        $this->orm->flush();

        $package = new \Newscoop\Package\Package();
        $package->setHeadline('test');
        $package->setRendition($rendition2);
        $this->orm->persist($package);
        $this->orm->flush();

        $this->service->setArticleRendition(self::ARTICLE_NUMBER, $rendition, $image);

        $this->service->reloadRenditions();

        $this->assertEquals(6, count($this->service->getRenditions()));
        $this->assertFalse(array_key_exists('old', $this->service->getRenditions()));
        $this->assertTrue(array_key_exists('packaged', $this->service->getRenditions()));

        $articleRenditions = $this->service->getArticleRenditions(self::ARTICLE_NUMBER);
        $this->assertFalse($articleRenditions->offsetExists($rendition));
    }
}
