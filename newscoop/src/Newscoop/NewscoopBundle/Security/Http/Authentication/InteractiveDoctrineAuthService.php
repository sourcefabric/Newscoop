<?php

/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2015 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Security\Http\Authentication;

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