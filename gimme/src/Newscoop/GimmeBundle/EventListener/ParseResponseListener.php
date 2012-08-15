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

class ParseResponseListener
{
    public function onResponse(FilterResponseEvent $event)
    {
        $response = array();
        $responseData = json_decode($event->getResponse()->getContent(), true);

        if (count($responseData) > 0) {
            $response['items'] = $responseData;
        } else {
            // invoke listener for error reponse.
        }

        $event->setResponse(new Response(json_encode($response)));
    }
}