<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Local Image
 * @Entity(repositoryClass="Newscoop\Entity\Repository\ImageRepository")
 * @Table(name="Images")
 */
class LocalImage implements ImageInterface
{
    const LOCATION_LOCAL = 'local';
    const LOCATION_REMOTE = 'remote';

    const BROKEN_FILE = 'image_broken.png';
    const BROKEN_THUMB = 'image_broken_thumbnail.png';
    const BROKEN_WIDTH = 800;
    const BROKEN_HEIGHT = 600;

    /**
     * @Id @Column(type="integer", name="Id") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(name="Location")
     * @var string
     */
    private $location;

    /**
     * @Column(name="ImageFileName", nullable=True, length=80)
     * @var string
     */
    private $basename;

    /**
     * @Column(name="ThumbnailFileName", nullable=True, length=80)
     * @var string
     */
    private $thumbnailPath;

    /**
     * @Column(name="URL", nullable=True)
     * @var string
     */
    private $url;

    /**
     * @Column(nullable=True, name="Description")
     * @var string
     */
    private $description;

    /**
     * @Column(type="integer", nullable=True)
     * @var int
     */
    private $width;

    /**
     * @Column(type="integer", nullable=True)
     * @var int
     */
    private $height;

    /**
     * @Column(nullable=True, name="Photographer")
     * @var string
     */
    private $photographer;
    
    /**
     * @Column(nullable=True, name="Place")
     * @var string
     */
    private $place;
    
    /**
     * @Column(nullable=True, name="Date")
     * @var string
     */
    private $date;

    /**
     * @OneToMany(targetEntity="Newscoop\Image\ArticleRendition", mappedBy="image", cascade={"remove"})
     * @var Doctrine\Common\Collections\Collection
     */
    private $renditions;

    /**
     * @OneToMany(targetEntity="Newscoop\Package\Item", mappedBy="image", cascade={"remove"})
     * @var Doctrine\Common\Collections\Collection
     */
    private $items;

    /**
     * @Column(type="integer", name="is_updated_storage")
     * @var int
     */
    private $isUpdatedStorage = 0;

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

        $this->renditions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set place
     *
     * @param string $place
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
     * Get caption
     *
     * @proxy to getDescription
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->getDescription();
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
     * @param string $thumbnailpath
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
    public function getThumbnailPath()
    {
        return 'images/thumbnails/' . $this->thumbnailPath;
    }
}
