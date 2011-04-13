<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Acl;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Resource entity
 * @entity
 * @table(name="acl_resource")
 */
class Resource implements \Zend_Acl_Resource_Interface
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
     * @manyToMany(targetEntity="Action")
     * @joinTable(name="acl_resource_action")
     */
    private $actions;

    /**
     * @var array
     */
    private $rules = array(array(), array());

    /**
     */
    public function __construct()
    {
        $this->actions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Get resource id for acl
     *
     * @return string|NULL
     */
    public function getResourceId()
    {
        return empty($this->id) ? 0 : strtolower($this->getName());
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Newscoop\Entity\Acl\Resource
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
     * Set actions
     *
     * @param array $actions
     * @return Newscoop\Entity\Acl\Resource
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * Get actions
     *
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set rules
     *
     * @param array $rules
     * @return Newscoop\Entity\Acl\Resource
     */
    public function addRule(Rule $rule, $inherited = FALSE)
    {
        $this->rules[(bool) $inherited][] = $rule;
        return $this;
    }

    /**
     * Get rules
     *
     * @param bool $inherited
     * @return array
     */
    public function getRules($inherited = FALSE)
    {
        return $this->rules[(bool) $inherited];
    }
}
