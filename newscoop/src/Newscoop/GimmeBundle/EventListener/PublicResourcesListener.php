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
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if (strpos($route, 'newscoop_gimme_') === false) {
            return;
        }

        $unprotected = $this->container->get('em')->getRepository('\Newscoop\GimmeBundle\Entity\PublicApiResource')->findOneByResource($route);
        $routesArray = array(
            'newscoop_gimme_users_login',
            'newscoop_gimme_users_logout',
            'newscoop_gimme_users_register',
            'newscoop_gimme_users_restorepassword',
            'newscoop_gimme_users_getuseraccesstoken',
        );

        if ($request->getMethod() == 'POST' && $route == 'newscoop_gimme_comments_createcomment') {
            $publicationService = $this->container->get('newscoop.publication_service');
            $publication = $publicationService->getPublication();
            if ($publication->getPublicCommentsEnabled()) {
                $routesArray[] = 'newscoop_gimme_comments_createcomment';
            }
        }


        if (in_array($route, $routesArray)) {
            $unprotected = true;
        }

        if (!$unprotected &&
            strpos($route, 'newscoop_gimme_') !== false &&
            (false === $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ||
            false === $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
         ) {
            throw new OAuth2AuthenticateException(OAuth2::HTTP_UNAUTHORIZED,
                OAuth2::TOKEN_TYPE_BEARER,
                $this->container->get('fos_oauth_server.server')->getVariable(OAuth2::CONFIG_WWW_REALM),
                'OAuth2 authentication required'
            );
        }
    }
}
