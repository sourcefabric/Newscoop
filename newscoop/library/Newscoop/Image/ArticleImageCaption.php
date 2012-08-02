<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Newscoop\Entity\Language;

/**
 * Article Image Caption Entity
 * @Entity
 * @Table(name="ArticleImageCaptions")
 */
class ArticleImageCaption
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="ArticleImage", inversedBy="captions")
     * @var Newscoop\Image\ArticleImage
     */
    private $articleImage;

    /**
     * @ManyToOne(targetEntity="LocalImage", inversedBy="captions")
     * @JoinColumn(name="IdImage", referencedColumnName="Id")
     * @var int
     */
    private $image;

    /**
     * @OneToOne(targetEntity="Newscoop\Entity\Language")
     * @JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    private $language;

    /**
     * @Column(type="integer", name="IdLanguage")
     * @var int
     * workaround for indexby field
     */
    private $languageId;

    /**
     * @Column(type="integer", name="NrArticle")
     * @var int
     */
    private $articleNumber;

    /**
     * @Column(length=255)
     * @var string
     */
    private $caption;

    /**
     * @param Newscoop\Image\ArticleImage $articleImage
     * @param Newscoop\Entity\Language $language
     */
    public function __construct(ArticleImage $articleImage, Language $language)
    {
        $this->articleImage = $articleImage;
        $this->language = $language;
        $this->image = $articleImage->getImage();
        $this->articleNumber = $articleImage->getArticleNumber();
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
}
