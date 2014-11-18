<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\View\TopicView;

/**
 * Topic names entity
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\TopicRepository")
 * @ORM\Table(name="TopicNames")
 */
class Topic
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="fk_topic_id")
     * @var int
     * @todo add reference to topic
     */
    protected $id;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Language")
     * @ORM\JoinColumn(name="fk_language_id", referencedColumnName="Id")
     * @var Language
     */
    protected $language;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $name;

    /**
     * Link to topic articles resource
     * @var string
     */
    protected $articlesLink;

    /**
     * Get topic id
     *
     * @return int
     */
    public function getTopicId()
    {
        return $this->id;
    }

    /**
     * Set topic id
     *
     * @param int $id
     *
     * @return int
     */
    public function setTopicId($id)
    {
        $this->id = $id;

        return $id;
    }

    /**
     * Get language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language->getId();
    }

    /**
     * Set language
     *
     * @param Language $language
     *
     * @return Language
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $language;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;

        return $name;
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * Get view
     *
     * @return TopicView
     */
    public function getView()
    {
        $view = new TopicView();
        $view->defined = true;
        $view->identifier = $this->id;
        $view->name = $this->name;
        $view->value = sprintf('%s:%s', $this->name, $this->language->getCode());

        return $view;
    }
}
