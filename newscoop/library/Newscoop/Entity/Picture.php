<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity
 * @Table(name="Images")
 */
class Picture
{
    const STATUS_APPROVED = 'approved';

    const SOURCE_INGEST = 'newsfeed';

    /**
     * @Id @GeneratedValue
     * @Column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @Column(name="Photographer")
     * @var string
     */
    private $photographer;

    /**
     * @Column(name="Description")
     * @var string
     */
    private $headline;

    /**
     * @Column(name="Caption", nullable=True)
     * @var string
     */
    private $caption;

    /**
     * @Column(name="Source")
     * @var string
     */
    private $source;

    /**
     * @Column(type="datetime", name="Date", nullable=True)
     * @var DateTime
     */
    private $date;

    /**
     * @Column(name="Place", nullable=True)
     * @var string
     */
    private $place;

    /**
     * @Column(name="ContentType", nullable=True)
     * @var string
     */
    private $contentType;

    /**
     * @Column(name="Location", nullable=True)
     * @var string
     */
    private $location;

    /**
     * @Column(name="URL", nullable=True)
     * @var string
     */
    private $url;

    /**
     * @Column(name="ThumbnailFileName", nullable=True)
     * @var string
     */
    private $thumbnailFileName;

    /**
     * @Column(name="ImageFileName", nullable=True)
     * @var string
     */
    private $imageFileName;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\User")
     * @JoinColumn(name="UploadedByUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    private $uploadedBy;

    /**
     * @Column(type="datetime", name="TimeCreated", nullable=True)
     * @var DateTime
     */
    private $created;

    /**
     * @Column(type="datetime", name="LastModified", nullable=True)
     * @var DateTime
     */
    private $updated;

    /**
     * @Column(name="Status", nullable=True)
     * @var string
     */
    private $status;

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
