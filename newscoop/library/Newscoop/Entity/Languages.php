<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Languages entity
 * @entity
 * @table(name="Languages")
 */
class Languages
{
    /**
     * @id @generatedValue
     * @column(name="Id", type="integer")
     * @var int
     */
    private $id;

    /**
     * @column(name="Name")
     * @var string
     */
    private $name;


    /**
     * Set language id
     *
     * @param int $p_id
     * @return Newscoop\Entity\Language
     */
    public function setId($p_id)
    {
        $this->id = $p_id;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get language id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id = $p_id;
    }

    /**
     * Set language name
     *
     * @param int $p_name
     * @return Newscoop\Entity\Language
     */
    public function setName($p_name)
    {
        $this->name = $p_name;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get language name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
