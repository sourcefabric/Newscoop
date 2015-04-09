<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Local Image
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\ImageRepository")
 * @ORM\Table(name="Images", indexes={
 *   @ORM\Index(name="is_updated_storage", columns={"is_updated_storage"}),
 *   @ORM\Index(name="Description", columns={"Description"}),
 *   @ORM\Index(name="Photographer", columns={"Photographer"}),
 *   @ORM\Index(name="Place", columns={"Place"}),
 * })
 */
class LocalImage implements ImageInterface
{
    const LOCATION_LOCAL = 'local';
    const LOCATION_REMOTE = 'remote';

    const BROKEN_FILE = 'image_broken.png';
    const BROKEN_THUMB = 'image_broken_thumbnail.png';
    const BROKEN_WIDTH = 800;
    const BROKEN_HEIGHT = 600;

    const STATUS_UNAPPROVED = 'unapproved';
    const STATUS_APPROVED = 'approved';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="Id", length=10)
     * @ORM\GeneratedValue
     * @var int
     */
    public $id;

    /**
     * @ORM\Column(name="Location")
     * @var string
     */
    protected $location;

    /**
     * @ORM\Column(name="ImageFileName", nullable=True, length=80)
     * @var string
     */
    protected $basename;

    /**
     * @ORM\Column(name="ThumbnailFileName", nullable=True, length=80)
     * @var string
     */
    protected $thumbnailPath;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="UploadedByUser", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", name="TimeCreated", nullable=true)
     * @var DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", name="LastModified", nullable=true)
     * @var DateTime
     */
    protected $updated;

    /**
     * @ORM\Column(name="URL", nullable=True)
     * @var string
     */
    protected $url;

    /**
     * @ORM\Column(type="text", nullable=True, name="Description")
     * @var text
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", nullable=true, name="width")
     * @var int
     */
    protected $width;

    /**
     * @ORM\Column(type="integer", nullable=true, name="height")
     * @var int
     */
    protected $height;

    /**
     * @ORM\Column(nullable=True, name="Photographer")
     * @var string
     */
    protected $photographer;

    /**
     * @ORM\Column(nullable=True, name="photographer_url")
     * @var string
     */
    protected $photographerUrl;

    /**
     * @ORM\Column(nullable=True, name="Place")
     * @var string
     */
    protected $place;

    /**
     * @ORM\Column(nullable=True, name="Date")
     * @var string
     */
    protected $date;

    /**
     * @ORM\Column(name="ContentType")
     * @var string
     */
    protected $contentType;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Image\ArticleRendition", mappedBy="image", cascade={"remove"})
     * @var Doctrine\Common\Collections\Collection
     */
    protected $renditions;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Package\Item", mappedBy="image", cascade={"remove"})
     * @var Doctrine\Common\Collections\Collection
     */
    protected $items;

    /**
     * @ORM\Column(type="integer", name="is_updated_storage")
     * @var int
     */
    protected $isUpdatedStorage = 0;

    /**
     * @ORM\Column(name="Source", nullable=true)
     * @var string
     */
    protected $source;

    /**
     * @ORM\Column(type="string", name="Status")
     * @var string
     */
    protected $status;

    /**
     * @ORM\OneToMany(targetEntity="ArticleImageCaption", mappedBy="image")
     * @var array
     */
    protected $captions;

    /**
    * Virtual property set by rest api with connection to articleImage 
    */
    protected $articleImageId;

    /**
     * @param string $image
     */
    public function __construct($image = '')
    {
        if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0 || strpos($image, 'file://') === 0) {
            $this->location = self::LOCATION_REMOTE;
            $this->url = (string) $image;
        } else {
            $this->location = self::LOCATION_LOCAL;
            $this->basename = (string) $image;
        }

        $this->renditions = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->captions = new ArrayCollection();
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
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        if ($this->hasUpdatedStorage()) {
            return 'images/' . $this->basename;
        } elseif ($this->isLocal()) {
            return basename($this->basename) === $this->basename ? 'images/' . $this->basename : $this->basename;
        } else {
            return $this->url;
        }
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth()
    {
        if (empty($this->width)) {
            $this->getInfo();
        }

        return $this->width;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        if (empty($this->height)) {
            $this->getInfo();
        }

        return $this->height;
    }

    /**
     * Get image info
     *
     * @return array
     */
    private function getInfo()
    {
        $filename = $this->isLocal() ? APPLICATION_PATH . '/../' . $this->getPath() : $this->url;
        if ($this->isLocal() && !file_exists($filename)) {
            $this->setBroken();

            return;
        }

        try {
            $info = getimagesize($filename);
        } catch (\Exception $e) {
            $this->setBroken();

            return;
        }

        if (!is_array($info) || empty($info[0]) || empty($info[1])) {
            $this->setBroken();

            return;
        }

        $this->width = (int) $info[0];
        $this->height = (int) $info[1];

        $this->saveEntity();
    }

    /**
     * Set image as broken
     *
     * @return void
     */
    private function setBroken()
    {
        $this->location = self::LOCATION_LOCAL;
        $this->basename = self::BROKEN_FILE;
        $this->thumbnailPath = self::BROKEN_THUMB;
        $this->width = self::BROKEN_WIDTH;
        $this->height = self::BROKEN_HEIGHT;
        $this->isUpdatedStorage = true;
        $this->saveEntity();
    }

    /**
     * Store updated info if persisted
     *
     * @return void
     *
     * @todo remove once on image upload is refactored
     */
    private function saveEntity()
    {
        $em = \Zend_Registry::get('container')->getService('em');
        if ($em->contains($this)) {
            $em->flush($this);
        }
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Test if is local image
     *
     * @return bool
     */
    public function isLocal()
    {
        return $this->location === self::LOCATION_LOCAL;
    }

    /**
     * Test if image has defined width
     *
     * @return bool
     */
    public function hasWidth()
    {
        return $this->width !== null;
    }

    /**
     * Set photographer
     *
     * @param string $photographer
     *
     * @return void
     */
    public function setPhotographer($photographer)
    {
        $this->photographer = (string) $photographer;
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
     * Set photographer url
     *
     * @param string $url
     *
     * @return void
     */
    public function setPhotographerUrl($url)
    {
        $this->photographerUrl = (string) $url;
    }

    /**
     * Get photographer url
     *
     * @return string
     */
    public function getPhotographerUrl()
    {
        return $this->photographerUrl;
    }

    /**
    * Get caption
    *
    * Proxy to getDescription
    *
    * @return string
    */
    public function getCaption()
    {
        return $this->getDescription();
    }

    /**
    * Get captions
    *
    * @return array
    */
    public function getCaptions()
    {
        $captions = array();
        foreach ($this->captions as $languageId => $caption) {
            $captions[$languageId] = $caption->getCaption();
        }

        return $captions;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return void
     */
    public function setPlace($place)
    {
        $this->place = (string) $place;
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

    /**
     * Set date
     *
     * @param string $date
     *
     * @return void
     */
    public function setDate($date)
    {
        $this->date = (string) $date;
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
     * Test is storage was updated
     *
     * @return bool
     */
    public function hasUpdatedStorage()
    {
        return (bool) $this->isUpdatedStorage;
    }

    /**
     * Update storage
     *
     * @param string $path
     * @param string $thumbnailPath
     *
     * @return void
     */
    public function updateStorage($path, $thumbnailPath)
    {
        $this->upload($path, $thumbnailPath);
        $this->isUpdatedStorage = true;
    }

    /**
     * Upload image
     *
     * @param string $path
     * @param string $thumbnailPath
     *
     * @return void
     */
    public function upload($path, $thumbnailPath)
    {
        $this->basename = $path;
        $this->thumbnailPath = $thumbnailPath;
    }

    /**
     * Get thumbnail path
     *
     * @return string
     */
    public function getThumbnailPath($clear = false)
    {
        if ($clear) {
            return $this->thumbnailPath;
        }

        return 'images/thumbnails/' . $this->thumbnailPath;
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
     * Gets the value of contentType.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets the value of contentType.
     *
     * @param string $contentType the content type
     *
     * @return self
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Gets the value of user.
     *
     * @return \Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the value of user.
     *
     * @param \Newscoop\Entity\User $user the user
     *
     * @return self
     */
    public function setUser(\Newscoop\Entity\User $user = null)
    {
        $this->user = $user;

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
    public function setStatus($status = null)
    {
        $this->status = self::STATUS_UNAPPROVED;

        if ($status == self::STATUS_APPROVED) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * Sets the value of width.
     *
     * @param int $width the width
     *
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Sets the value of height.
     *
     * @param int $height the height
     *
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Sets the value of thumbnailPath.
     *
     * @param string $thumbnailPath the thumbnail path
     *
     * @return self
     */
    public function setThumbnailPath($thumbnailPath)
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    /**
     * Sets the value of basename.
     *
     * @param string $basename the basename
     *
     * @return self
     */
    public function setBasename($basename)
    {
        $this->basename = $basename;

        return $this;
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
        $this->source = $source;

        return $this;
    }

    /**
     * Gets the value of basename.
     *
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * Sets the value of url.
     *
     * @param string $url the url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Gets articleImage.
     *
     * @return mixed
     */
    public function getArticleImageId()
    {
        return $this->articleImageId;
    }

    /**
     * Sets articleImage.
     *
     * @param mixed $articleImageId the article image id
     *
     * @return self
     */
    public function setArticleImageId($articleImageId)
    {
        $this->articleImageId = $articleImageId;

        return $this;
    }
}
