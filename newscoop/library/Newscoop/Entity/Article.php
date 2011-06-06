<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Article entity
 *
 * @Entity
 * @Table(name="Articles")
 */
class Article
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @Id
     * @ManyToOne(targetEntity="Newscoop\Entity\Issue")
     * @JoinColumn(name="NrIssue", referencedColumnName="Number")
     * @var Newscoop\Entity\Issue
     */
    private $issue;

    /**
     * @Id
     * @ManyToOne(targetEntity="Newscoop\Entity\Section")
     * @JoinColumn(name="NrSection", referencedColumnName="Number")
     * @var Newscoop\Entity\Section
     */
    private $section;

    /**
     * @Id
     * @ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @Id
     * @Column(type="integer", name="Number")
     * @var int
     */
    private $number;

    /**
     * @Column(name="Name")
     * @var string
     */
    private $name;

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
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->name;
    }
}
