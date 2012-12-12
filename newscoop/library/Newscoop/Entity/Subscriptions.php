<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Subscriptions")
 */
class Subscriptions
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="IdUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @ORM\Column(type="string", name="Active")
     * @var string
     */
    private $active;

    /**
     * @ORM\Column(type="float", name="ToPay")
     * @var float
     */
    private $toPay;

    /**
     * @ORM\Column(type="string", name="Currency", length=70)
     * @var string
     */
    private $currency;

    /**
     * @ORM\Column(type="string", name="Type")
     * @var string
     */
    private $type;

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get publication
     * @return Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get user
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
