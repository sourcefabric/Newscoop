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
 * @ORM\Table(name="user_token")
 */
class UserToken
{
    /**
     * @ORM\Id 
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $user;

    /**
     * @ORM\Id 
     * @ORM\Column(type="string", length=40)
     * @var string
     */
    protected $action;

    /**
     * @ORM\Id 
     * @ORM\Column(type="string", length=40)
     * @var string
     */
    protected $token;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $created;

    /**
     * @param string $action
     * @param string $token
     * @param Newscoop\Entity\User $user
     */
    public function __construct(User $user, $action, $token)
    {
        $this->user = $user;
        $this->action = (string) $action;
        $this->token = (string) $token;
        $this->created = new \DateTime();
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
}
