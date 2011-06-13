<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Publication entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\PublicationRepository")
 * @Table(name="Publications")
 */
class Publication extends Entity
{
    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /* --------------------------------------------------------------- */

    /**
     * @id @generatedValue
     * @Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @Column(name="Name")
     * @var string
     */
    private $name;

    /**
     * @OneToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="IdDefaultLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @OneToMany(targetEntity="Newscoop\Entity\Issue", mappedBy="publication")
     * @var array
     */
    private $issues;


    /**
     * @column(name="comments_public_enabled")
     * @var bool
     */
    private $public_enabled;

    /**
     * @Column(name="comments_moderator_to")
     * @var string
     */
    private $moderator_to;

    /**
     * @Column(name="comments_moderator_from")
     * @var string
     */
    private $moderator_from;

    /**
     */
    public function __construct()
    {
        $this->issues = new ArrayCollection;
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
     * Get issues
     *
     * @return array
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * Get languages
     *
     * @return array
     */
    public function getLanguages()
    {
        $languages = array();
        foreach ($this->issues as $issue) {
            $languages[] = $issue->getLanguage();
        }

        return $languages;
    }

    /**
     * Get default language of the publication
     *
     * @return Newscoop\Entity\Language
     */
    public function getDefaultLanguage()
    {
        return $this->default_language;
    }

    /**
     * Get default language name of the publication
     *
     * @return string
     */
    public function getDefaultLanguageName()
    {
        return $this->default_language->getName();
    }

    /*
     * Get sections
     *
     * @return array
     */
    public function getSections()
    {
        $added = array();
        $sections = array();
        foreach ($this->issues as $issue) {
            foreach ($issue->getSections() as $section) {
                if (in_array($section->getNumber(), $added)) { // @todo handle within repository
                    continue;
                }

                $sections[] = $section;
                $added[] = $section->getNumber();
            }
        }

        return $sections;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * Set moderator to email address
     *
     * @param string $p_moderator_to
     * @return Publication
     */
    public function setModeratorTo($p_moderator_to)
    {
        return $this->moderator_to = $p_moderator_to;
    }

    /**
     * Get moderator to email address
     *
     * @return string
     */
    public function getModeratorTo()
    {
        return $this->moderator_to;
    }

    /**
     * Set moderator from email address
     *
     * @param string $p_moderator_from
     * @return Publication
     */
    public function setModeratorFrom($p_moderator_from)
    {
        return $this->moderator_to = $p_moderator_from;
    }

    /**
     * Get moderator from email address
     *
     * @return string
     */
    public function getModeratorFrom()
    {
        return $this->moderator_from;
    }
}

