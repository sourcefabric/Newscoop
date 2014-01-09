<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Attachment entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\AttachmentRepository")
 * @ORM\Table(name="Attachments")
 */
class Attachment
{
    /* Values for enum fields */
    const CONTENT_DISPOSITION_ATTACHMENT = 'attachment';

    const SOURCE_LOCAL = 'local';
    const SOURCE_FEEDBACK = 'feedback';

    private $source_array = array(SOURCE_LOCAL, SOURCE_FEEDBACK);

    const STATUS_APPROVED = 'approved';
    const STATUS_UNAPPORVED = 'unapproved';

    private $status_array = array(STATUS_APPROVED, STATUS_UNAPPORVED);

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="fk_language_id", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinTable(name="ArticleAttachments",
     *      joinColumns={
     *          @ORM\JoinColumn(name="fk_attachment_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="fk_article_number", referencedColumnName="Number"),
     *      }
     *  )
     * @var Doctrine\Common\Collections\Collection
     */
    private $articles;

    /**
     * @ORM\Column(name="file_name", nullable=True)
     * @var string
     */
    private $filename;

    /**
     * @ORM\Column(name="extension", nullable=True)
     * @var string
     */
    private $extension;

    /**
     * @ORM\Column(name="mime_type", nullable=True)
     * @var string
     */
    private $mimeType;

    /**
     * @ORM\Column(name="content_disposition", nullable=True)
     * @var string
     */
    private $contentDisposition;

    /**
     * @ORM\Column(name="http_charset", nullable=True)
     * @var string
     */
    private $httpCharset;

    /**
     * @ORM\Column(type="bigint", name="size_in_bytes", nullable=True)
     * @var string
     */
    private $sizeInBytes;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\Translation", mappedBy="phrase_id")
     * @ORM\JoinColumn(name="phrase_id", referencedColumnName="fk_description_id", nullable=True)
     * @var Newscoop\Entity\Translation
     * TODO: Fix this problem. Related to tables Translations, AutoId, Topics.
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="fk_user_id", referencedColumnName="Id", nullable=True)
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", name="time_created")
     * @var DateTime
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", name="last_modified")
     * @var DateTime
     */
    private $modified;

    /**
     * @ORM\Column(name="Source")
     * @var string
     */
    private $source = SOURCE_LOCAL;

    /**
     * @ORM\Column(name="Status")
     * @var string
     */
    private $status = STATUS_APPROVED;

    private function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->description = new ArrayCollection();
    }

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter for id
     *
     * @param int $id Value to set
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Getter for language
     *
     * @return Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Setter for language
     *
     * @param Newscoop\Entity\Language $language Value to set
     *
     * @return self
     */
    public function setLanguage(\Newscoop\Entity\Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Getter for articles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Setter for articles
     *
     * @param Doctrine\Common\Collections\ArrayCollection $articles Value to set
     *
     * @return self
     */
    public function setArticles(ArrayCollection $articles)
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * Getter for filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Setter for filename
     *
     * @param string $filename Value to set
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Getter for extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Setter for extension
     *
     * @param string $extension Value to set
     *
     * @return self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Getter for mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Setter for mimeType
     *
     * @param string $mimeType Value to set
     *
     * @return self
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Getter for contentDisposition
     *
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * Setter for contentDisposition
     *
     * @param string $contentDisposition Value to set
     *
     * @return self
     */
    public function setContentDisposition($contentDisposition)
    {
        $this->contentDisposition = $contentDisposition;

        return $this;
    }

    /**
     * Getter for httpCharset
     *
     * @return string
     */
    public function getHttpCharset()
    {
        return $this->httpCharset;
    }

    /**
     * Setter for httpCharset
     *
     * @param string $httpCharset Value to set
     *
     * @return self
     */
    public function setHttpCharset($httpCharset)
    {
        $this->httpCharset = $httpCharset;

        return $this;
    }

    /**
     * Getter for sizeInBytes
     *
     * @return string
     */
    public function getSizeInBytes()
    {
        return $this->sizeInBytes;
    }

    /**
     * Setter for sizeInBytes
     *
     * @param string $sizeInBytes Value to set
     *
     * @return self
     */
    public function setSizeInBytes($sizeInBytes)
    {
        $this->sizeInBytes = $sizeInBytes;

        return $this;
    }

    /**
     * Gets description (Translation) phrase id
     * TODO: check if this is needed, see annotation comment for property description
     *
     * @return int|null
     */
    public function getDescriptionId()
    {
        $description = $this->getDescription();

        if ($description !== null && $description->count() > 0) {

            $firstDescription = $description->first();

            return $firstDescription->getPhraseId();
        } else {

            return null;
        }
    }

    /**
     * Getter for description
     *
     * @return Newscoop\Entity\Translation
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Setter for description
     *
     * @param Doctrine\Common\Collections\ArrayCollection $description Value to set
     *
     * @return self
     */
    public function setDescription(ArrayCollection $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter for user
     *
     * @return Newscoop\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Setter for user
     *
     * @param Newscoop\Entity\User|null $user Value to set
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Getter for created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Setter for created
     *
     * @param DateTime $created Value to set
     *
     * @return self
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Getter for modified
     *
     * @return DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Setter for modified
     *
     * @param DateTime $modified Value to set
     *
     * @return self
     */
    public function setModified(\DateTime $modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Getter for source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Setter for source
     *
     * @param string $source Value to set
     *
     * @return self
     */
    public function setSource($source)
    {
        if (!in_array($source, $source_array)) {
            throw new \InvalidArgumentException("Invalid Source.");
        }
        $this->source = $source;

        return $this;
    }

    /**
     * Getter for status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Setter for status
     *
     * @param string $status Value to set
     *
     * @return self
     */
    public function setStatus($status)
    {
        if (!in_array($status, $status_array)) {
            throw new \InvalidArgumentException("Invalid status.");
        }
        $this->status = $status;

        return $this;
    }

    /**
     * Return the relative URL to the attached image.
     *
     * @return string
     */
    public function getAttachmentUri()
    {
        return '/attachment/' . $this->getId()  . '/' . $this->getFilename();
    }
}
