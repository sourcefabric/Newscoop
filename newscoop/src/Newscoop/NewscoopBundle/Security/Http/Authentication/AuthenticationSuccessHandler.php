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
    private $authAdapter;
 
    /**
    * Constructor
    * 
    * @param Zend_Auth   $zendAuth
    */
    public function __construct(HttpUtils $httpUtils, array $options, $authAdapter)
    {
        $this->authAdapter = $authAdapter;

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

        \Article::UnlockByUser($zendAuth->getIdentity());

        $request->setLocale($request->request->get('login_language'));

        $session = $request->getSession();
        $session->set('NO_CACHE', true);

        if ($request->get('ajax') === 'true') {
            // close popup with login.
            return new Response("<script type=\"text/javascript\">window.parent.g_security_token = '".\SecurityToken::GetToken()."';window.parent.$(window.parent.document.body).data('loginDialog').dialog('close');window.parent.setSecurityToken(window.parent.g_security_token);</script>");
        }

        return parent::onAuthenticationSuccess($request, $token);
    }
}
