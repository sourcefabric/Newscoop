<?php

/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2015 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Symfony\Component\HttpFoundation\Request;

/**
 * Custom login listener.
 */
class InteractiveLogin extends TokenBasedRememberMeServices
{
    /**
     * {@inheritdoc}
     */
    protected function processAutoLoginCookie(array $cookieParts, Request $request)
    {
        $user = parent::processAutoLoginCookie($cookieParts, $request);

        $zendAuth = \Zend_Auth::getInstance();
        $authAdapter = new InteractiveDoctrineAuthService();
        $authAdapter->user = $user;
        $zendAuth->authenticate($authAdapter);

        return $user;
    }
}