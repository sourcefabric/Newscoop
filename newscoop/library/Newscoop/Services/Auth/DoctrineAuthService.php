<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Auth;

use Doctrine\ORM\EntityManager;

/**
 * Doctrine Auth service
 *
 * @deprecated in 4.3, will be removed in 4.4
 */
class DoctrineAuthService implements \Zend_Auth_Adapter_Interface
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var string */
    protected $email;

    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /** @var bool */
    protected $is_admin = false;

    /** @var bool */
    protected $is_external = false;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Perform authentication attempt
     *
     * @return \Zend_Auth_Result
     */
    public function authenticate()
    {
        if ($this->is_admin || !empty($this->username)) {
            $params = array('username' => $this->username);
        } elseif (!empty($this->email)) {
            $params = array('email' => $this->email);
        }

        $user = isset($params)
            ? $this->em->getRepository('Newscoop\Entity\User')->findOneBy($params)
            : null;

        if (empty($user)) {
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, NULL);
        }

        if (!$user->isActive()) {
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE_UNCATEGORIZED, NULL);
        }

        if ($this->is_admin && !$user->isAdmin()) {
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE_UNCATEGORIZED, NULL);
        }

        if (!$this->is_external && !$user->checkPassword($this->password)) {
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, NULL);
        }

        $user->setLastLogin(new \DateTime());
        $this->em->flush(); // store updated password
        return new \Zend_Auth_Result(\Zend_Auth_Result::SUCCESS, $user->getId());
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Newscoop\Services\Auth\DoctrineAuthService
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Newscoop\Services\Auth\DoctrineAuthService
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set is admin constrain
     *
     * @param bool $admin
     * @return Newscoop\Services\AuthService
     */
    public function setAdmin($admin = true)
    {
        $this->is_admin = (bool) $admin;
        return $this;
    }

    /**
     * Set is external constrain
     *
     * @param bool $external
     * @return Newscoop\Services\AuthService
     */
    public function setExternal($external = true)
    {
        $this->is_external = (bool) $external;
        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Newscoop\Services\AuthService
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;
        return $this;
    }

    /**
     * Find by credentials
     *
     * @param string $email
     * @param string $password
     * @return Newscoop\Entity\User
     */
    public function findByCredentials($email, $password)
    {
        $user = $this->em->getRepository('Newscoop\Entity\User')
            ->findOneBy(array(
                'email' => $email,
            ));

        if (empty($user) || !$user->isActive() || !$user->checkPassword($password)) {
            return null;
        }

        return $user;
    }
}
