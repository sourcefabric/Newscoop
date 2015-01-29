<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * RelatedArticles entity
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\RelatedArticlesRepository")
 * @ORM\Table(name="context_boxes")
 */
class RelatedArticles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Id")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="fk_article_no")
     * @var integer
     */
    protected $articleNumber;

    public function __construct($articleNumber)
    {
        $this->setArticleNumber($articleNumber);
    }

    /**
     * Gets the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of articleNumber.
     *
     * @return integer
     */
    public function getArticleNumber()
    {
        return $this->articleNumber;
    }

    /**
     * Sets the value of articleNumber.
     *
     * @param integer $articleNumber the article number
     *
     * @return self
     */
    protected function setArticleNumber($articleNumber)
    {
        $this->articleNumber = $articleNumber;

        return $this;
    }
}