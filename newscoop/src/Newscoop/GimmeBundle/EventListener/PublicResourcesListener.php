<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use OAuth2\OAuth2;
use OAuth2\OAuth2AuthenticateException;


class PublicResourcesListener
{
    protected $em;
    protected $serverService;
    protected $security;
    protected $publicationService;

    public function __construct($em, $serverService, $security, $publicationService)
    {
        $this->em = $em;
        $this->serverService = $serverService;
        $this->security = $security;
        $this->publicationService = $publicationService;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (strpos($route, 'newscoop_gimme_') === false) {
            return;
        }

        $unprotected = $this->em->getRepository('\Newscoop\GimmeBundle\Entity\PublicApiResource')->findOneByResource($route);
        $rootsArray = array(
            'newscoop_gimme_users_login',
            'newscoop_gimme_users_logout',
            'newscoop_gimme_users_register',
            'newscoop_gimme_users_restorepassword',
            'newscoop_gimme_users_getuseraccesstoken',
        );

        if ($this->publicationService->getPublication()->getPublicCommentsEnabled()) {
            $rootsArray[] = 'newscoop_gimme_comments_createcomment';
        }

        if (in_array($route, $rootsArray)) {
            $unprotected = true;
        }

        if (!$unprotected &&
            strpos($route, 'newscoop_gimme_') !== false &&
            false === $this->security->isGranted('IS_AUTHENTICATED_FULLY')
         ) {
            throw new OAuth2AuthenticateException(OAuth2::HTTP_UNAUTHORIZED,
                OAuth2::TOKEN_TYPE_BEARER,
                $this->serverService->getVariable(OAuth2::CONFIG_WWW_REALM),
                'OAuth2 authentication required'
            );
        }
    }
}
