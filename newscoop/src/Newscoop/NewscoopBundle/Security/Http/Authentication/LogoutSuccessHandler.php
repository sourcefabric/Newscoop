<?php
 
namespace Newscoop\NewscoopBundle\Security\Http\Authentication;
 
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
 
/**
 * Custom authentication success handler
 */
class LogoutSuccessHandler extends DefaultLogoutSuccessHandler
{
    private $authAdapter;
 
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
        \Article::UnlockByUser((int) $zendAuth->getIdentity());
        $zendAuth->clearIdentity();

        return parent::onLogoutSuccess($request);
    }
}
