<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Acl;

/**
 * Rule entity
 * @entity(repositoryClass="Newscoop\Entity\Repository\Acl\RuleRepository")
 * @table(name="acl_rule")
 */
class Rule
{
    /**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @column
     * @var string
     */
    private $type;

    /** @var array */
    private $types = array('allow', 'deny');

    /**
     * @manyToOne(targetEntity="Newscoop\Entity\Acl\Role", inversedBy="rules")
     * @var Newscoop\Entity\Acl\Role
     */
    private $role;

    /**
     * @oneToOne(targetEntity="Newscoop\Entity\Acl\Resource")
     * @var Newscoop\Entity\Acl\Resource
     */
    private $resource;

    /**
     * @oneToOne(targetEntity="Newscoop\Entity\Acl\Action")
     * @var Newscoop\Entity\Acl\Action
     */
    private $action;

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
     * Set type
     *
     * @param string $type
     * @return Newscoop\Entity\Acl\Rule
     * @throws InvalidArgumentException
     */
    public function setType($type)
    {
        $type = strtolower((string) $type);
        if (!in_array($type, $this->types)) {
            throw new \InvalidArgumentException("Rule type '$type' not allowed.");
        }

        $this->type = (string) $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set role
     *
     * @param Newscoop\Entity\Acl\Role $role
     * @return Newscoop\Entity\Acl\Rule
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Set resource
     *
     * @param Newscoop\Entity\Acl\Resource|NULL $resource
     * @return Newscoop\Entity\Acl\Rule
     */
    public function setResource(Resource $resource = NULL)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Get resource
     *
     * @return Newscoop\Entity\Acl\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get resource id
     *
     * @return int
     */
    public function getResourceId()
    {
        return $this->resource ? $this->resource->getId() : 0;
    }

    /**
     * Set action
     *
     * @param Newscoop\Entity\Acl\Action|NULL $action
     * @return Newscoop\Entity\Acl\Rule
     */
    public function setAction(Action $action = NULL)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get action
     *
     * @return Newscoop\Entity\Acl\Action
     */
    public function getAction()
    {
        return $this->action;
    }
}
