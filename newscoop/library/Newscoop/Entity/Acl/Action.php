<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Acl;

/**
 * Action entity
 * @entity
 * @table(name="acl_action")
 */
class Action
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
     * @manyToOne(targetEntity="Newscoop\Entity\Acl\Resource")
     * @var Newscoop\Entity\Acl\Resource
     */
    private $resource;

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
     * Get resource
     *
     * @return Newscoop\Entity\Acl\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
