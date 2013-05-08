<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Acl;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Role entity
 * @ORM\Entity
 * @ORM\Table(name="acl_role")
 */
class Role
{
    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Acl\Rule", mappedBy="role")
     */
    private $rules;

    /**
     */
    public function __construct()
    {
        $this->rules = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get rules
     *
     * @return ArrayCollection
     */
    public function getRules()
    {
        return $this->rules;
    }
}
