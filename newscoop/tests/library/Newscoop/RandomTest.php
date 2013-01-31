<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 */
class RandomTest extends \TestCase
{
    public function setUp()
    {
        $this->random = new Random();
    }

    public function testGetRandomString()
    {
        $randoms = array();
        for ($i = 0; $i < 1000; $i++) {
            $random = $this->random->getRandomString(5);
            $this->assertEquals(5, strlen($random));
            if (!array_key_exists($random, $randoms)) {
                $randoms[$random] = true;
            }
        }

        $this->assertGreaterThan(990, count($randoms));
    }
}
