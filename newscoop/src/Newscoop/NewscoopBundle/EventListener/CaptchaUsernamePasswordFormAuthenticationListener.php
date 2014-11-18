<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * UsernamePasswordFormAuthenticationListener is the default implementation of
 * an authentication via a simple form composed of a username and a password.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CaptchaUsernamePasswordFormAuthenticationListener extends UsernamePasswordFormAuthenticationListener
{    
    /**
     *
     * TODO: Add Recaptcha, but first:
     * * add recaptcha config to newscoop preferences not in recaptcha plugin config
     * * remove old recaptcha libraries
     * * reenable failed logins counter here Newscoop\NewscoopBundle\Security\Http\Authentication\AuthenticationFailedHandler
     * * clean code
     * 
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        if ($request->request->has('captcha_code', $request->query->has('captcha_code')) && \LoginAttempts::MaxLoginAttemptsExceeded()) {
            if (false /* add recaptcha validation here */) {
                throw new AuthenticationException($translator->trans("CAPTCHA code is not valid.  Please try again.", array(), 'home'));
            }
        }

        return parent::attemptAuthentication($request);
    }
}
