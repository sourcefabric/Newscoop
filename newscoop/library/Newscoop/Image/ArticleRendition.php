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
     * @Id @Column(name="rendition_name")
     * @var string
     */
    protected $renditionName;

    /**
     * @Column(type="integer", name="rendition_width")
     * @var int
     */
    protected $renditionWidth;

    /**
     * @Column(type="integer", name="rendition_height")
     * @var int
     */
    protected $renditionHeight;

    /**
     * @Column(name="rendition_specs")
     * @var int
     */
    protected $renditionSpecs;

    /**
     * @param int $articleNumber
     * @param Newscoop\Image\Rendition $rendition
     * @param Newscoop\Image\ImageInterface $image
     */
    public function __construct($articleNumber, Rendition $rendition, ImageInterface $image)
    {
        $this->articleNumber = (int) $articleNumber;
        $this->image = $image;
        $this->renditionName = $rendition->getName();
        $this->renditionWidth = $rendition->getWidth();
        $this->renditionHeight = $rendition->getHeight();
        $this->renditionSpecs = $rendition->getSpecs();
    }

    /**
     * Get rendition name
     *
     * @return string
     */
    public function getName()
    {
        return $this->renditionName;
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
        return new Rendition($this->renditionWidth, $this->renditionHeight, $this->renditionSpecs, $this->renditionName);
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
