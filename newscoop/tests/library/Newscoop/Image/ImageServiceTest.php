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
        $this->assertEquals('300x300/fit/' . rawurlencode(rawurlencode($image)), $src);
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
        $this->assertNull($this->service->getDefaultArticleImage(1));
        $this->assertInstanceOf('Newscoop\Image\ArticleImage', $this->service->addArticleImage(1, new LocalImage('default')));
        $this->assertContains('default', $this->service->getDefaultArticleImage(1)->getPath());
    }
}
