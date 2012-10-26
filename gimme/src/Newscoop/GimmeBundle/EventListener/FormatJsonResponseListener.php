<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Newscoop\Gimme\Json;

/**
 * Preetify json response.
 */
class FormatJsonResponseListener
{
    public function onResponse(FilterResponseEvent $event)
    {
        $responseData = $event->getResponse()->getContent();

        $formatedJson = Json::indent($responseData);
        $event->setResponse(new Response($formatedJson));
    }
}