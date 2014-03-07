<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Newscoop\EventDispatcher\Events\CollectObjectsDataEvent;

/**
 * Register new lists in $gimme
 */
class ListObjectsListener
{
    /**
     * Register plugin list objects in Newscoop
     *
     * @param CollectObjectsDataEvent $event
     */
    public function registerObjects(CollectObjectsDataEvent $event)
    {
        $event->registerListObject('newscoop\templatelist\users', array(
            'class' => 'Newscoop\TemplateList\Users',
            'list' => 'users',
            'url_id' => 'uid',
        ));

        $event->registerListObject('newscoop\templatelist\slideshows', array(
            'class' => 'Newscoop\TemplateList\Slideshows',
            'list' => 'slideshows',
            'url_id' => 'sliid',
        ));

        $event->registerListObject('newscoop\templatelist\slideshowitems', array(
            'class' => 'Newscoop\TemplateList\SlideshowItems',
            'list' => 'slideshow_items',
            'url_id' => 'slit',
        ));

        $event->registerObjectTypes('slideshow', array(
            'class' => '\Newscoop\TemplateList\Meta\SlideshowsMeta'
        ));

        $event->registerObjectTypes('slideshow_item', array(
            'class' => '\Newscoop\TemplateList\Meta\SlideshowItemMeta'
        ));
    }
}
