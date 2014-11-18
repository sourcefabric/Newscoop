<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Override request metod based on "method" parameter - for development and testing purposes.
 */
class OverrideMethodListener
{
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->query->has('method')) {
            $request->server->set('REQUEST_METHOD', strtoupper($request->query->get('method')));
        }
    }
}
