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
     * @Column(name="ImageFileName")
     * @var string
     */
    private $basename;

    /**
     * @Column(nullable=True, name="Description")
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $info;

    /**
     * @param string $basename
     */
    public function __construct($basename)
    {
        $this->basename = (string) $basename;
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
        return basename($this->basename) === $this->basename ? 'images/' . $this->basename : $this->basename;
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth()
    {
        if ($this->info === null) {
            $this->getInfo();
        }

        return $this->info[0];
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        if ($this->info === null) {
            $this->getInfo();
        }

        return $this->info[1];
    }

    /**
     * Get image info
     *
     * @return array
     */
    private function getInfo()
    {
        return $this->info = getimagesize(APPLICATION_PATH . '/../' . $this->getPath());
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
}
