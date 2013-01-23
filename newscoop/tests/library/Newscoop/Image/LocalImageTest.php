<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class LocalImageTest extends \TestCase
{
    public function tearDown()
    {
        system(sprintf('rm -f %s', APPLICATION_PATH . '/../images/phpunit*'));
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\LocalImage', new LocalImage(self::PICTURE_LANDSCAPE));
    }

    public function testGetPath()
    {
        $image = new LocalImage('cms-123.jpg');
        $this->assertEquals('images/cms-123.jpg', $image->getPath());

        $image = new LocalImage(self::PICTURE_LANDSCAPE);
        $this->assertEquals(self::PICTURE_LANDSCAPE, $image->getPath());
    }

    public function testGetDimensions()
    {
        $image = new LocalImage(self::PICTURE_LANDSCAPE);
        $this->assertEquals(500, $image->getWidth());
        $this->assertEquals(333, $image->getHeight());
    }

    public function testSetGetDescription()
    {
        $image = new LocalImage(self::PICTURE_LANDSCAPE);
        $image->setDescription('desc');
        $this->assertEquals('desc', $image->getDescription());
    }

    public function testRemoteImage()
    {
        $url = 'file://' . realpath(APPLICATION_PATH . '/../' . self::PICTURE_LANDSCAPE);
        $image = new LocalImage($url);
        $this->assertEquals(500, $image->getWidth());
        $this->assertEquals(333, $image->getHeight());
        $this->assertEquals($url, $image->getPath());
    }

    public function testHasGetWidth()
    {
        $image = new LocalImage(self::PICTURE_LANDSCAPE);
        $this->assertFalse($image->hasWidth());
        $image->getWidth();
        $this->assertTrue($image->hasWidth());
    }

    /**
     * @ticket WOBS-1189
     */
    public function testGetWidthAfterStoring0()
    {
        $image = new LocalImage(self::PICTURE_LANDSCAPE);

        $width = new \ReflectionProperty($image, 'width');
        $width->setAccessible(true);
        $width->setValue($image, 0);

        $this->assertEquals(500, $image->getWidth());
    }

    /**
     * @ticket WOBS-1189
     */
    public function testGetHeightAfterStoring0()
    {
        $image = new LocalImage(self::PICTURE_LANDSCAPE);

        $height = new \ReflectionProperty($image, 'height');
        $height->setAccessible(true);
        $height->setValue($image, 0);

        $this->assertEquals(333, $image->getHeight());
    }

    /**
     * @ticket WOBS-1189
     * @ticket CS-4546
     */
    public function testGetImageSizeWrongImage()
    {
        $tmp = tempnam(APPLICATION_PATH . '/../images/', 'phpunit');
        file_put_contents($tmp, '');

        $image = new LocalImage(basename($tmp));
        $this->assertEquals(LocalImage::BROKEN_WIDTH, $image->getWidth());
    }
}
