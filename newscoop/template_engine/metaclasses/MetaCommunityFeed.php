<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Events\CommunityTickerEvent;

/**
 */
final class MetaCommunityFeed extends MetaEntity
{
    /** @var string */
    public $type;

    /** @var MetaUser */
    public $user;

    /** @var Newscoop\Entity\CommunityFeed */
    private $feed;

    /**
     * @param Newscoop\Entity\CommunityFeed $feed
     */
    public function __construct(CommunityTickerEvent $feed = null)
    {
        if (!$feed) { // fix getting called once more
            return;
        }

        $this->feed = $feed;
        $this->type = implode('-', explode('.', $this->feed->getEvent()));
        $this->user = new MetaUser($feed->getUser());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        switch ($this->type) {
            case 'print-subscribe':
                return sprintf('New print subscriber: %s.', $this->user);

            case 'user-register':
                return sprintf('%s subscribed to TagesWoche.', $this->user);

            case 'topic-follow':
                $params = $this->feed->getParams();
                return sprintf("%s started to follow topic '%s'.", $this->user, $params['topic_name']);

            default:
                return sprintf('%s %s', $this->user, $this->type);
        }
    }
}
