<?php
/**
 * @package Newscoop\CommunityTickerBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\CommunityTickerBundle\Meta;

use Newscoop\CommunityTickerBundle\Entity\CommunityTickerEvent;

/**
 * Meta Community ticker
 */
class MetaCommunityTicker
{
    /** 
     * @var CommunityTickerEvent
     */
    private $feed;

    /** 
     * @var string
     */
    public $type;

    /** 
     * @var array 
     */
    public $params;

    /** 
     * @var MetaComment
     */
    public $comment;

    /** 
     * @var string
     */
    public $created;

    /** 
     * @var MetaArticle
     */
    public $article;

    /** 
     * @var MetaUser
     */
    public $user;

    /** 
     * @var MetaTopic
     */
    public $topic;

    /**
     * @param CommunityTickerEvent $feed
     */
    public function __construct(CommunityTickerEvent $feed = null)
    {
        if (!$feed) {
            return;
        }

        $this->params = $feed->getParams();
        $this->type = implode('-', explode('.', $feed->getEvent()));
        $this->user = $this->getUser($feed);
        $this->comment = $this->getComment();
        $this->created = $this->getCreated($feed);
        $this->article = $this->getArticle();
        $this->topic = $this->getTopic();
    }

    /**
     * Get user
     *
     * @param CommunityTickerEvent $feed
     *
     * @return \MetaUser|null
     */
    protected function getUser($feed)
    {   
        return $feed->getUser() ? new \MetaUser($feed->getUser()) : null;
    }

    /**
     * Get created date
     *
     * @param CommunityTickerEvent $feed
     *
     * @return string
     */
    protected function getCreated($feed)
    {
        $date = $feed->getCreated();

        return $date->format('d.m.Y H:i:s');
    }

    /**
     * Get comment
     *
     * @return \MetaComment|null
     */
    protected function getComment()
    {
        return !empty($this->params['id']) ? new \MetaComment($this->params['id']) : null;
    }

    /**
     * Get article
     *
     * @return \MetaArticle|null
     */
    protected function getArticle()
    {
        return !empty($this->params['number']) ? new \MetaArticle($this->params['language'], $this->params['number']) : null;
    }

    /**
     * Get topic
     *
     * @return \MetaTopic|null
     */
    protected function getTopic()
    {  
        return !empty($this->params['topic_id']) ? new \MetaTopic($this->params['topic_id']) : null;
    }
}