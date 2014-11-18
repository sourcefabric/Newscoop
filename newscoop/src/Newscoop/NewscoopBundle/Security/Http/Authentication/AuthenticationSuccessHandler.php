<?php

namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * Custom authentication success handler
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    protected $authAdapter;

    protected $em;

    protected $userService;

    /**
    * Constructor
    *
    */
    public function __construct(HttpUtils $httpUtils, array $options, $authAdapter, $em, $userService)
    {
        $this->authAdapter = $authAdapter;
        $this->em = $em;
        $this->userService = $userService;

        parent::__construct($httpUtils, $options);
    }

    /**
    * This is called when an interactive authentication attempt succeeds. This
    * is called by authentication listeners inheriting from AbstractAuthenticationListener.
    * @param Request        $request
    * @param TokenInterface $token
    * @return Response The response to return
    */
    function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user->isAdmin()) { // can't go into admin
            $redirector = \Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->direct('index', 'index', 'default');
        }

        \LoginAttempts::DeleteOldLoginAttempts();
        \LoginAttempts::ClearLoginAttemptsForIp();

        $zendAuth = \Zend_Auth::getInstance();
        $this->authAdapter->setUsername($user->getUsername())->setPassword($request->request->get('_password'))->setAdmin(true);
        $result = $zendAuth->authenticate($this->authAdapter);

        $OAuthtoken = $this->userService->loginUser($user, 'oauth_authorize');
        $session = $request->getSession();
        $session->set('_security_oauth_authorize', serialize($OAuthtoken));

        \Article::UnlockByUser($zendAuth->getIdentity());

        $request->setLocale($request->request->get('login_language'));
        setcookie('NO_CACHE', '1', NULL, '/', '.'.$this->extractDomain($_SERVER['HTTP_HOST']));

        $user->setLastLogin(new \DateTime());
        $this->em->flush();

        if ($request->get('ajax') === 'true') {
            // close popup with login.
            return new Response("<script type=\"text/javascript\">window.parent.g_security_token = '".\SecurityToken::GetToken()."';window.parent.$(window.parent.document.body).data('loginDialog').dialog('close');window.parent.setSecurityToken(window.parent.g_security_token);</script>");
        }

        return parent::onAuthenticationSuccess($request, $token);
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
