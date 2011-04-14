<?php
/**
 * @package Newscoop
 * @subpackage Languages
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Language entity
 * @entity(repositoryClass="Newscoop\Entity\Repository\LanguageRepository")
 * @table(name="Languages")
 */
class Language
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
     * @column(name="CodePage")
     * @var string
     */
    private $code_page;

    /**
     * @column(name="OrigName")
     * @var string
     */
    private $original_name;

    /**
     * @column(name="Code")
     * @var string
     */
    private $code;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Newscoop\Entity\Language
     */
    public function setName($name)
    {
        $this->name = (string) $name;
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

    /**
     * Set original/native name
     *
     * @param string $name
     * @return Newscoop\Entity\Language
     */
    public function setNativeName($native_name)
    {
        $this->original_name = (string) $native_name;
        return $this;
    }

    public function getNativeName()
    {
        return $this->original_name;
    }

    /**
     * Set code page
     *
     * @param string $name
     * @return Newscoop\Entity\Language
     */
    public function setCodePage($code_page)
    {
        $this->code_page = (string) $code_page;
        return $this;
    }

    public function getCodePage()
    {
        return $this->code_page;
    }

    /**
     * Set code
     *
     * @param string $name
     * @return Newscoop\Entity\Language
     */
    public function setCode($code)
    {
        $this->code = (string) $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }
}