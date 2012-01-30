<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Newscoop\Image\LocalImage,
    Newscoop\Image\Rendition;

/**
 */
class ItemTest extends \TestCase
{
    /**
     * @var Newscoop\Package\Package
     */
    private $package;

    public function setUp()
    {
        $this->package = new Package();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Package\Item', new Item($this->package, new LocalImage('test')));
    }

    public function testGetPackageItems()
    {
        $item = new Item($this->package, new LocalImage('test'));
        $this->assertEquals(1, count($item->getPackageItems()));
        $this->assertContains($item, $item->getPackageItems());
        $this->assertNull($item->getPackageId());
    }

    public function testGetImageSpecs()
    {
        $rendition = new Rendition(200, 200, 'crop', 'test');
        $this->package->setRendition($rendition);

        $item = new Item($this->package, new LocalImage('test'));
        $item->setCoords('0_0_200_200');

        $this->assertEquals('crop_0_0_200_200', $item->getImageSpecs());
    }
}
