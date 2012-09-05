<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity
 * @Table(name="user_token")
 */
class UserToken
{
    /**
     * @Id @ManyToOne(targetEntity="Newscoop\Entity\User")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @Id @Column(type="string", length=40)
     * @var string
     */
    private $action;

    /**
     * @Id @Column(type="string", length=40)
     * @var string
     */
    private $token;

    /**
     * @Column(type="datetime")
     * @var DateTime
     */
    private $created;

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
