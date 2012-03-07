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
    const PICTURE_LANDSCAPE = 'tests/fixtures/picture_landscape.jpg';
    const PICTURE_PORTRAIT = 'tests/fixtures/picture_portrait.jpg';

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
}
