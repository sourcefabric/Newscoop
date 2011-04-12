<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Newscoop\Entity\Languages;

/**
 * Articles entity
 * @entity
 * @table(name="Articles")
 * @entity(repositoryClass="Newscoop\Entity\Repository\ArticlesRepository")
 */
class Articles
{
    /**
     * @id @generatedValue
     * @column(name="Number", type="integer")
     * @var int
     */
    private $id;

    /**
     * @manyToOne(targetEntity="Languages")
     * @joinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Languages
     */
    private $language;

    /**
     * @column(name="comments_enabled")
     * @var int
     */
    private $comments_enabled;

    /**
     * @column(name="comments_locked")
     * @var int
     */
    private $comments_locked;

    /**
     * @column(name="Name")
     * @var string
     */
    private $name;

    /**
     * Get user name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Id
     *
     * @return string
     */
    public function setId($p_id)
    {
        return $this->id = $p_id;
    }

    public function setCommentsEnabled($p_enabled )
    {
        $this->comments_enabled = $p_enabled;
    }
}
