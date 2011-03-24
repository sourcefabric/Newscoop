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
}
