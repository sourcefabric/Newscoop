<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Article entity
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\ArticleRepository")
 * @ORM\Table(name="Articles")
 */
class Article
{
    const STATUS_PUBLISHED = 'Y';
    const STATUS_NOT_PUBLISHED = 'N';
    const STATUS_SUBMITTED = 'S';
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Issue")
     * @ORM\JoinColumn(name="NrIssue", referencedColumnName="Number")
     * @var Newscoop\Entity\Issue
     */
    private $issue;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Section")
     * @ORM\JoinColumn(name="NrSection", referencedColumnName="Number")
     * @var Newscoop\Entity\Section
     */
    private $section;
    
    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="IdUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $creator;

    /**
     * @ORM\ManyToMany(targetEntity="Author")
     * @ORM\JoinTable(name="ArticleAuthors",
     *      joinColumns={
     *          @ORM\JoinColumn(name="fk_article_number", referencedColumnName="Number"),
     *          @ORM\JoinColumn(name="fk_language_id", referencedColumnName="IdLanguage")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="fk_author_id", referencedColumnName="id")
     *      }
     *  )
     * @var Newscoop\Entity\Authors
     */
    private $authors;

    /**
     * @ORM\ManyToMany(targetEntity="AuthorType")
     * @ORM\JoinTable(name="ArticleAuthors",
     *      joinColumns={
     *          @ORM\JoinColumn(name="fk_article_number", referencedColumnName="Number"),
     *          @ORM\JoinColumn(name="fk_language_id", referencedColumnName="IdLanguage"),
     *          @ORM\JoinColumn(name="fk_author_id", referencedColumnName="IdLanguage"),
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="fk_author_id", referencedColumnName="id")
     *      }
     *  )
     * @var Newscoop\Entity\Authors
     */
    private $articleAuthorTypes;

    /**
     * Article Authors for Newscoop\Gimme
     * @var object
     */
    private $articleAuthors;

    /**
     * @ORM\Column(name="NrSection", nullable=True)
     * @var int
     */
    private $sectionId;

    /**
     * @ORM\Column(name="NrIssue", nullable=True)
     * @var int
     */
    private $issueId;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="Number")
     * @var int
     */
    private $number;

    /**
     * @ORM\Column(name="Name", nullable=True)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="ShortName", nullable=True)
     * @var string
     */
    private $shortName;

    /**
     * @ORM\Column(name="time_updated", nullable=True)
     * @var string
     */
    private $date;

    /**
     * @ORM\Column(name="comments_enabled", nullable=True)
     * @var int
     */
    private $comments_enabled;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="thread", indexBy="language")
     * @var Newscoop\Entity\Comments
     */
    private $comments;

    /**
     * @var string
     */
    private $comments_link;
    
    /**
     * @ORM\Column(name="Type", nullable=True)
     * @var string
     */
    private $type;
    
    /**
     * @ORM\Column(name="PublishDate", nullable=True)
     * @var string
     */
    private $published;
    
    /**
     * @ORM\Column(name="Published", nullable=True)
     * @var string
     */
    private $workflowStatus;

    /**
     * @ORM\Column(type="integer", name="ArticleOrder", nullable=True)
     * @var int
     */
    private $articleOrder;

    /**
     * @ORM\Column(name="Public", nullable=True)
     * @var string
     */
    private $public;

    /**
     * @ORM\Column(name="OnFrontPage", nullable=True)
     * @var string
     */
    private $onFrontPage;

    /**
     * @ORM\Column(name="OnSection", nullable=True)
     * @var string
     */
    private $onSection;

    /**
     * @ORM\Column(type="datetime", name="UploadDate", nullable=True)
     * @var DateTime
     */
    private $uploaded;

    /**
     * @ORM\Column(name="Keywords", nullable=True)
     * @var string
     */
    private $keywords;

    /**
     * @ORM\Column(name="IsIndexed", nullable=True)
     * @var string
     */
    private $isIndexed;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="LockUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $lockUser;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\Topic")
     * @ORM\JoinTable(name="ArticleTopics",
     *      joinColumns={
     *          @ORM\JoinColumn(name="NrArticle", referencedColumnName="Number")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="TopicId", referencedColumnName="fk_topic_id")
     *      }
     *  )
     * @var Newscoop\Entity\Topic
     */
    private $topics;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\Playlist")
     * @ORM\JoinTable(name="playlist_article",
     *      joinColumns={
     *          @ORM\JoinColumn(name="article_no", referencedColumnName="Number")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="id_playlist", referencedColumnName="id_playlist")
     *      }
     *  )
     * @var Newscoop\Entity\Playlist
     */
    private $playlists;

    /**
     * @ORM\Column(type="datetime", name="LockTime", nullable=True)
     * @var DateTime
     */
    private $lockTime;

    /**
     * @ORM\Column(type="integer", name="comments_locked", nullable=True)
     * @var int
     */
    private $commentsLocked;

    /**
     * @ORM\Column(type="integer", name="object_id", nullable=True)
     * @var int
     */
    private $objectId;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Package\Package")
     * @ORM\JoinTable(name="package_article_package",
     *      joinColumns={
     *          @ORM\JoinColumn(name="article_id", referencedColumnName="Number")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="package_id", referencedColumnName="id")
     *      }
     *  )
     * @var Newscoop\Package\Package
     */
    private $packages;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Webcode")
     * @ORM\JoinColumn(name="webcode", referencedColumnName="webcode")
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
     * Set comments_link
     * @param string $link uri for comments resource in Newscoop API
     */
    public function setCommentsLink($link) {
        $this->comments_link = $link;
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

    /**
     * Set articleAuthors
     * $articleAuthors
     */
    public function setArticleAuthors($articleAuthors)
    {
        $this->articleAuthors = $articleAuthors;
    }

    /**
     * Get articleAuthors
     *
     * @return object
     */
    public function getArticleAuthors()
    {
        if (!$this->articleAuthors) {
            $this->articleAuthors = $this->authors;
        }

        return $this->articleAuthors;
    }

    /**
     * Set Packages
     * $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    /**
     * Get packages
     *
     * @return object
     */
    public function getPackages()
    {
        if (count($this->packages) == 0) {
            return null;
        }
        
        return $this->packages;
    }
}
