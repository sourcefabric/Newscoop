<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Auth;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\User;
use Newscoop\Entity\UserIdentity;

/**
 */
class SocialAuthService implements \Zend_Auth_Adapter_Interface
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var string */
    protected $provider;

    /** @var string */
    protected $providerUserId;

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
        $identity = $this->em->find('Newscoop\Entity\UserIdentity', array(
            'provider' => $this->provider,
            'provider_user_id' => $this->providerUserId,
        ));

        if (empty($identity)) {
            return new \Zend_Auth_Result(\Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, NULL);
        }

        return new \Zend_Auth_Result(\Zend_Auth_Result::SUCCESS, $identity->getUser()->getId());
    }

    /**
     * Set provider
     *
     * @param string $provider
     * @return Newscoop\Services\Auth\SocialAuthService
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Set provider user id
     *
     * @param string $providerUserId
     * @return Newscoop\Services\Auth\SocialAuthService
     */
    public function setProviderUserId($providerUserId)
    {
        $this->providerUserId = $providerUserId;
        return $this;
    }

    /**
     * Add identity
     *
     * @param Newscoop\Entity\User $user
     * @param string $provider
     * @param string $providerUserId
     * @return void
     */
    public function addIdentity(User $user, $provider, $providerUserId)
    {

        $userIdentity = $this->em->getRepository('Newscoop\Entity\UserIdentity')
            ->findOneBy(array(
                'provider' => $provider,
                'provider_user_id' => $providerUserId,
            )
        );

        if ($userIdentity) {
            return $userIdentity;
        }

        $userIdentity = new UserIdentity($provider, $providerUserId, $user);
        $this->em->persist($userIdentity);
        $user->setLastLogin(new \DateTime());
        $this->em->flush();
        return $userIdentity;
    }
}
