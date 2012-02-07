<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Local Image
 * @Entity
 * @Table(name="Images")
 */
class LocalImage implements ImageInterface
{
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
     * @Column(name="ImageFileName", nullable=True)
     * @var string
     */
    private $basename;

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
     * @param string $image
     */
    public function __construct($image)
    {
        if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0 || strpos($image, 'file://') === 0) {
            $this->location = 'remote';
            $this->url = (string) $image;
        } else {
            $this->location = 'local';
            $this->basename = (string) $image;
        }
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
        if ($this->isLocal()) {
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
        if ($this->width === null) {
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
        if ($this->height === null) {
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
        $info = $this->isLocal() ?
            getimagesize(APPLICATION_PATH . '/../' . $this->getPath()) : getimagesize($this->url);

        $this->width = (int) $info[0];
        $this->height = (int) $info[1];

        // @todo remove once on image upload is refactored
        \Zend_Registry::get('container')->getService('em')->flush($this);
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
        return $this->location === 'local';
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
     * Get photographer
     *
     * @return string
     */
    public function getPhotographer()
    {
        return $this->photographer;
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
}
