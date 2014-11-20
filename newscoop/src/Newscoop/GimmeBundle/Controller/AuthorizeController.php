<?php
/**
 * @package Newscoop\GimmeBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use FOS\OAuthServerBundle\Event\OAuthEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\OAuthServerBundle\Controller\AuthorizeController as BaseAuthorizeController;
use Newscoop\GimmeBundle\Entity\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthorizeController extends BaseAuthorizeController
{
    public function authorizeAction(Request $request)
    {
        if (!$request->get('client_id')) {
            throw new NotFoundHttpException("Client id parameter {$request->get('client_id')} is missing.");
        }

        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->findClientByPublicId($request->get('client_id'));
        if (!($client instanceof Client)) {
            throw new NotFoundHttpException("Client {$request->get('client_id')} is not found.");
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $form = $this->container->get('newscoop.gimme.authorize.form');
        $formHandler = $this->container->get('newscoop.gimme.authorize.form_handler');

        $event = $this->container->get('event_dispatcher')->dispatch(
            OAuthEvent::PRE_AUTHORIZATION_PROCESS,
            new OAuthEvent($user, $this->getClient())
        );

        if ($event->isAuthorizedClient()) {
            $scope = $this->container->get('request')->get('scope', null);

            return $this->container
                ->get('fos_oauth_server.server')
                ->finishClientAuthorization(true, $user, $request, $scope);
        }

        if (($response = $formHandler->process()) !== false) {
            if (true === $this->container->get('session')->get('_fos_oauth_server.ensure_logout')) {
                $this->container->get('security.context')->setToken(null);
                $this->container->get('session')->invalidate();
            }

            $this->container->get('event_dispatcher')->dispatch(
                OAuthEvent::POST_AUTHORIZATION_PROCESS,
                new OAuthEvent($user, $this->getClient(), $formHandler->isAccepted())
            );
        }

        $templatesService = $this->container->get('newscoop.templates.service');
        $smarty = $templatesService->getSmarty();
        $smarty->assign('client', $client);

        return new Response($templatesService->fetchTemplate('oauth_authorize.tpl'));
    }
}
