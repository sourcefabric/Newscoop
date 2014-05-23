<?php

namespace Newscoop\GimmeBundle\EventListener;

use FOS\OAuthServerBundle\Event\OAuthEvent;

class OAuthEventListener
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
        if ($event->getClient()->getTrusted()) {
            $event->setAuthorizedClient(true);

            return true;
        }

        if ($user = $this->getUser($event)) {
            $event->setAuthorizedClient(
                $user->hasClient($event->getClient())
            );
        }
    }

    public function onPostAuthorizationProcess(OAuthEvent $event)
    {
        if ($event->isAuthorizedClient()) {
            if (null !== $client = $event->getClient()) {
                $user = $this->getUser($event);
                $user->addClient($client);
                $client->addUser($user);
                $this->em->persist($user);
                $this->em->persist($client);
                $this->em->flush();
            }
        }
    }

    protected function getUser(OAuthEvent $event)
    {
        return $event->getUser();
    }
}
