<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * OAuth logout success handler
 */
class OAuthLogoutSuccessHandler extends AbstractLogoutHandler
{
    protected $securityContext;

    /**
     * @param HttpUtils $httpUtils
     * @param string    $targetUrl
     */
    public function __construct(HttpUtils $httpUtils, $securityContext)
    {
        parent::__construct($httpUtils);
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
        // logout from zend
        $zendAuth = \Zend_Auth::getInstance();
        $zendAuth->clearIdentity();
        // logout from frontend
        $token = new AnonymousToken(null, 'anon.');
        $session = $request->getSession();
        $request->getSession()->invalidate();
        $session->set('_security_frontend_area', serialize($token));
        $this->securityContext->setToken($token);

        $this->unsetNoCacheCookie($request);

        return parent::onLogoutSuccess($request);
    }
}
