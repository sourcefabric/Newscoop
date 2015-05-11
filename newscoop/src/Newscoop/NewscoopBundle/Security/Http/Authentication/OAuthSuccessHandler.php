<?php

/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Custom authentication success handler for OAuth.
 * It sings in to the Symfony frontend firewall and also to the Zend,
 * when signing in via OAuth firewall.
 */
class OAuthSuccessHandler extends AbstractAuthenticationHandler
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
        $options['target_path_parameter'] = '_failure_path';

        parent::__construct($httpUtils, $options);
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from AbstractAuthenticationListener.
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        $zendAuth = \Zend_Auth::getInstance();
        $this->authAdapter->setUsername($user->getUsername())->setPassword($request->request->get('_password'));
        $zendAuth->authenticate($this->authAdapter);
        $frontendToken = $this->userService->loginUser($user, 'frontend_area');
        $session = $request->getSession();
        $session->set('_security_frontend_area', serialize($frontendToken));
        $this->setNoCacheCookie($request);

        return parent::onAuthenticationSuccess($request, $token);
    }
}
