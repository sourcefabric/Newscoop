<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * @entity
 * @table(name="liveuser_users")
 */
class User
{
    /**
     * @id
     * @column(type="integer")
     * @generatedValue
     * @var int
     */
    private $Id;

    /**
     * @column
     * @var string
     */
    private $Name;

    /**
     * Get user name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }
}
