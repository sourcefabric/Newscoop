<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Snippet entity
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\AttachmentRepository")
 * @ORM\Table(name="Attachments")
 */
class Attachment
{
    const CONTENT_DISPOSITION = 'attachment';
    const SOURCE_LOCAL = 'local';
    const SOURCE_FEEDBACK = 'feedback';
    const STATUS_UNAPPROVED = 'unapproved';
    const STATUS_APPROVED = 'approved';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="fk_language_id", referencedColumnName="Id", nullable=true)
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @ORM\Column(name="file_name", nullable=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="extension", length=50, nullable=true)
     * @var string
     */
    private $extension;

    /**
     * @ORM\Column(name="mime_type", nullable=true)
     * @var string
     */
    private $mimeType;

    /**
     * @ORM\Column(name="content_disposition", nullable=true)
     * @var string
     */
    private $contentDisposition;

    /**
     * @ORM\Column(name="http_charset", length=50, nullable=true)
     * @var string
     */
    private $httpCharset;

    /**
     * @ORM\Column(name="size_in_bytes", type="bigint", nullable=true)
     * @var integer
     */
    private $sizeInBytes;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Translation")
     * @ORM\JoinColumn(name="fk_description_id", referencedColumnName="Id", nullable=true, onDelete="SET NULL")
     * @var Newscoop\Entity\Translation
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="fk_user_id", referencedColumnName="Id", nullable=true)
     * @var Newscoop\Entity\User
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", name="last_modified", nullable=false)
     * @var DateTime
     */
    private $updated;

    /**
     * @ORM\Column(type="datetime", name="time_created", nullable=false)
     * @var DateTime
     */
    private $created;

    /**
     * @ORM\Column(name="Source", nullable=false)
     * @var string
     */
    private $source;

    /**
     * @ORM\Column(name="Status", nullable=false)
     * @var string
     */
    private $status;

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param int $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of language.
     *
     * @return Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the value of language.
     *
     * @param \Newscoop\Entity\Language $language the language
     *
     * @return self
     */
    public function setLanguage(\Newscoop\Entity\Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Sets the value of extension.
     *
     * @param string $extension the extension
     *
     * @return self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Gets the value of mimeType.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Sets the value of mimeType.
     *
     * @param string $mimeType the mime type
     *
     * @return self
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Gets the value of contentDisposition.
     *
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * Sets the value of contentDisposition.
     *
     * @param string $contentDisposition the content disposition
     *
     * @return self
     */
    public function setContentDisposition($contentDisposition)
    {
        if ($contentDisposition == self::CONTENT_DISPOSITION) {
            $this->contentDisposition = $contentDisposition;
        }

        return $this;
    }

    /**
     * Gets the value of httpCharset.
     *
     * @return string
     */
    public function getHttpCharset()
    {
        return $this->httpCharset;
    }

    /**
     * Sets the value of httpCharset.
     *
     * @param string $httpCharset the http charset
     *
     * @return self
     */
    public function setHttpCharset($httpCharset)
    {
        $this->httpCharset = $httpCharset;

        return $this;
    }

    /**
     * Gets the value of sizeInBytes.
     *
     * @return integer
     */
    public function getSizeInBytes()
    {
        return $this->sizeInBytes;
    }

    /**
     * Sets the value of sizeInBytes.
     *
     * @param integer $sizeInBytes the size in bytes
     *
     * @return self
     */
    public function setSizeInBytes($sizeInBytes)
    {
        $this->sizeInBytes = $sizeInBytes;

        return $this;
    }

    /**
     * Gets the value of user.
     *
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get User id
     *
     * @return string
     */
    public function getUserId()
    {
        if ($this->user instanceof \Newscoop\Entity\User) {
            return $this->user->getId();
        }

        return null;
    }

    /**
     * Sets the value of user.
     *
     * @param mixed $user the user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the value of updated.
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Sets the value of updated.
     *
     * @param DateTime $updated the updated
     *
     * @return self
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Gets the value of created.
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets the value of created.
     *
     * @param DateTime $created the created
     *
     * @return self
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Gets the value of source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the value of source.
     *
     * @param string $source the source
     *
     * @return self
     */
    public function setSource($source)
    {
        $this->source = self::SOURCE_LOCAL;

        if (in_array($source, array(self::SOURCE_FEEDBACK))) {
            $this->source = $source;
        }

        return $this;
    }

    /**
     * Gets the value of status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the value of status.
     *
     * @param string $status the status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = self::STATUS_UNAPPROVED;

        if (in_array($status, array(self::STATUS_APPROVED))) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * Sets the value of description.
     *
     * @param \Newscoop\Entity\Translations $description the description
     *
     * @return self
     */
    public function setDescription(\Newscoop\Entity\Translation $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return \Newscoop\Entity\Translation
     */
    public function getDescription()
    {
        return $this->description;
    }
}
