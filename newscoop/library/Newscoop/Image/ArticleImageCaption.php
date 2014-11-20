<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Image;

use Newscoop\Entity\Language;
use Doctrine\ORM\Mapping as ORM;

/**
 * Article Image Caption Entity
 * @ORM\Entity
 * @ORM\Table(name="ArticleImageCaptions")
 */
class ArticleImageCaption
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ArticleImage", inversedBy="captions")
     * @var Newscoop\Image\ArticleImage
     */
    protected $articleImage;

    /**
     * @ORM\ManyToOne(targetEntity="LocalImage", inversedBy="captions")
     * @ORM\JoinColumn(name="IdImage", referencedColumnName="Id")
     * @var int
     */
    protected $image;

    /**
     * @ORM\OneToOne(targetEntity="Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="IdLanguage", referencedColumnName="Id")
     * @var Newscoop\Entity\Language
     */
    protected $language;

    /**
     * @ORM\Column(type="integer", name="IdLanguage")
     * @var int
     * workaround for indexby field
     */
    protected $languageId;

    /**
     * @ORM\Column(type="integer", name="NrArticle")
     * @var int
     */
    protected $articleNumber;

    /**
     * @ORM\Column(length=255)
     * @var string
     */
    protected $caption;

    /**
     * @param ArticleImage $articleImage
     * @param Language    $language
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
     *
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
