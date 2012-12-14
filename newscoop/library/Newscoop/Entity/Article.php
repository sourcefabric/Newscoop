<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use ArticleData;
use Newscoop\View\ArticleView;

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
     * @Column(type="datetime", name="time_updated", nullable=true)
     * @var DateTime
     */
    private $updated;

    /**
     * @Column(type="datetime", name="indexed", nullable=true)
     * @var DateTime
     */
    private $indexed;

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
     * @Column(type="datetime", name="PublishDate", nullable=true)
     * @var DateTime
     */
    private $published;
    
    /**
     * @Column(name="Published", nullable=true)
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
     * @ManyToMany(targetEntity="Newscoop\Entity\Author")
     * @JoinTable(name="ArticleAuthors",
     *      joinColumns={
     *          @JoinColumn(name="fk_article_number", referencedColumnName="Number"),
     *          @JoinColumn(name="fk_language_id", referencedColumnName="IdLanguage")
     *      },
     *      inverseJoinColumns={
     *          @JoinColumn(name="fk_author_id", referencedColumnName="id")
     *      }
     *  )
     * @var Doctrine\Common\Collections\Collection
     */
    private $authors;

    /**
     * @ManyToMany(targetEntity="Newscoop\Entity\Topic")
     * @JoinTable(name="ArticleTopics",
     *      joinColumns={
     *          @JoinColumn(name="NrArticle", referencedColumnName="Number")
     *      },
     *      inverseJoinColumns={
     *          @JoinColumn(name="TopicId", referencedColumnName="fk_topic_id")
     *      }
     *  )
     * @var Doctrine\Common\Collections\Collection
     */
    private $topics;

    /**
     * @var ArticleData
     */
    private $data;

    /**
     * @param int $number
     * @param Newscoop\Entity\Language $language
     */
    public function __construct($number, Language $language)
    {
        $this->number = (int) $number;
        $this->language = $language;
        $this->updated = new DateTime();
        $this->authors = new ArrayCollection();
        $this->topics = new ArrayCollection();
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
        return $this->updated;
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

    /**
     * Publish article
     *
     * @return void
     */
    public function publish()
    {
        $this->workflowStatus = self::STATUS_PUBLISHED;
        $this->published = new DateTime();
    }

    /**
     * Set indexed
     *
     * @return void
     */
    public function setIndexed()
    {
        $this->indexed = new DateTime();
        $this->updated = clone $this->updated;
    }

    /**
     * Author article
     *
     * @param string $title
     * @param array $fields
     * @return void
     */
    public function author($title, array $fields)
    {
        $this->name = (string) $title;

        foreach ($fields as $key => $val) {
            $this->setFieldValue($key, $val);
        }
    }

    /**
     * Get view
     *
     * @return object
     */
    public function getView()
    {
        try {
            $view = new ArticleView(array(
                'number' => $this->number,
                'language' => $this->language->getCode(),
                'title' => $this->name,
                'updated' => $this->updated,
                'published' => $this->published,
                'indexed' => $this->indexed,
                'type' => $this->type,
                'webcode' => $this->webcode ? (string) $this->webcode : null,
                'publication_number' => $this->publication ? $this->publication->getId() : null,
                'issue_number' => $this->issue ? $this->issue->getNumber() : null,
                'section_number' => $this->section ? $this->section->getNumber() : null,
                'keywords' => array_filter(explode(',', $this->keywords)),
            ));
        } catch (EntityNotFoundException $e) {
            return new ArticleView();
        }

        $view->authors = $this->authors->map(function ($author) { return $author->getView()->name; })
            ->toArray();

        $view->topics = $this->topics->map(function ($topic) { return $topic->getView()->name; })
            ->toArray();

        $this->addFields($view);
        return $view;
    }

    /**
     * Set article type field value
     *
     * @param string $field
     * @param string $value
     * @return void
     */
    private function setFieldValue($field, $value)
    {
        $this->initArticleData();
        $this->data->setProperty('F' . $field, $value);
    }

    /**
     * Add field properties to view
     *
     * @return array
     */
    private function addFields(ArticleView $view)
    {
        $this->initArticleData();
        foreach ($this->data->getUserDefinedColumns(true) as $column) {
            $columnName = $column->getPrintName();
            if (!property_exists($view, $columnName)) {
                $view->$columnName = $this->data->getFieldValue($columnName);
            }
        }
    }

    /**
     * Init ArticleData
     *
     * @return ArticleData
     */
    private function initArticleData()
    {
        if ($this->data === null) {
            $this->data = new ArticleData($this->type, $this->number, $this->getLanguageId());
        }

        return $this->data;
    }
}
