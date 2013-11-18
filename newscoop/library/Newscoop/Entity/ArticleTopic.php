<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Newscoop\Utils\Validation;

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
    private $article;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Topic")
     * @ORM\JoinColumn(name="TopicId", referencedColumnName="fk_topic_id")
     */
    private $topic;
    
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
     * @return Newscoop\Entity\TopicNames
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

