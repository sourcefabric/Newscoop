<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class ImageServiceTest extends \TestCase
{
    const ARTICLE_NUMBER = 123;
    const PICTURE_LANDSCAPE = 'tests/fixtures/picture_landscape.jpg';
    const PICTURE_PORTRAIT = 'tests/fixtures/picture_portrait.jpg';

    /** @var Newscoop\Image\ImageService */
    protected $service;

    /** @var array */
    protected $config = array();

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    public function setUp()
    {
        $this->config = array(
            'cache_url' => 'images/cache',
            'cache_path' => sys_get_temp_dir() . '/' . uniqid(),
        );

        $this->orm = $this->setUpOrm('Newscoop\Image\LocalImage', 'Newscoop\Image\ArticleImage');
        $this->service = new ImageService($this->config, $this->orm);
    }

    public function tearDown()
    {
        if (realpath($this->config['cache_path'])) {
            system('rm -r ' . $this->config['cache_path']);
        }
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\ImageService', $this->service);
    }

    public function testGetSrc()
    {
        $image = 'images/picture.jpg';
        $src = $this->service->getSrc($image, 300, 300);
        $this->assertEquals('300x300/fit/' . str_replace('/', '|', $image), $src);
    }

    public function testFindByArticle()
    {
        $this->assertEquals(0, count($this->service->findByArticle(self::ARTICLE_NUMBER)));
        $this->service->addArticleImage(self::ARTICLE_NUMBER, new LocalImage('test'));
        $articleImages = $this->service->findByArticle(self::ARTICLE_NUMBER);
        $this->assertEquals(1, count($articleImages));
        $this->assertInstanceOf('Newscoop\Image\ArticleImage', $articleImages[0]);
    }

    public function testGetDefaultArticleImage()
    {
        $this->assertNull($this->service->getDefaultArticleImage(self::ARTICLE_NUMBER));
        $this->assertInstanceOf('Newscoop\Image\ArticleImage', $this->service->addArticleImage(self::ARTICLE_NUMBER, new LocalImage('default')));
        $this->assertContains('default', $this->service->getDefaultArticleImage(self::ARTICLE_NUMBER)->getPath());
    }

    public function testGetThumbnail()
    {
        $rendition = new Rendition(200, 200, 'crop');
        $image = new LocalImage(self::PICTURE_LANDSCAPE);
        $thumbnail = $this->service->getThumbnail($rendition, $image);

        $this->assertEquals($this->service->getSrc(self::PICTURE_LANDSCAPE, 200, 200, 'crop'), $thumbnail->src);
        $this->assertEquals(200, $thumbnail->width);
        $this->assertEquals(200, $thumbnail->height);
    }

    public function testDefaultImage()
    {
        $this->assertNull($this->service->getDefaultArticleImage(self::ARTICLE_NUMBER));

        $this->service->addArticleImage(self::ARTICLE_NUMBER, new LocalImage('first'));
        $this->assertContains('first', $this->service->getDefaultArticleImage(self::ARTICLE_NUMBER)->getPath());
        $this->assertTrue($this->service->getDefaultArticleImage(self::ARTICLE_NUMBER)->isDefault());

        $image = $this->service->addArticleImage(self::ARTICLE_NUMBER, new LocalImage('second'));
        $this->service->setDefaultArticleImage(self::ARTICLE_NUMBER, $image);

        $this->assertTrue($image->isDefault());
        $this->assertContains('second', $this->service->getDefaultArticleImage(self::ARTICLE_NUMBER)->getPath());
    }

    public function testSetDefaultImageForFindByArticleWithoutSetDefault()
    {
        $this->orm->persist($imageTic = new LocalImage('tic'));
        $this->orm->persist($imageToc = new LocalImage('toc'));
        $this->orm->flush();

        $this->orm->persist(new ArticleImage(self::ARTICLE_NUMBER, $imageTic));
        $this->orm->persist(new ArticleImage(self::ARTICLE_NUMBER, $imageToc));
        $this->orm->flush();

        $images = $this->service->findByArticle(self::ARTICLE_NUMBER);
        $this->assertTrue($images[0]->isDefault());
    }

    public function testFind()
    {
        $this->assertNull($this->service->find(1));

        $this->orm->persist(new LocalImage(self::PICTURE_LANDSCAPE));
        $this->orm->flush();

        $this->assertNotNull($this->service->find(1));
    }

    public function testFindBy()
    {
        $this->assertEquals(0, count($this->service->findBy(array())));
        $this->assertEquals(0, $this->service->getCountBy(array()));

        $this->orm->persist(new LocalImage(self::PICTURE_LANDSCAPE));
        $this->orm->persist(new LocalImage('file://' . realpath(APPLICATION_PATH . '/../' . self::PICTURE_LANDSCAPE)));
        $this->orm->flush();

        $this->assertEquals(2, count($this->service->findBy(array())));
        $this->assertEquals(2, $this->service->getCountBy(array()));
    }
}
