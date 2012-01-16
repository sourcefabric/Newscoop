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
        $this->assertInstanceOf('Newscoop\Image\Rendition', new Rendition('test', 1, 1));
    }

    public function testProperties()
    {
        $rendition = new Rendition('test', 200, 150, 'crop');
        $this->assertEquals('test', $rendition->getName());
        $this->assertEquals(200, $rendition->getWidth());
        $this->assertEquals(150, $rendition->getHeight());
        $this->assertEquals('crop', $rendition->getSpecs());
        $this->assertEquals('test', (string) $rendition);
    }
}
