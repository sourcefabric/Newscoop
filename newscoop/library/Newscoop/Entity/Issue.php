<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Issue entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\IssueRepository")
 * @ORM\Table(name="Issues", uniqueConstraints={@ORM\UniqueConstraint(name="issues_unique", columns={"IdPublication", "Number", "IdLanguage"})})
 */
class Issue
{
    const STATUS_PUBLISHED = 'Y';
    const STATUS_NOT_PUBLISHED = 'N';

    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Publication", inversedBy="issues")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    protected $publication;

    /**
     * @ORM\Column(type="integer", name="Number")
     * @var int
     */
    protected $number;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    protected $language;

    /**
     * @ORM\Column(name="Name")
     * @var string
     */
    protected $name = '';

    /**
     * @ORM\Column(name="Published", nullable=True)
     * @var string
     */
    protected $workflowStatus;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Section", mappedBy="issue")
     * @var array
     */
    protected $sections;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Template")
     * @ORM\JoinColumn(name="IssueTplId", referencedColumnName="Id")
     * @var Newscoop\Entity\Template"
     */
    protected $template;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Template")
     * @ORM\JoinColumn(name="SectionTplId", referencedColumnName="Id")
     * @var Newscoop\Entity\Template"
     */
    protected $sectionTemplate;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Template")
     * @ORM\JoinColumn(name="ArticleTplId", referencedColumnName="Id")
     * @var Newscoop\Entity\Template"
     */
    protected $articleTemplate;

    /**
     * @ORM\Column(name="ShortName")
     * @var string
     */
    protected $shortName = '';

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Output\OutputSettingsIssue", mappedBy="issue")
     * @var Newscoop\Entity\Output\OutputSettingsIssue
     */
    protected $outputSettingsIssues;

    /**
     * @param int                         $number
     * @param Newscoop\Entity\Publication $publication
     */
    public function __construct($number, \Newscoop\Entity\Publication $publication = null, \Newscoop\Entity\Language $language = null)
    {
        $this->number = (int) $number;
        $this->sections = new ArrayCollection();

        if ($publication !== null) {
            $this->publication = $publication;
            $this->language = $language !== null ? $language : $this->publication->getDefaultLanguage();
            $this->publication->addIssue($this);
        }
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
        return $this->language !== null ? $this->language->getId() : null;
    }

    /**
     * Get language code
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->language !== null ? $this->language->getCode() : null;
    }

    /**
     * Get publication
     *
     * @return Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Get publication Id
     *
     * @return int
     */
    public function getPublicationId()
    {
        return $this->publication !== null ? $this->publication->getId() : null;
    }

    /**
     * Add section
     *
     * @param  Newscoop\Entity\Section $section
     * @return void
     */
    public function addSection(Section $section)
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
        }
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

    /**
     * Set template
     *
     * @param  Newscoop\Entity\Template $template
     * @return Newscoop\Entity\Issue
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Set section template
     *
     * @param  Newscoop\Entity\Template $template
     * @return Newscoop\Entity\Issue
     */
    public function setSectionTemplate(Template $template)
    {
        $this->sectionTemplate = $template;

        return $this;
    }

    /**
     * Set article template
     *
     * @param  Newscoop\Entity\Template $template
     * @return Newscoop\Entity\Issue
     */
    public function setArticleTemplate(Template $template)
    {
        $this->articleTemplate = $template;

        return $this;
    }

    /**
     * Get name of the issue
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get short name of the issue
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set workflow status
     *
     * @param  string $workflowStatus
     * @return void
     */
    public function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = (string) $workflowStatus;
    }

    /**
     * Get workflow status
     *
     * @return string
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     * Get issue number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }
}
