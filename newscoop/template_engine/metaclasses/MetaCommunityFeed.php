<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

use Newscoop\Entity\Events\CommunityTickerEvent;

/**
 */
final class MetaCommunityFeed extends MetaDbObject
{
    /** @var MetaUser */
    private $user;

    /** @var Newscoop\Entity\CommunityFeed */
    private $feed;

    private $type;

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

        $this->m_skipFilter[] = "message";

        $this->feed = $feed;
        $this->type = implode('-', explode('.', $this->feed->getEvent()));
        $this->user = new MetaUser($feed->getUser());
    }

    protected function getUser()
    {
        return $this->user;
    }

    protected function getType()
    {
        return $this->type;
    }

    protected function getCreated()
    {
        $date = $this->feed->getCreated();
        return $date->format('d.m.Y');
    }

    protected function getMessage()
    {
        switch ($this->type) {
            case 'print-subscribe':
                return sprintf('New print subscriber: %s.', $this->user->name);

            case 'user-register':
                return sprintf('%s subscribed to TagesWoche.', $this->user->name);

            case 'topic-follow':
                $params = $this->feed->getParams();
                return sprintf("%s started to follow topic '%s'.", $this->user->name, $params['topic_name']);

            default:
                return sprintf('%s %s', $this->user->name, $this->type);
        }
    }
}
