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
class PackageTest extends \TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Package\Package', new Package());
    }

    public function testPrevNextItem()
    {
        $package = new Package();
        $first = new Item($package, new LocalImage('first'));

        $this->assertNull($package->getPrev($first));
        $this->assertNull($package->getNext($first));

        $second = new Item($package, new LocalImage('second'));

        $this->assertEquals($first, $package->getPrev($second));
        $this->assertEquals($second, $package->getNext($first));
    }
}
