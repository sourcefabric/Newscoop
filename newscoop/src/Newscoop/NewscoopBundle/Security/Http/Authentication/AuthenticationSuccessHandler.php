<?php
 
namespace Newscoop\NewscoopBundle\Security\Http\Authentication;
 
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
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
        $zendAuth = \Zend_Auth::getInstance();
        $this->authAdapter->setUsername($user->getUsername())->setPassword($request->request->get('_password'))->setAdmin(true);
        $result = $zendAuth->authenticate($this->authAdapter);

        return parent::onAuthenticationSuccess($request, $token);
    }
}
