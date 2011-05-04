<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Article entity
 * @entity
 * @table(name="Articles")
 * @entity(repositoryClass="Newscoop\Entity\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @id @generatedValue
     * @column(name="Number", type="integer")
     * @var int
     */
    private $id;

    /**
     * @manyToOne(targetEntity="Language")
     * @joinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @ManyToOne(targetEntity="Publication")
     * @JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @ManyToOne(targetEntity="Section")
     * @JoinColumn(name="NrSection", referencedColumnName="Number")
     * @var Newscoop\Entity\Section
     */
    private $section;

    /**
     * @column(name="NrSection")
     * @var int
     */
    private $sectionId;

    /**
     * @column(name="NrIssue")
     * @var int
     */
    private $issueId;

    /**
     * @column(name="Name")
     * @var string
     */
    private $name;

    /**
     * Set article id
     *
     * @param int $p_id
     * @return Article
     */
    public function setId($p_id)
    {
        $this->id = $p_id;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get article id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get article name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set publication
     *
     * @param  Publication $p_publication
     * @return Article
     */
    public function setPublication(Publication $p_publication)
    {
        $this->publication = $p_publication;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get publication
     *
     * @return Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Get publication id
     *
     * @return int
     */
    public function getPublicationId()
    {
        return $this->publication->getId();
    }

    /**
     * Get section
     *
     * @return Newscoop\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Get section id
     *
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Get issue id
     *
     * @return int
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * Set language
     *
     * @param  Newscoop\Entity\Language $p_language
     * @return Newscoop\Entity\Article
     */
    public function setLanguage(Language $p_language)
    {
        $this->language = $p_language;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get language
     *
     * @return Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language->getId();
    }

}
