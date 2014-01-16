<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Newscoop\Image\LocalImage;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Newscoop\Package\ItemRepository")
 * @ORM\Table(name="package_item")
 */
class Item
{
    /**
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Package\Package", inversedBy="items")
     * @var Newscoop\Package\Package
     */
    private $package;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Image\LocalImage", inversedBy="items")
     * @ORM\JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Image\LocalImage
     */
    private $image;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $offset;

    /**
     * @ORM\Column(nullable=True)
     * @var string
     */
    private $caption;

    /**
     * @ORM\Column(nullable=True)
     * @var string
     */
    private $coords;

    /**
     * @ORM\Column(nullable=True, name="video_url")
     * @var string
     */
    private $videoUrl;

    /**
     * Item type used by Newscoop API
     * @var string
     */
    private $type;

    /**
     * Item link used by Newscoop API
     * @var string
     */
    private $link;

    /**
     * @param Newscoop\Package\Package $package
     * @param mixed $item
     */
    public function __construct(Package $package, $item)
    {
        if (is_a($item, 'Newscoop\Image\LocalImage')) {
            $this->image = $item;
            $this->setCaption($item->getCaption());
        } else {
            $this->videoUrl = $item->getUrl();
        }

        $this->package = $package;
        $this->offset = (int) count($this->package->getItems());
        $this->package->getItems()->set($this->offset, $this);
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
     * Get image
     *
     * @return Newscoop\Image\LocalImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set offset
     *
     * @param int $offset
     * @return void
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * Get offset
     *
     * @param int $offset
     * @return void
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Get rendition
     *
     * @return Newscoop\Image\Rendition
     */
    public function getRendition()
    {
        $rendition = $this->package->getRendition();
        $rendition->setCoords($this->coords);
        return $rendition;
    }

    /**
     * Set caption
     *
     * @param string $caption
     * @return void
     */
    public function setCaption($caption)
    {
        $this->caption = (string) $caption;
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
     * Set crop coordinates
     *
     * @param string $coords
     * @return void
     */
    public function setCoords($coords)
    {
        $this->coords = (string) $coords;
    }

    /**
     * Test if item is video
     *
     * @return bool
     */
    public function isVideo()
    {
        return $this->videoUrl !== null;
    }

    /**
     * Test if item is image
     *
     * @return bool
     */
    public function isImage()
    {
        return !$this->isVideo();
    }

    /**
     * Set video url
     *
     * @param string $url
     * @return void
     */
    public function setVideoUrl($url)
    {
        $this->videoUrl = (string) $url;
    }

    /**
     * Get video url
     *
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    /**
     * Get package items
     *
     * @return array
     */
    public function getPackageItems()
    {
        return $this->package->getItems();
    }

    /**
     * Get image specs
     *
     * @return string
     */
    public function getImageSpecs()
    {
        return trim($this->package->getRendition()->getSpecs() . '_' . $this->coords, '_');
    }

    /**
     * Get package id
     *
     * @return int
     */
    public function getPackageId()
    {
        return $this->package->getId();
    }

    /**
     * Set Item type
     * @param string $type "video" or "image"
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get Item type
     * @return string "video" or "image"
     */
    public function getType()
    {
        if ($this->videoUrl) {
            return 'video';
        } else if ($this->image){
            return 'image';
        }
        
        return null;
    }

    /**
     * Set link for Item resource
     * @param string $link Link to resource
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Get link for Item resource
     * @return string $link Link to resource
     */
    public function getLink()
    {
        return $this->link;
    }
}
