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
 * RelatedArticle entity
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\RelatedArticleRepository")
 * @ORM\Table(name="context_articles", indexes={
 *   @ORM\Index(name="article_number", columns={"fk_article_no"}),
 * })
 */
class RelatedArticle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Id")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="fk_context_id")
     * @var integer
     */
    protected $articleListId;

    /**
     * @ORM\Column(type="integer", name="fk_article_no")
     * @var integer
     */
    protected $articleNumber;

    /**
     * @ORM\Column(type="integer", name="order_number")
     * @var integer
     */
    protected $order;

    public function __construct($articleListId, $articleNumber)
    {
        $this->setArticleListId($articleListId);
        $this->setArticleNumber($articleNumber);
        $this->setOrder(0);
    }

    /**
     * Gets the value of articleListId.
     *
     * @return integer
     */
    public function getArticleListId()
    {
        return $this->articleListId;
    }

    /**
     * Sets the value of articleListId.
     *
     * @param integer $articleListId the article list id
     *
     * @return self
     */
    public function setArticleListId($articleListId)
    {
        $this->articleListId = $articleListId;

        return $this;
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
    public function setArticleNumber($articleNumber)
    {
        $this->articleNumber = $articleNumber;

        return $this;
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
     * Gets the value of order.
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets the value of order.
     *
     * @param integer $order the order
     *
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}