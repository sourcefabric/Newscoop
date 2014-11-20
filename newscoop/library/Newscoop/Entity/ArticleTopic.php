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
 * @ORM\Entity()
 * @ORM\Table(name="ArticleTopics")
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
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Topic")
     * @ORM\JoinColumn(name="TopicId", referencedColumnName="fk_topic_id")
     */
    protected $topic;

    /**
     * Get article
     *
     * @return integer
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
     * @return integer
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
