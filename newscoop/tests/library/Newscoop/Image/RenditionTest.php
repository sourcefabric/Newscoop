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
}
