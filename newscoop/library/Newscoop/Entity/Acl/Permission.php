<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Acl;

/**
 * Permission entity
 * @Entity
 * @Table(name="liveuser_rights")
 */
class Permission
{
    /**
     * @Id
     * @Column(name="right_define_name")
     * @var string
     */
    private $name;

    /**
     * Return name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
