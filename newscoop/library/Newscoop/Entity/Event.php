<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * @entity
 * @table(name="Events")
 */
class Event
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
     * Get event id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Get event name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }
}
