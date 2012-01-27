<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Newscoop\Image\LocalImage;

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
    }
}
