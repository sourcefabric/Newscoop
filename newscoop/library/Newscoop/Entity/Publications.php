<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Newscoop\Entity\Languages;

/**
 * Publications entity
 * @entity
 * @table(name="Publications")
 */
class Publications
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
     * @manyToOne(targetEntity="Languages")
     * @joinColumn(name="IdDefaultLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Languages
     */
    private $default_language;

    /**
     * Set publication id
     *
     * @param int $p_id
     * @return Newscoop\Entity\Publications
     */
    public function setId($p_id)
    {
        $this->id = $p_id;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get publication id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id = $p_id;
    }

    /**
     * Set publication name
     *
     * @param int $p_name
     * @return Newscoop\Entity\Publications
     */
    public function setName($p_name)
    {
        $this->name = $p_name;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get publication name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set default language of the publication
     *
     * @param Language $
     * @return Newscoop\Entity\Publications
     */
    public function setDefaultLanguage(Languages $p_language)
    {
        $this->default_language = $p_language;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get default language of the publication
     *
     * @return Newscoop\Entity\Languages
     */
    public function getDefaultLanguage()
    {
        return $this->default_language;
    }

    /**
     * Get default language name of the publication
     *
     * @return Newscoop\Entity\Languages
     */
    public function getDefaultLanguageName()
    {
        return $this->default_language->getName();
    }

}
