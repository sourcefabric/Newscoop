<?php

/**
 * @package Newscoop\ArticlesBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Entity\User;
use Newscoop\Entity\Hierarchable;

/**
 * @ORM\Entity(repositoryClass="Newscoop\ArticlesBundle\Entity\Repository\EditorialCommentRepository")
 * @ORM\Table(name="editorial_comments")
 */
class EditorialComment implements Hierarchable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="fk_article_number", referencedColumnName="Number"),
     *      @ORM\JoinColumn(name="fk_language_id", referencedColumnName="IdLanguage")
     *  })
     */
    protected $article;

    /**
     * @ORM\Column(type="integer", name="fk_article_number")
     * @var int
     */
    protected $articleNumber;

    /**
     * @ORM\Column(type="integer", name="fk_language_id")
     * @var int
     */
    protected $languageId;

    /**
     * @ORM\Column(type="text", name="comment")
     * @var text
     */
    protected $comment;

    /**
     * @ORM\Column(type="boolean", name="resolved")
     * @var boolean
     */
    protected $resolved = false;

    /**
     * @ORM\ManyToOne(targetEntity="EditorialComment")
     * @ORM\JoinColumn(name="fk_parent_id", referencedColumnName="id", onDelete="SET NULL")
     * @var Newscoop\ArticlesBundle\Entity\EditorialComment
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer", name="fk_parent_id")
     * @var integer
     */
    protected $parentId;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="Id")
     * @var Newscoop\Entity\User
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @var datetime
     */
    protected $created;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     * @var boolean
     */
    protected $is_active = true;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * Gets the value of is_active.
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Sets the value of is_active.
     *
     * @param boolean $is_active the is  active
     *
     * @return self
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param int $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the }).
     *
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Sets the }).
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
     * Gets the value of articleNumber.
     *
     * @return int
     */
    public function getArticleNumber()
    {
        return $this->articleNumber;
    }

    /**
     * Sets the value of articleNumber.
     *
     * @param int $articleNumber the article number
     *
     * @return self
     */
    public function setArticleNumber($articleNumber)
    {
        $this->articleNumber = $articleNumber;

        return $this;
    }

    /**
     * Gets the value of languageId.
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * Sets the value of languageId.
     *
     * @param int $languageId the language id
     *
     * @return self
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * Gets the value of comment.
     *
     * @return text
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the value of comment.
     *
     * @param text $comment the comment
     *
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Gets the value of resolved.
     *
     * @return boolean
     */
    public function getResolved()
    {
        return $this->resolved;
    }

    /**
     * Sets the value of resolved.
     *
     * @param boolean $resolved the resolved
     *
     * @return self
     */
    public function setResolved($resolved)
    {
        $this->resolved = $resolved;

        return $this;
    }

    /**
     * Gets the value of parent.
     *
     * @return Newscoop\ArticlesBundle\Entity\EditorialComment
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the value of parent.
     *
     * @param Newscoop\ArticlesBundle\Entity\EditorialComment $parent the parent
     *
     * @return self
     */
    public function setParent(EditorialComment $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Gets the value of user.
     *
     * @return Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the value of user.
     *
     * @param Newscoop\Entity\User $user the user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the value of created.
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets the value of created.
     *
     * @param datetime $created the created
     *
     * @return self
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Gets the value of parentId.
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Sets the value of parentId.
     *
     * @param integer $parentId the parent id
     *
     * @return self
     */
    protected function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }
}
