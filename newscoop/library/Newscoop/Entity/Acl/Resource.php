<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Acl;

/**
 * Resource entity
 * @entity
 * @table(name="acl_resource")
 */
class Resource
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
        return $this->id;
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
     * Get actions
     *
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }
}
