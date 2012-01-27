<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Newscoop\Image\LocalImage,
    Newscoop\Image\Rendition,
    Newscoop\Image\LocalImageTest;

require_once __DIR__ . '/../Image/LocalImageTest.php';

/**
 */
class PackageServiceTest extends \TestCase
{
    const ARTICLE_NUMBER = 123;

    /** @var Newscoop\Package\PackageService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    public function setUp()
    {
        $this->orm = $this->setUpOrm('Newscoop\Package\Package', 'Newscoop\Package\Item', 'Newscoop\Image\LocalImage', 'Newscoop\Image\Rendition');
        $this->service = new PackageService($this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Package\PackageService', $this->service);
    }

    public function testSave()
    {
        $this->assertEquals(0, count($this->service->findByArticle(self::ARTICLE_NUMBER)));

        $rendition = $this->getRendition(500, 333, 'crop', 'square');

        $package = $this->service->save(array(
            'article' => self::ARTICLE_NUMBER,
            'headline' => 'headline',
            'rendition' => $rendition,
        ));

        $this->assertInstanceOf('Newscoop\Package\Package', $package);
        $this->assertNotNull($package->getId());
        $this->assertEquals(self::ARTICLE_NUMBER, $package->getArticleNumber());
        $this->assertEquals('headline', $package->getHeadline());
        $this->assertContains('1', (string) $package);
        $this->assertEquals(500, $package->getRendition()->getWidth());
        $this->assertEquals(333, $package->getRendition()->getHeight());
        $this->assertEquals('crop', $package->getRendition()->getSpecs());
        $this->assertEquals('square', $package->getRendition()->getName());

        $this->assertEquals(1, count($this->service->findByArticle(self::ARTICLE_NUMBER)));
    }

    public function testFind()
    {
        $this->assertNull($this->service->find(1));
        $this->service->save(array('headline' => 'test'));
        $this->assertNotNull($this->service->find(1));
    }

    public function testAddItem()
    {
        $this->orm->persist($image = new LocalImage('test'));
        $this->orm->persist($image2 = new LocalImage('next'));
        $this->orm->flush();

        $slideshow = $this->service->save(array('headline' => 'test'));
        $this->assertEquals(0, count($slideshow->getItems()));

        $this->service->addItem($slideshow, $image);
        $this->assertEquals(1, count($slideshow->getItems()));

        $this->assertEquals($image, $slideshow->getItems()->first()->getImage());
        $this->assertEquals(1, $slideshow->getItems()->first()->getId());
        $this->assertEquals(0, $slideshow->getItems()->first()->getOffset());

        $this->service->addItem($slideshow, $image2);
        $this->assertEquals(2, count($slideshow->getItems()));
    }

    public function testSetOrder()
    {
        $package = $this->service->save(array('headline' => 'test'));
        $this->service->addItem($package, new LocalImage('tic'));
        $this->service->addItem($package, new LocalImage('toc'));
        $this->service->addItem($package, new LocalImage('tac'));

        $this->service->setOrder($package, array('item-3', 'item-1', 'item-2'));

        $this->assertContains('tac', $package->getItems()->first()->getImage()->getPath());
        $this->assertContains('tic', $package->getItems()->get(1)->getImage()->getPath());
        $this->assertContains('toc', $package->getItems()->get(2)->getImage()->getPath());
    }

    public function testRemoveItem()
    {
        $package = $this->service->save(array('headline' => 'test'));
        $this->service->addItem($package, new LocalImage('tic'));
        $this->service->addItem($package, new LocalImage('tac'));
        $this->service->addItem($package, new LocalImage('toc'));

        $this->service->removeItem($package, 1);

        $this->assertEquals(2, count($package->getItems()));
        $this->assertEquals(0, $package->getItems()->get(0)->getOffset());
        $this->assertEquals(1, $package->getItems()->get(1)->getOffset());
    }

    public function testItemRendition()
    {
        $rendition = $this->getRendition(500, 333, 'crop', 'test');

        $package = $this->service->save(array(
            'headline' => 'test',
            'rendition' => $rendition,
        ));

        $this->service->addItem($package, new LocalImage(LocalImageTest::PICTURE_LANDSCAPE));
        $this->assertEquals($rendition, $package->getItems()->first()->getRendition());
    }

    public function testFindItem()
    {
        $this->assertNull($this->service->findItem(1));

        $package = $this->service->save(array('headline' => 'test'));
        $this->service->addItem($package, new LocalImage(LocalImageTest::PICTURE_LANDSCAPE));

        $this->assertNotNull($this->service->findItem(1));
    }

    public function testSaveItem()
    {
        $rendition = $this->getRendition(500, 333, 'crop', 'test');
        $package = $this->service->save(array(
            'headline' => 'test',
            'rendition' => $rendition,
        ));

        $item = $this->service->addItem($package, new LocalImage(LocalImageTest::PICTURE_LANDSCAPE));

        $this->service->saveItem(array(
            'caption' => 'testcap',
            'coords' => '0_0_120_120',
            'url' => 'tic',
        ), $item);

        $this->assertEquals('testcap', $item->getCaption());
        $this->assertEquals('crop_0_0_120_120', $item->getRendition()->getSpecs());
        $this->assertEquals('tic', $item->getVideoUrl());
    }

    public function testAddVideo()
    {
        $package = $this->service->save(array('headline' => 'test'));

        $youtube = $this->service->addItem($package, new RemoteVideo('http://youtu.be/1XsPVO61e9w'));
        $this->assertTrue($youtube->isVideo());
        $this->assertFalse($youtube->isImage());
        $this->assertEquals('http://youtu.be/1XsPVO61e9w', $youtube->getVideoUrl());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddSmallImage()
    {
        $package = $this->service->save(array(
            'headline' => 'test',
            'rendition' => $this->getRendition(800, 600, 'fit', 'test'),
        ));

        $this->service->addItem($package, new LocalImage(LocalImageTest::PICTURE_LANDSCAPE));
    }

    /**
     * Get rendition
     *
     * @param int $width
     * @param int $height
     * @param string $specs
     * @param string $name
     * @return Newscoop\Image\Rendition
     */
    private function getRendition($width, $height, $specs, $name)
    {
        $rendition = new Rendition($width, $height, $specs, $name);
        $this->orm->persist($rendition);
        $this->orm->flush($rendition);
        return $rendition;
    }
}
