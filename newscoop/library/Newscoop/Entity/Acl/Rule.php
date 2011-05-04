<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Acl;

use Resource\Acl\RuleInterface;

/**
 * Rule entity
 * @entity(repositoryClass="Newscoop\Entity\Repository\Acl\RuleRepository")
 * @table(name="acl_rule")
 */
class Rule implements RuleInterface
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
     * @column(length="80")
     * @var string
     */
    private $resource;

    /**
     * @column(length="80")
     * @var string
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
     * @param string $resource
     * @return Newscoop\Entity\Acl\Rule
     */
    public function setResource($resource)
    {
        $this->resource = (string) $resource;
        return $this;
    }

    /**
     * Get resource
     *
     * @return string|NULL
     */
    public function getResource()
    {
        return empty($this->resource) ? NULL : $this->resource;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Newscoop\Entity\Acl\Rule
     */
    public function setAction($action)
    {
        $this->action = (string) $action;
        return $this;
    }

    /**
     * Get action
     *
     * @return Newscoop\Entity\Acl\Action
     */
    public function getAction()
    {
        return empty($this->action) ? NULL : $this->action;
    }
}
