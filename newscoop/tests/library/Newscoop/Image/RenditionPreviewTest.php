<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 */
class RenditionPreviewTest extends \TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Image\RenditionPreview', $this->getPreview(1, 1, 100, 100));
        $this->assertInstanceOf('Newscoop\Image\RenditionInterface', $this->getPreview(1, 1, 100, 100));
    }

    public function testEqual()
    {
        $preview = $this->getPreview(400, 300, 400, 300);
        $this->assertEquals(400, $preview->getWidth());
        $this->assertEquals(300, $preview->getHeight());
    }

    public function testOriginalSmaller()
    {
        $preview = $this->getPreview(400, 300, 800, 600);
        $this->assertEquals(400, $preview->getWidth());
        $this->assertEquals(300, $preview->getHeight());
    }

    public function testOriginalWidthBigger()
    {
        $preview = $this->getPreview(200, 150, 150, 150);
        $this->assertEquals(150, $preview->getWidth());
        $this->assertEquals(113, $preview->getHeight());
    }

    public function testOriginalHeightBigger()
    {
        $preview = $this->getPreview(150, 200, 150, 150);
        $this->assertEquals(113, $preview->getWidth());
        $this->assertEquals(150, $preview->getHeight());
    }

    public function testOriginalBiggerLandscape()
    {
        $preview = $this->getPreview(400, 300, 200, 200);
        $this->assertEquals(200, $preview->getWidth());
        $this->assertEquals(150, $preview->getHeight());
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
       return new RenditionPreview(new Rendition($renditionWidth, $renditionHeight), $previewWidth, $previewHeight);
    }
}
