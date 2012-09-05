<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity
 * @Table(name="user_identity")
 */
class UserIdentity
{
    /**
     * @Id
     * @Column(type="string", length=80)
     * @var string
     */
    private $provider;

    /**
     * @Id
     * @Column(type="string", length=255)
     * @var string
     */
    private $provider_user_id;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\User", fetch="EAGER")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @param string $provider
     * @param string $providerUserId
     * @param Newscoop\Entity\User $user
     */
    public function __construct($provider, $providerUserId, User $user)
    {
        $this->provider = $provider;
        $this->provider_user_id = $providerUserId;
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
