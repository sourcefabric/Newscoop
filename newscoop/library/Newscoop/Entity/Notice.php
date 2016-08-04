<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notices entity
 * @Entity(repositoryClass="Newscoop\Entity\Repository\NoticeRepository")
 * @Table(name="notice")
 */
class Notice
{
    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Publication")
     * @JoinColumn(name="publication_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    private $publication;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="language_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\User")
     * @JoinColumn(name="user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $author;

    /**
     * @var string $firstname
     *
     * @Column(type="string")
     */
    private $firstname;

    /**
     * @var string $lastname
     *
     * @Column(type="string")
     */
    private $lastname;
    /**
     * @Column(name="title")
     * @var string
     */
    private $title = '';

    /**
     * @Column(name="sub_title")
     * @var string
     */
    private $sub_title = '';

    /**
     * @Column(name="body")
     * @var string
     */
    private $body = '';

    /**
     * @Column(name="zipcode")
     * @var string
     */
    private $zipcode = '';

    /**
     * @Column(name="phone")
     * @var string
     */
    private $phone = '';

    /**
     * @Column(name="adress")
     * @var string
     */
    private $adress = '';

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @Column(type="datetime")
     */
    private $updated;

    /**
     * @var datetime $published
     *
     * @Column(type="datetime",nullable=true)
     */
    private $date;

    /**
     * @var datetime $published
     *
     * @Column(type="datetime")
     */
    private $published;

    /**
     * @var datetime $published
     *
     * @Column(type="datetime")
     */
    private $unpublished;

    /**
     * @column(type="smallint",nullable=true)
     * @var int
     */
    private $priority;

    /**
     * @column(type="smallint")
     * @var int
     */
    private $status;


    /**
     * @ManyToMany(targetEntity="Newscoop\Entity\NoticeCategory")
     */
    private $categories;

    /**
     * @var string to code mapper for status
     */
    static $status_enum = array('published', 'submitted', 'deleted');

    /**
     * @param int $number
     * @param Newscoop\Entity\Publication $publication
     */
    public function __construct()
    {/*
        if ($publication !== null) {
            $this->publication = $publication;
            $this->language = $language !== null ? $language : $this->publication->getDefaultLanguage();
        }*/
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
     * Set title of notice
     *
     * @return string
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get name of the issue
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get short name of the issue
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param \Newscoop\Entity\datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \Newscoop\Entity\datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param \Newscoop\Entity\datetime $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return \Newscoop\Entity\datetime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $status_enum = self::$status_enum;

        $status_id = $this->getStatus();
        if(key_exists($status_id,$status_enum)){
            return $status_enum[$status_id];
        }
        return false;
    }

    /**
     * @param string $sub_title
     */
    public function setSubTitle($sub_title)
    {
        $this->sub_title = $sub_title;
    }

    /**
     * @return string
     */
    public function getSubTitle()
    {
        return $this->sub_title;
    }

    /**
     * @param \Newscoop\Entity\datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return \Newscoop\Entity\datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param \Newscoop\Entity\Newscoop\Entity\Publication $publication
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;
    }

    /**
     * @return \Newscoop\Entity\Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @param \Newscoop\Entity\datetime $unpublished
     */
    public function setUnpublished($unpublished)
    {
        $this->unpublished = $unpublished;
    }

    /**
     * @return \Newscoop\Entity\datetime
     */
    public function getUnpublished()
    {
        return $this->unpublished;
    }

    /**
     * @param string $adress
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;
    }

    /**
     * @return string
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * @param \Newscoop\Entity\datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return \Newscoop\Entity\datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $zipcode
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param \Newscoop\Entity\Newscoop\Entity\User $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return \Newscoop\Entity\Newscoop\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}

