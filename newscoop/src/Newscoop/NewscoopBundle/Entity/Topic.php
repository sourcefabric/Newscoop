<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Newscoop\Entity\Article;
use Newscoop\View\TopicView;

/**
 * @Gedmo\Tree(type="nested")
 * @Gedmo\TranslationEntity(class="Newscoop\NewscoopBundle\Entity\TopicTranslation")
 * @ORM\Table(name="main_topics")
 * @ORM\Entity(repositoryClass="Newscoop\NewscoopBundle\Entity\Repository\TopicRepository")
 */
class Topic
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=64)
     */
    protected $title;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    protected $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    protected $rgt;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $root;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    protected $level;

    /**
     * @ORM\OneToMany(targetEntity="Topic", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated;

     /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    public $locale;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    protected $topicOrder;

    /**
     * @ORM\OneToMany(
     *   targetEntity="TopicTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Newscoop\Entity\Article", mappedBy="topics")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ArticleNr", referencedColumnName="Number"),
     *      @ORM\JoinColumn(name="LanguageId", referencedColumnName="IdLanguage")
     *      })
     * @var Newscoop\Entity\Article
     */
    protected $articles;

    /**
     * Link to topic articles resource
     * @var string
     */
    protected $articlesLink;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->articles = new ArrayCollection();
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getTopicId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setTopicId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param mixed $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param mixed $title the title
     *
     * @return self
     */
    public function setName($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of lft.
     *
     * @return mixed
     */
    public function getLeft()
    {
        return $this->lft;
    }

    /**
     * Sets the value of lft.
     *
     * @param mixed $lft the lft
     *
     * @return self
     */
    public function setLeft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Gets the value of rgt.
     *
     * @return mixed
     */
    public function getRight()
    {
        return $this->rgt;
    }

    /**
     * Sets the value of rgt.
     *
     * @param mixed $rgt the rgt
     *
     * @return self
     */
    public function setRight($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Gets the value of parent.
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Gets the integer value of parent.
     *
     * @return integer
     */
    public function getParentAsInteger()
    {
        if ($this->parent) {
            return (int) $this->parent->getId();
        }
    }

    /**
     * Sets the value of parent.
     *
     * @param mixed $parent the parent
     *
     * @return self
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Checkes if topic is root
     *
     * @return boolean
     */
    public function isRoot()
    {
        if ($this->root == $this->id) {
            return true;
        }

        return false;
    }

    /**
     * Gets the value of root.
     *
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Sets the value of root.
     *
     * @param mixed $root the root
     *
     * @return self
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Gets the value of level.
     *
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the value of level.
     *
     * @param mixed $level the level
     *
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Gets the value of children.
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets the value of children.
     *
     * @param mixed $children the children
     *
     * @return self
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param mixed $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = strip_tags($description);

        return $this;
    }

    /**
     * Gets the value of created.
     *
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets the value of created.
     *
     * @param mixed $created the created
     *
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Gets the value of updated.
     *
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Sets the value of updated.
     *
     * @param mixed $updated the updated
     *
     * @return self
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Gets the translations
     *
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Adds the translation
     *
     * @param mixed $translations the translations
     *
     * @return self
     */
    public function addTranslation(TopicTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setObject($this);
        }
    }

    /**
     * Checks if there is translation
     *
     * @param mixed $locale the locale
     *
     * @return self
     */
    public function hasTranslation($locale)
    {
        return isset($this->translations[$locale]);
    }

    /**
     * Gets the translation
     *
     * @param mixed $locale the locale
     *
     * @return mixed
     */
    public function getTranslation($locale)
    {
        if ($this->hasTranslation($locale)) {
            return $this->translations[$locale];
        } else {
            return null;
        }
    }

    /**
     * Gets the Used locale to override Translation listener`s locale
     *
     * @return mixed
     */
    public function getTranslatableLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the Used locale to override Translation listener`s locale
     *
     * @param mixed $locale the locale
     *
     * @return self
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets the value of topic order.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->topicOrder;
    }

    /**
     * Sets the value of topic order.
     *
     * @param int topicOrder the topic order
     *
     * @return self
     */
    public function setOrder($topicOrder = null)
    {
        $this->topicOrder = $topicOrder;

        return $this;
    }

    /**
     * Adds Topic to Article
     *
     * @param Article $article the Article to attach
     *
     * @return Topic
     */
    public function addArticleTopic(Article $article)
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addTopic($this);
        }

        return $this;
    }

    /**
     * Removes Topic from Article
     *
     * @param Article $article the Article to deattach topic
     *
     * @return Topic
     */
    public function removeArticleTopic(Article $article)
    {
        if (!$this->articles->contains($article)) {
            $article->removeTopic($this);
            $this->articles->removeElement($article);
        }

        return $this;
    }

    /**
     * Returns topic's title when echo this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Get view
     *
     * @return Newscoop\View\TopicView
     */
    public function getView()
    {
        $view = new TopicView();
        $view->defined = true;
        $view->identifier = $this->id;
        $view->name = $this->title;
        $view->value = sprintf('%s:%s', $this->title, $this->locale);

        return $view;
    }

    /**
     * Gets object
     *
     * @return Topic
     */
    public function getObject()
    {
        return clone $this;
    }

    /**
     * Set link to topic articles resource
     *
     * @param string $articlesLink Link to topic articles resource
     */
    public function setArticlesLink($articlesLink)
    {
        $this->articlesLink = $articlesLink;

        return $this;
    }

    /**
     * Get link to topic articles resource
     * @return string Link to topic articles resource
     */
    public function getArticlesLink()
    {
        return $this->articlesLink;
    }
}
