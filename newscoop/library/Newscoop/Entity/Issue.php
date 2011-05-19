<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Collections\ArrayCollection;

/**
 * Issue entity
 * @Entity
 * @Table(name="Issues", {@UniqueConstraint(name="issues_unique",columns={"IdPublication", "Number", "Language"})})
 */
class Issue extends Entity
{
    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @Column(type="integer", name="Number")
     * @var int
     */
    private $number;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @OneToMany(targetEntity="Newscoop\Entity\Section", mappedBy="issue")
     * @var array
     */
    private $sections;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Template")
     * @JoinColumn(name="IssueTplId", referencedColumnName="Id")
     * @var Newscoop\Entity\Template"
     */
    private $template;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Template")
     * @JoinColumn(name="SectionTplId", referencedColumnName="Id")
     * @var Newscoop\Entity\Template"
     */
    private $sectionTemplate;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Template")
     * @JoinColumn(name="ArticleTplId", referencedColumnName="Id")
     * @var Newscoop\Entity\Template"
     */
    private $articleTemplate;

    /**
     */
    public function __construct()
    {
        $this->sections = new ArrayCollection;
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
     * Get sections
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }
}

