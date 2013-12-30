<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Tests;

// This assumes that this class file is located at:
// src/Application/AcmeBundle/Tests/ContainerAwareUnitTestCase.php
// with Symfony 2.0 Standard Edition layout. You may need to change it
// to fit your own file system mapping.
require_once __DIR__.'/../../../../application/AppKernel.php';
require_once __DIR__.'/../../../../../newscoop/constants.php';

class ContainerAwareUnitTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $kernel;
    protected static $container;

    public static function setUpBeforeClass()
    {
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();

        self::$container = self::$kernel->getContainer();
    }

    public function get($serviceId)
    {
        return self::$kernel->getContainer()->get($serviceId);
    }
}
