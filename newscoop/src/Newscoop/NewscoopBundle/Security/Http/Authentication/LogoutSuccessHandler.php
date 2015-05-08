<?php

namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Custom authentication success handler
 */
class LogoutSuccessHandler extends AbstractLogoutHandler
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

        $this->unsetNoCacheCookie($request);

        return $this->httpUtils->createRedirectResponse($request, $referer);
    }
}
