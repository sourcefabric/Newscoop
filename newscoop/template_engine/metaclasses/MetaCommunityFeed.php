<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\CommunityFeed;

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
    public function __construct(CommunityFeed $feed)
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
                printf('New print subscriber: %s.', $this->user);
                break;

            case 'user-register':
                printf('%s subscribed to TagesWoche.', $this->user);
                break;

            case 'topic-follow':
                $params = $this->feed->getParams();
                printf("%s started to follow topic '%s'.", $this->user, $params['topic_name']);
                break;

            default:
                printf('%s %s', $this->user, $this->type);
        }
    }
}
