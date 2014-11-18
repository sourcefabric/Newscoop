<?php

namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Custom authentication success handler
 */
class LogoutSuccessHandler extends DefaultLogoutSuccessHandler
{
    protected $securityContext;

    /**
     * @param HttpUtils $httpUtils
     * @param string    $targetUrl
     */
    public function __construct(HttpUtils $httpUtils, $targetUrl, $securityContext)
    {
        parent::__construct($httpUtils, $targetUrl);
        $this->securityContext = $securityContext;
    }

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @param Request $request
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        // Clear Zend auth
        $zendAuth = \Zend_Auth::getInstance();
        $zendAuth->clearIdentity();

        $referer = $request->headers->get('referer');
        // logout from OAuth
        $token = new AnonymousToken(null, 'anon.');
        $session = $request->getSession();
        $request->getSession()->invalidate();
        $session->set('_security_oauth_authorize', serialize($token));
        $this->securityContext->setToken($token);

        setcookie('NO_CACHE', 'NO', time()-3600, '/', '.'.$this->extractDomain($_SERVER['HTTP_HOST']));

        return $this->httpUtils->createRedirectResponse($request, $referer);
    }

    private function extractDomain($domain)
    {
        if (preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches)) {
            return $matches['domain'];
        } else {
            return $domain;
        }
    }
}
