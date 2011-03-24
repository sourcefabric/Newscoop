<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Acl;

/**
 * Role entity
 * @entity
 * @table(name="acl_role")
 */
class Role
{
    /**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @column(length="80")
     * @var string
     */
    private $name;

    /**
     * @oneToOne(targetEntity="Newscoop\Entity\Acl\Role")
     */
    private $parent;

    /**
     * @oneToMany(targetEntity="Newscoop\Entity\Acl\Rule", mappedBy="role")
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
     * Set name
     *
     * @param string $name
     * @return Newscoop\Entity\Acl\Role
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set parent
     *
     * @param Newscoop\Entity\Acl\Role $parent
     * @return Newscoop\Entity\Acl\Role
     */
    public function setParent(self $parent)
    {
        $this->parent = $parent;
        return $parent;
    }

    /**
     * Get parent
     *
     * @return Newscoop\Entity\Acl\Role|NULL
     */
    public function getParent()
    {
        return $this->parent;
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
