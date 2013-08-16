<?php
 
namespace Newscoop\NewscoopBundle\Security\Http\Authentication;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
 
/**
 * Custom authentication success handler
 */
class AuthenticationFailedHandler extends DefaultAuthenticationFailureHandler
{
    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // log failed attepts
        //\LoginAttempts::RecordLoginAttempt();

        return parent::onAuthenticationFailure($request, $exception);
    }
}
