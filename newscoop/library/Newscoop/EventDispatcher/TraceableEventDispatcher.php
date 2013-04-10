<?php
/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\EventDispatcher;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Debug\ContainerAwareTraceableEventDispatcher;
use Symfony\Component\EventDispatcher\Event;

/**
 * TraceableEventDispatcher class.
 *
 * Provides wrapper methods for symfony's EventDispatcher.
 */
class TraceableEventDispatcher extends ContainerAwareTraceableEventDispatcher
{

    public function __construct($container)
    {
        parent::__construct($container, $container->getService('debug.stopwatch'));
    }
    
    /**
     * Adds an event listener that listens on the specified events.
     *
     * Wrapper for Symfony\Component\EventDispatcher\EventDispatcher::addListener method.
     *
     * @param string $eventName The event to listen on
     * @param callable $listener The listener
     * @param integer $priority The higher this value, the earlier an event listener will be triggered in the chain (defaults to 0)
     */
    public function connect($eventName, $listener, $priority = 0) 
    {
        parent::addListener($eventName, $listener, $priority);
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * Wrapper for Symfony\Component\EventDispatcher\EventDispatcher::dispatch method.
     *
     * @param string $eventName The name of the event to dispatch. The name of the event is the name of the method that is invoked on listeners.
     * @param Event $event The event to pass to the event handlers/listeners. If not supplied, an empty Event instance is created.
     *
     * @return Event
     */
    public function notify($eventName, Event $event) 
    {
        return parent::dispatch($eventName, $event);
    }
}
