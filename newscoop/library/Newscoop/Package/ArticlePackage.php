<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="package_article_package")
 */
class ArticlePackage
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="Number")
     */
    protected $article;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Package")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id")
     */
    protected $package;

    /**
     * Gets the value of article.
     *
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Sets the value of article.
     *
     * @param mixed $article the article
     *
     * @return self
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Gets the value of package.
     *
     * @return mixed
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Sets the value of package.
     *
     * @param mixed $package the package
     *
     * @return self
     */
    public function setPackage($package)
    {
        $this->package = $package;

        return $this;
    }
}
