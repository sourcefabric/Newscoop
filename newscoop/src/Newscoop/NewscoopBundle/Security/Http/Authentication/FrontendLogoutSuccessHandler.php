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
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Custom authentication success handler
 */
class FrontendLogoutSuccessHandler extends DefaultLogoutSuccessHandler
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
        $zendAuth = \Zend_Auth::getInstance();
        $zendAuth->clearIdentity();
        // logout from OAuth
        $token = new AnonymousToken(null, 'anon.');
        $session = $request->getSession();
        $request->getSession()->invalidate();
        $session->set('_security_oauth_authorize', serialize($token));
        $this->securityContext->setToken($token);

        setcookie('NO_CACHE', 'NO', time()-3600, '/', '.'.$this->extractDomain($_SERVER['HTTP_HOST']));

        return parent::onLogoutSuccess($request);
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
