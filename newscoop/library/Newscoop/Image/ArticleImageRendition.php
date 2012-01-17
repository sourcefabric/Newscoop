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
class ArticleImageRendition
{
    /**
     * @Id @Column(type="integer")
     * @var int
     */
    protected $articleNumber;

    /**
     * @Id @ManyToOne(targetEntity="Newscoop\Image\Image", fetch="EAGER")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Image\Image
     */
    protected $image;

    /**
     * @Id @Column(name="rendition_name")
     * @var string
     */
    protected $rendition;

    /**
     * @param Newscoop\Image\ArticleImage $image
     * @param Newscoop\Image\Rendition $rendition
     */
    public function __construct(ArticleImage $image, Rendition $rendition)
    {
        $this->articleNumber = $image->getArticleNumber();
        $this->image = $image->getImage();
        $this->rendition = $rendition->getName();
    }

    /**
     * Get rendition name
     *
     * @return string
     */
    public function getName()
    {
        return $this->rendition;
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
     * Test if is default picture
     *
     * @return bool
     */
    public function isDefault()
    {
        return false;
    }
}
