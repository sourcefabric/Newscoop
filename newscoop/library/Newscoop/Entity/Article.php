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
 * @Entity(repositoryClass="Newscoop\Entity\Repository\ArticleRepository")
 * @Table(name="Articles")
 */
class Article
{
    const STATUS_PUBLISHED = 'Y';
    const STATUS_NOT_PUBLISHED = 'N';
    const STATUS_SUBMITTED = 'S';
    
    /**
     * @Id
     * @ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Issue")
     * @JoinColumn(name="NrIssue", referencedColumnName="Number")
     * @var Newscoop\Entity\Issue
     */
    private $issue;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Section")
     * @JoinColumn(name="NrSection", referencedColumnName="Number")
     * @var Newscoop\Entity\Section
     */
    private $section;
    
    /**
     * @OneToOne(targetEntity="Newscoop\Entity\User")
     * @JoinColumn(name="IdUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $creator;

    /**
     * @column(name="NrSection", nullable=True)
     * @var int
     */
    private $sectionId;

    /**
     * @column(name="NrIssue", nullable=True)
     * @var int
     */
    private $issueId;

    /**
     * @Id
     * @Column(type="integer", name="Number")
     * @var int
     */
    private $number;

    /**
     * @Column(name="Name", nullable=True)
     * @var string
     */
    private $name;

    /**
     * @Column(name="ShortName", nullable=True)
     * @var string
     */
    private $shortName;

    /**
     * @Column(name="time_updated", nullable=True)
     * @var string
     */
    private $date;

    /**
     * @Column(name="comments_enabled", nullable=True)
     * @var int
     */
    private $comments_enabled;
    
    /**
     * @Column(name="Type", nullable=True)
     * @var string
     */
    private $type;
    
    /**
     * @Column(name="PublishDate", nullable=True)
     * @var string
     */
    private $published;
    
    /**
     * @Column(name="Published", nullable=True)
     * @var string
     */
    private $workflowStatus;

    /**
     * @Column(type="integer", name="ArticleOrder", nullable=True)
     * @var int
     */
    private $articleOrder;

    /**
     * @Column(name="Public", nullable=True)
     * @var string
     */
    private $public;

    /**
     * @Column(name="OnFrontPage", nullable=True)
     * @var string
     */
    private $onFrontPage;

    /**
     * @Column(name="OnSection", nullable=True)
     * @var string
     */
    private $onSection;

    /**
     * @Column(type="datetime", name="UploadDate", nullable=True)
     * @var DateTime
     */
    private $uploaded;

    /**
     * @Column(name="Keywords", nullable=True)
     * @var string
     */
    private $keywords;

    /**
     * @Column(name="IsIndexed", nullable=True)
     * @var string
     */
    private $isIndexed;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\User")
     * @JoinColumn(name="LockUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $lockUser;

    /**
     * @Column(type="datetime", name="LockTime", nullable=True)
     * @var DateTime
     */
    private $lockTime;

    /**
     * @Column(type="integer", name="comments_locked", nullable=True)
     * @var int
     */
    private $commentsLocked;

    /**
     * @Column(type="integer", name="object_id", nullable=True)
     * @var int
     */
    private $objectId;

    /**
     * @OneToOne(targetEntity="Newscoop\Entity\Webcode")
     * @JoinColumn(name="webcode", referencedColumnName="webcode")
     * @var Newscoop\Entity\Webcode
     */
    private $webcode;

    /**
     * @param int $number
     * @param Newscoop\Entity\Language $language
     */
    public function __construct($number, Language $language)
    {
        $this->number = (int) $number;
        $this->language = $language;
    }

    /**
     * Set article id
     *
     * @param int $p_id
     * @return Article
     */
    public function setId($p_id)
    {
        $this->number = $p_id;
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
        return $this->number;
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
        return ($this->publication) ? $this->publication->getId() : null;
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
    
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
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
        return ($this->language) ? $this->language->getId() : null;
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

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get whether commenting is enabled
     *
     * @return int
     */
    public function commentsEnabled()
    {
        return (int) $this->comments_enabled;
    }
    
    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Get publishDate
     *
     * @return string
     */
    public function getPublishDate()
    {
        return $this->published;
    }
    
    /**
     * Set creator
     *
     * @param  User $p_user
     * @return Article
     */
    public function setCreator(User $p_user)
    {
        $this->creator = $p_user;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get creator
     *
     * @return Newscoop\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set webcode
     *
     * @param Newscoop\Entity\Webcode $webcode
     * @return void
     */
    public function setWebcode($webcode)
    {
        $this->webcode = $webcode;
    }

    /**
     * Get webcode
     *
     * @return string
     */
    public function getWebcode()
    {
        return (string) $this->webcode;
    }

    /**
     * Test if article has webcode
     *
     * @return bool
     */
    public function hasWebcode()
    {
        return isset($this->webcode);
    }
}
