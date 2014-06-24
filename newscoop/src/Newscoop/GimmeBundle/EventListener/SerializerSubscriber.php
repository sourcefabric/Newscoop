<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Interact with serilizer events
 */
class SerializerSubscriber implements EventSubscriberInterface
{
    private $container;

    /**
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.controller' => array('onController', -128)
        );
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onController(FilterControllerEvent $event)
    {
        $request = $this->container->get('request');
        if ($configuration = $request->attributes->get('_view')) {
            if ($request->query->has('expand')) {
                $configuration->setSerializerGroups(array('details'));
            }
        }
    }
}
