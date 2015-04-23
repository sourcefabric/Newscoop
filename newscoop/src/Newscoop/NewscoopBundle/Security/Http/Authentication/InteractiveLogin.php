<?php

/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2015 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Symfony\Component\HttpFoundation\Request;

/**
 * Temporary class for remember_me token based authentication
 */
class InteractiveDoctrineAuthService implements \Zend_Auth_Adapter_Interface
{
    public $user = null;

    /**
     * Perform authentication attempt
     *
     * @return \Zend_Auth_Result
     */
    public function authenticate()
    {
        if (empty($this->user)) {
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, NULL);
        }

        if (!$this->user->isActive()) {
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE_UNCATEGORIZED, NULL);
        }

        return new \Zend_Auth_Result(\Zend_Auth_Result::SUCCESS, $this->user->getId());
    }
}

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