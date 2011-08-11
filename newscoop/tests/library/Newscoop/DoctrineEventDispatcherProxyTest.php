<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use Doctrine\ORM\Events;

/**
 */
class DoctrineEventDispatcherProxyTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\DoctrineEventDispatcherProxy */
    protected $proxy;

    /** @var sfEventDispatcher */
    protected $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMockBuilder('sfEventDispatcher')
            ->getMock();

        $this->proxy = new DoctrineEventDispatcherProxy($this->dispatcher);
    }

    public function testEventDoctrineDispatcherProxy()
    {
        $this->assertInstanceOf('Newscoop\DoctrineEventDispatcherProxy', $this->proxy);
        $this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->proxy);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(array(
            Events::postPersist,
            Events::preUpdate,
            Events::preRemove,
        ), $this->proxy->getSubscribedEvents());

        foreach ($this->proxy->getSubscribedEvents() as $event) {
            $this->assertTrue(is_callable(array($this->proxy, $event)), "$event is not callable");
        }
    }
}
