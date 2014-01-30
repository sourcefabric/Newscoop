<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Save locale cookie.
 */
class LocaleListener
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function onResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->request->has('login_language')) {
            $response = $event->getResponse();
            $response->headers->setCookie(new Cookie('TOL_Language', $request->request->get('login_language')));
            $event->setResponse($response);
        }
    }

    public function onRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        $pos = strpos($request->server->get('REQUEST_URI'), '/admin');
        $cookies = $request->cookies;

        if ($cookies->has('TOL_Language')) {
            $request->setLocale($cookies->get("TOL_Language"));
        }

        if ($pos === false) {
            $publicationMetadata = $request->attributes->get('_newscoop_publication_metadata');
            $language = $this->em->getRepository('Newscoop\Entity\Language')->findOneById($publicationMetadata['publication']['id_default_language']);
            $request->setLocale($language->getCode());
        }
    }
}
