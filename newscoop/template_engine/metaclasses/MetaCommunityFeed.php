<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once __DIR__ . '/MetaDbObject.php';

use Newscoop\Entity\Events\CommunityTickerEvent;

/**
 */
final class MetaCommunityFeed extends MetaDbObject
{
    /** @var Newscoop\Entity\CommunityFeed */
    private $feed;

    private $type;

    /** @var array */
    public $params;

    /**
     * @param Newscoop\Entity\CommunityFeed $feed
     */
    public function __construct(CommunityTickerEvent $feed = null)
    {
        if (!$feed) { // fix getting called once more
            return;
        }

        $this->m_dbObject = $feed;

        $this->m_properties['id'] = 'getId';
        //$this->m_properties['params'] = 'getParams';

        $this->m_customProperties['created'] = 'getCreated';
        $this->m_customProperties['user'] = 'getUser';
        $this->m_customProperties['type'] = 'getType';
        $this->m_customProperties['message'] = 'getMessage';
        $this->m_customProperties['comment'] = 'getComment';
        $this->m_customProperties['article'] = 'getArticle';

        $this->params =  $feed->getParams();

        $this->m_skipFilter[] = "message";

        $this->feed = $feed;
        $this->type = implode('-', explode('.', $this->feed->getEvent()));

        if (in_array($this->type, array('user-register', 'image-approved'))) { // @todo remove when having corrent proxies
            $feed->getUser() ? $feed->getUser()->getId() : 'no user';
        }
    }

    protected function getUser()
    {
        return new MetaUser($this->m_dbObject->getUser());
    }

    protected function getType()
    {
        return $this->type;
    }

    protected function getCreated()
    {
        $date = $this->feed->getCreated();
        return $date->format('d.m.Y H:i:s');
    }

    protected function getMessage()
    {
        switch ($this->type) {
            case 'print-subscribe':
                return sprintf('New print subscriber: %s.', $this->user->name);

            case 'user-register':
                return sprintf('%s subscribed.', $this->user->name);

            case 'topic-follow':
                $params = $this->feed->getParams();
                return sprintf("%s started to follow topic '%s'.", $this->user->name, $params['topic_name']);

            default:
                return sprintf('%s %s', $this->user->name, $this->type);
        }
    }

    /**
     * Get recommended comment
     *
     * @return MetaComment|null
     */
    protected function getComment()
    {
        if ($this->type != 'comment-recommended') {
            return null;
        }

        $params = $this->m_dbObject->getParams();
        return !empty($params['id']) ? new \MetaComment($params['id']) : null;
    }

    /**
     * Get article
     *
     * @return MetaArticle|null
     */
    protected function getArticle()
    {
        if ($this->type != 'blog-published') {
            return null;
        }

        $params = $this->m_dbObject->getParams();
        return !empty($params['number']) ? new \MetaArticle($params['language'], $params['number']) : null;
    }
}
