<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_identity")
 */
class UserIdentity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=80)
     * @var string
     */
    protected $provider;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $provider_user_id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $user;

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
