<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Images")
 */
class Picture
{
    const STATUS_APPROVED = 'approved';

    const SOURCE_INGEST = 'newsfeed';

    /**
     * @ORM\Id @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Id")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="Photographer")
     * @var string
     */
    protected $photographer;

    /**
     * @ORM\Column(name="Description")
     * @var string
     */
    protected $headline;

    /**
     * @ORM\Column(name="Caption", nullable=True)
     * @var string
     */
    protected $caption;

    /**
     * @ORM\Column(name="Source")
     * @var string
     */
    protected $source;

    /**
     * @ORM\Column(type="datetime", name="Date", nullable=True)
     * @var DateTime
     */
    protected $date;

    /**
     * @ORM\Column(name="Place", nullable=True)
     * @var string
     */
    protected $place;

    /**
     * @ORM\Column(name="ContentType", nullable=True)
     * @var string
     */
    protected $contentType;

    /**
     * @ORM\Column(name="Location", nullable=True)
     * @var string
     */
    protected $location;

    /**
     * @ORM\Column(name="URL", nullable=True)
     * @var string
     */
    protected $url;

    /**
     * @ORM\Column(name="ThumbnailFileName", nullable=True)
     * @var string
     */
    protected $thumbnailFileName;

    /**
     * @ORM\Column(name="ImageFileName", nullable=True)
     * @var string
     */
    protected $imageFileName;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="UploadedByUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $uploadedBy;

    /**
     * @ORM\Column(type="datetime", name="TimeCreated", nullable=True)
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="LastModified", nullable=True)
     * @var DateTime
     */
    protected $updated;

    /**
     * @ORM\Column(name="Status", nullable=True)
     * @var string
     */
    protected $status;

    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get photographer
     *
     * @return string
     */
    public function getPhotographer()
    {
        return $this->photographer;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Test if is approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }
}
