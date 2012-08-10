<?php

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
            $response['code'] = 200;
            $response['data'] = $responseData;
        } else {
            // invoke listener for error reponse.
        }

        $event->setResponse(new Response(json_encode($response)));
    }
}