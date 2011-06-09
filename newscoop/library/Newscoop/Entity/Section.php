<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Section entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\SectionRepository")
 * @Table(name="Sections")
 */
class Section extends Entity
{
        /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Issue")
     * @JoinColumn(name="fk_issue_id", referencedColumnName="id")
     * @var Newscoop\Entity\Issue
     */
    private $issue;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
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
     * @ManyToOne(targetEntity="Newscoop\Entity\Template")
     * @JoinColumn(name="SectionTplId", referencedColumnName="Id")
     * @var Newscoop\Entity\Template"
     */
    private $template;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Template")
     * @JoinColumn(name="ArticleTplId", referencedColumnName="Id")
     * @var Newscoop\Entity\Template"
     */
    private $articleTemplate;

    /**
     * @param int $number
     * @param string $name
     */
    public function __construct($number, $name)
    {
        $this->number = (int) $number;
        $this->name = (string) $name;
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

    /**
     * Get language name
     *
     * @return string
     */
    public function getLanguageName()
    {
        return $this->language->getName();
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set template
     *
     * @param Newscoop\Entity\Template $template
     * @return Newscoop\Entity\Section
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Set article template
     *
     * @param Newscoop\Entity\Template $template
     * @return Newscoop\Entity\Section
     */
    public function setArticleTemplate(Template $template)
    {
        $this->articleTemplate = $template;
        return $this;
    }

    /**
     * Get the issue assigned to this section
     *
     * @return Newscoop\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }
}
