<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
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
    private $id;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="fk_language_id")
     * @var int
     * @todo add reference to language
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * Link to topic articles resource
     * @var string
     */
    private $articlesLink;

    /**
     * @param int $id
     * @param int $language
     * @param int $name
     */
    public function __construct($id, $language, $name)
    {
        $this->id = (int) $id;
        $this->language = (int) $language;
        $this->name = (string) $name;
    }

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
     * Get language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language;
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
     * Set link to topic articles resource
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
}
