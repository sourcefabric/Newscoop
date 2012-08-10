<?php

namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Newscoop\GimmeService\Json;

class FormatJsonResponseListener
{
    public function onResponse(FilterResponseEvent $event)
    {
        $responseData = $event->getResponse()->getContent();
        $formatedJson = Json::indent($responseData);

        $event->setResponse(new Response($formatedJson));
    }
}