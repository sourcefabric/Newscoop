<?php
/**
 * @package Newscoop\CommunityTickerBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\CommunityTickerBundle\EventListener;

use Newscoop\EventDispatcher\Events\CollectObjectsDataEvent;

class ListObjectsListener
{
    /**
     * Register plugin list objects in Newscoop
     * 
     * @param  CollectObjectsDataEvent $event
     */
    public function registerObjects(CollectObjectsDataEvent $event)
    {
        $event->registerListObject('newscoop\communitytickerbundle\templatelist\communityfeeds', array(
            'class' => 'Newscoop\CommunityTickerBundle\TemplateList\CommunityFeeds',
            'list' => 'community_feeds',
            'url_id' => 'cmfid',
        ));

        $event->registerObjectTypes('community_feed', array(
            'class' => '\Newscoop\CommunityTickerBundle\Meta\MetaCommunityTicker'
        ));
    }
}