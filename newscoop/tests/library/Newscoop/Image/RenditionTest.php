<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class RenditionTest extends \TestCase
{
    const PICTURE_LANDSCAPE = 'tests/fixtures/picture_landscape.jpg';
    const PICTURE_PORTRAIT = 'tests/fixtures/picture_portrait.jpg';

    /** @var Newscoop\Image\ImageService */
    private $imageService;

    public function setUp()
    {
        $this->imageService = $this->getMockBuilder('Newscoop\Image\ImageService')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\Rendition', new Rendition(1, 1));
    }

    public function testProperties()
    {
        $rendition = new Rendition(200, 150, 'crop', 'test');
        $this->assertEquals('test', $rendition->getName());
        $this->assertEquals(200, $rendition->getWidth());
        $this->assertEquals(150, $rendition->getHeight());
        $this->assertEquals('crop', $rendition->getSpecs());
        $this->assertEquals('test', (string) $rendition);
    }

    public function testPreviewEqual()
    {
        $preview = $this->getPreview(400, 300, 400, 300);
        $this->assertEquals(400, $preview->getWidth());
        $this->assertEquals(300, $preview->getHeight());
    }

    public function testPreviewOriginalSmaller()
    {
        $preview = $this->getPreview(400, 300, 800, 600);
        $this->assertEquals(400, $preview->getWidth());
        $this->assertEquals(300, $preview->getHeight());
    }

    public function testPreviewOriginalWidthBigger()
    {
        $preview = $this->getPreview(200, 150, 150, 150);
        $this->assertEquals(150, $preview->getWidth());
        $this->assertEquals(113, $preview->getHeight());
    }

    public function testPreviewOriginalHeightBigger()
    {
        $preview = $this->getPreview(150, 200, 150, 150);
        $this->assertEquals(113, $preview->getWidth());
        $this->assertEquals(150, $preview->getHeight());
    }

    public function testPreviewOriginalBiggerLandscape()
    {
        $preview = $this->getPreview(400, 300, 200, 200);
        $this->assertEquals(200, $preview->getWidth());
        $this->assertEquals(150, $preview->getHeight());
    }

    public function testGenerateImageFill()
    {
        $rendition = new Rendition(200, 200, 'fill');
        $image = $rendition->generate(new LocalImage(self::PICTURE_LANDSCAPE));

        $this->assertEquals(300, $image->getWidth());
        $this->assertEquals(200, $image->getHeight());
    }

    public function testGenerateImageFit()
    {
        $rendition = new Rendition(200, 200, 'fit');
        $image = $rendition->generate(new LocalImage(self::PICTURE_LANDSCAPE));

        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(133, $image->getHeight());
    }

    public function testGenerateImageFillCrop()
    {
        $rendition = new Rendition(200, 200, 'crop');
        $image = $rendition->generate(new LocalImage(self::PICTURE_LANDSCAPE));

        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(200, $image->getHeight());
    }

    public function testGenerateImageSpecificCrop()
    {
        $rendition = new Rendition(200, 200, 'crop_0_0_200_200');
        $image = $rendition->generate(new LocalImage(self::PICTURE_LANDSCAPE));
        $this->assertEquals(200, $image->getWidth());
        $this->assertEquals(200, $image->getHeight());
    }

    public function testAspectRatio()
    {
        $rendition = new Rendition(400, 300, 'fit');
        $this->assertEquals((float) 400 / (float) 300, $rendition->getAspectRatio());
    }

    public function testGetSelectAreaLandscape()
    {
        $rendition = new Rendition(400, 300, 'fill');
        $this->assertEquals(array(28, 0, 472, 333), $rendition->getSelectArea(new LocalImage(self::PICTURE_LANDSCAPE)));

        $rendition = new Rendition(200, 200, 'crop_0_0_333_333');
        $this->assertEquals(array(0, 0, 333, 333), $rendition->getSelectArea(new LocalImage(self::PICTURE_LANDSCAPE)));

        $rendition = new Rendition(150, 150, 'fill');
        $this->assertEquals(array(0, 84, 333,  417), $rendition->getSelectArea(new LocalImage(self::PICTURE_PORTRAIT)));
    }

    public function testGetMinSize()
    {
        $rendition = new Rendition(200, 200, 'fill');
        $this->assertEquals(array(200, 200), $rendition->getMinSize(new LocalImage(self::PICTURE_LANDSCAPE)));

        $rendition = new Rendition(400, 300, 'fill');
        $this->assertEquals(array(400, 300), $rendition->getMinSize(new LocalImage(self::PICTURE_LANDSCAPE)));
    }

    public function testGetThumbnail()
    {
        $this->imageService->expects($this->once())
            ->method('getSrc')
            ->with($this->equalTo(self::PICTURE_LANDSCAPE), $this->equalTo(300), $this->equalTo(300), $this->equalTo('crop'))
            ->will($this->returnValue('300x300/crop/' . rawurlencode(rawurlencode(self::PICTURE_LANDSCAPE))));

        $rendition = new Rendition(300, 300, 'crop');
        $thumbnail = $rendition->getThumbnail(new LocalImage(self::PICTURE_LANDSCAPE), $this->imageService);

        $this->assertInstanceOf('Newscoop\Image\Thumbnail', $thumbnail);
        $this->assertContains('300x300', $thumbnail->src);
        $this->assertEquals(300, $thumbnail->width, 'thumbnail_width');
        $this->assertEquals(300, $thumbnail->height, 'thumbnail_height');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetThumbnailWrongSize()
    {
        $rendition = new Rendition(1000, 1000, 'crop');
        $rendition->getThumbnail(new LocalImage(self::PICTURE_LANDSCAPE), $this->imageService);
    }

    /**
     * Get preview
     *
     * @param int $renditionWidth
     * @param int $renditionHeight
     * @param int $previewWidth
     * @param int $previewHeight
     * @return Newscoop\Image\RenditionPreview
     */
    private function getPreview($renditionWidth, $renditionHeight, $previewWidth, $previewHeight)
    {
        $rendition = new Rendition($renditionWidth, $renditionHeight);
        return $rendition->getPreview($previewWidth, $previewHeight);
    }

    /**
     * Encode filepath for url
     *
     * @param string $filepath
     * @return string
     */
    private function encode($filepath)
    {
        return rawurlencode(rawurlencode($filepath));
    }
}
