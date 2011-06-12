<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testVersion()
    {
        $version = Version::VERSION;
        $this->assertFalse(empty($version));
    }

    public function testCompareVersion()
    {
        // test = version
        $version = Version::VERSION;
        $this->assertEquals(0, Version::compare($version));

        // test < version
        $this->assertLessThan(0, Version::compare('0.1'));
        $this->assertLessThan(0, Version::compare($version . '-alfa'));
        $this->assertLessThan(0, Version::compare($version . '-beta'));
        $this->assertLessThan(0, Version::compare($version . '-rc1'));

        // test > version
        $this->assertGreaterThan(0, Version::compare($version . '.1'));
        $this->assertGreaterThan(0, Version::compare($version . '-1'));
    }
}
