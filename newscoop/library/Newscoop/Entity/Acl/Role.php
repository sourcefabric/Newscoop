<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Acl;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Role entity
 * @entity
 * @table(name="acl_role")
 */
class Role implements \Zend_Acl_Role_Interface
{
    /**
     * @id @generatedValue
     * @column(type="integer")
     * @var int
     */
    private $id;

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
     * Get role id for acl
     *
     * @return string
     */
    public function getRoleId()
    {
        return 'role' . $this->getId();
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
