<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article topic entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\ArticleTopicRepository")
 * @ORM\Table(name="ArticleTopics")
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\ArticleTopicRepository")
 */
class ArticleTopic
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinColumn(name="NrArticle", referencedColumnName="Number")
     */
    protected $article;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Newscoop\NewscoopBundle\Entity\Topic")
     * @ORM\JoinColumn(name="TopicId", referencedColumnName="id")
     */
    protected $topic;

    /**
     * Get article
     *
     * @return Newscoop\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set article
     *
     * @param integer $article
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get Topic
     *
     * @return Newscoop\NewscoopBundle\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set Topic
     *
     * @param integer $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }
}
