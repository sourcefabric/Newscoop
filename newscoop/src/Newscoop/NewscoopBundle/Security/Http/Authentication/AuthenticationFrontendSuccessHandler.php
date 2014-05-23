<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * Custom authentication success handler for frontend
 */
class AuthenticationFrontendSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    protected $authAdapter;

    protected $userService;

    /**
     * Constructor
     *
     * @param HttpUtils                                  $httpUtils
     * @param array                                      $options
     * @param Newscoop\Services\Auth\DoctrineAuthService $authAdapter
     * @param UserService                                $userService
     */
    public function __construct(HttpUtils $httpUtils, array $options, $authAdapter, $userService)
    {
        $this->authAdapter = $authAdapter;
        $this->userService = $userService;

        parent::__construct($httpUtils, $options);
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from AbstractAuthenticationListener.
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $zendAuth = \Zend_Auth::getInstance();
        $this->authAdapter->setEmail($user->getEmail())->setPassword($request->request->get('password'));
        $zendAuth->authenticate($this->authAdapter);

        $OAuthtoken = $this->userService->loginUser($user, 'oauth_authorize');
        $session = $request->getSession();
        $session->set('_security_oauth_authorize', serialize($OAuthtoken));

        return parent::onAuthenticationSuccess($request, $token);
    }
}
