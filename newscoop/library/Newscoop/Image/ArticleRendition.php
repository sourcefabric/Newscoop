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
     * @Id @Column(type="integer", name="article_number")
     * @var int
     */
    private $articleNumber;

    /**
     * @Id @Column(name="rendition_name")
     * @var string
     */
    private $renditionName;

    /**
     * @ManyToOne(targetEntity="Newscoop\Image\Image")
     * @JoinColumn(referencedColumnName="Id")
     * @var Newscoop\Image\Image
     */
    private $image;

    /**
     * @param int $articleNumber
     * @param Newscoop\Image\Rendition $rendition
     * @param int $imageId
     */
    public function __construct($articleNumber, Rendition $rendition, Image $image)
    {
        $this->articleNumber = (int) $articleNumber;
        $this->renditionName = (string) $rendition;
        $this->image = $image;
    }

    /**
     * Get rendition name
     *
     * @return string
     */
    public function getRenditionName()
    {
        return $this->renditionName;
    }

    /**
     * Get image id
     *
     * @return int
     */
    public function getImage()
    {
        return $this->image;
    }
}
