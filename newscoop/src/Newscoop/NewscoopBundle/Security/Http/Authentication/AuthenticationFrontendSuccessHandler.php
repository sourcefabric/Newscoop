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
    private $authAdapter;

    /**
     * Constructor
     *
     * @param HttpUtils                                  $httpUtils
     * @param array                                      $options
     * @param Newscoop\Services\Auth\DoctrineAuthService $authAdapter
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
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $zendAuth = \Zend_Auth::getInstance();
        $this->authAdapter->setEmail($user->getEmail())->setPassword($request->request->get('password'));
        $zendAuth->authenticate($this->authAdapter);

        return parent::onAuthenticationSuccess($request, $token);
    }
}
