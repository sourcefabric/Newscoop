<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

/**
 * Article Rendition
 * @Entity
 */
class ArticleRendition
{
    /**
     * @Id @Column(type="integer")
     * @var int
     */
    protected $articleNumber;

    /**
     * @Id @ManyToOne(targetEntity="Newscoop\Image\LocalImage", fetch="EAGER")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Image\Image
     */
    protected $image;

    /**
     * @Column(nullable=True)
     * @var string
     */
    protected $imageSpecs;

    /**
     * @Id @ManyToOne(targetEntity="Newscoop\Image\Rendition", fetch="EAGER")
     * @JoinColumn(referencedColumnName="name")
     * @var Newscoop\Image\Rendition
     */
    protected $rendition;

    /**
     * @param int $articleNumber
     * @param Newscoop\Image\Rendition $rendition
     * @param Newscoop\Image\ImageInterface $image
     * @param string $imageSpecs
     */
    public function __construct($articleNumber, Rendition $rendition, ImageInterface $image, $imageSpecs = null)
    {
        $this->articleNumber = (int) $articleNumber;
        $this->rendition = $rendition;
        $this->image = $image;
        $this->imageSpecs = $imageSpecs;
    }

    /**
     * Get rendition name
     *
     * @return string
     */
    public function getName()
    {
        return $this->rendition->getName();
    }

    /**
     * Get image id
     *
     * @return Newscoop\Image\ArticleImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get rendition
     *
     * @return Newscoop\Image\Rendition
     */
    public function getRendition()
    {
        $rendition = clone $this->rendition;
        $rendition->setCoords($this->imageSpecs);
        return $rendition;
    }

    /**
     * Get specs
     *
     * @return string
     */
    public function getImageSpecs()
    {
        return $this->imageSpecs;
    }

    /**
     * Test if is default picture
     *
     * @return bool
     */
    public function isDefault()
    {
        return false;
    }
}
