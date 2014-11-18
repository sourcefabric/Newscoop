<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Newscoop\Entity\User;

/**
 * Feedback entity
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\FeedbackRepository")
 */
class Feedback
{
    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     * @var Newscoop\Entity\Section
     */
    protected $section;
    
    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="publication_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    protected $publication;
    
    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="article_number", referencedColumnName="Number"),
     *     @ORM\JoinColumn(name="article_language", referencedColumnName="IdLanguage")
     * })
     * @var Newscoop\Entity\Article
     */
    protected $article;
    
    /**
     * @ORM\Column(length=2048)
     * @var text
     */
    protected $message;
    
    /**
     * @ORM\Column(length=128)
     * @var string
     */
    protected $url;
    
    /**
     * @ORM\Column(length=128)
     * @var string
     */
    protected $subject;

    /**
     * @ORM\Column(length=2)
     * @var int
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $time_created;

    /*
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $time_updated;

    /**
     * @var string to code mapper for status
     */
    static $status_enum = array('processed', 'pending', 'starred', 'deleted');
    /**
     * @var string to code mapper for attachment_type
     */
    static $attachment_type_enum = array('none', 'image', 'document');
    
    /**
     * @ORM\Column(length=1)
     * @var int
     */
    protected $attachment_type;
    
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $attachment_id;


    /**
     * Set id
     *
     * @param int $p_id
     * @return Newscoop\Entity\User
     */
    public function setId($p_id)
    {
        $this->id = $p_id;
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
     * Set time created
     *
     * @param DateTime $p_datetime
     * @return Feedback
     */
    public function setTimeCreated(DateTime $p_datetime)
    {
        $this->time_created = $p_datetime;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get creation time.
     *
     * @return DateTime
     */
    public function getTimeCreated()
    {
        return $this->time_created;
    }

    /**
     * Set time updated
     *
     * @param DateTime $p_datetime
     * @return Feedback
     */
    public function setTimeUpdated(DateTime $p_datetime)
    {
        $this->time_updated = $p_datetime;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get update time.
     *
     * @return DateTime
     */
    public function getTimeUpdated()
    {
        return $this->time_updated;
    }
    
    /**
     * Set url.
     *
     * @param string $p_url
     * @return Feedback
     */
    public function setUrl($p_url)
    {
        $this->url = (string)$p_url;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get comment url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Set subject.
     *
     * @param string $p_subject
     * @return Feedback
     */
    public function setSubject($p_subject)
    {
        $this->subject = (string)$p_subject;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message.
     *
     * @param string $p_message
     * @return Feedback
     */
    public function setMessage($p_message)
    {
        $this->message = $p_message;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Set user
     *
     * @param Newscoop\Entity\User $p_user
     * @return Feedback
     */
    public function setUser(User $p_user)
    {
        $this->user = $p_user;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get user
     *
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set section
     *
     * @param Section $p_section
     * @return Feedback
     */
    public function setSection(Section $p_section)
    {
        $this->section = $p_section;
        // return this for chaining mechanism
        return $this;
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
     * Set publication
     *
     * @param Publication $p_publication
     * @return Feedback
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
     * Set article
     *
     * @param Article $p_article
     * @return Feedback
     */
    public function setArticle(Article $p_article)
    {
        $this->article = $p_article;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get article
     *
     * @return Newscoop\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set status
     *
     * @return Feedback
     */
    public function setStatus($status)
    {
        $status_enum = array_flip(self::$status_enum);
        $this->status = $status_enum[$status];
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return self::$status_enum[$this->status];
    }
    
    /**
     * Set attachment type
     *
     * @return Feedback
     */
    public function setAttachmentType($attachment_type)
    {
        $attachment_type_enum = array_flip(self::$attachment_type_enum);
        $this->attachment_type = $attachment_type_enum[$attachment_type];
        return $this;
    }

    /**
     * Get attachment_type
     *
     * @return string
     */
    public function getAttachmentType()
    {
        return self::$attachment_type_enum[$this->attachment_type];
    }
    
    /**
     * Set attachment_id
     *
     * @param integer p_attachment_id
     * @return Feedback
     */
    public function setAttachmentId($attachment_id)
    {
        $this->attachment_id = $attachment_id;
        // return this for chaining mechanism
        return $this;
    }

    /**
     * Get attachment_id
     *
     * @return integer
     */
    public function getAttachmentId()
    {
        return $this->attachment_id;
    }
}
