<?php
/**
 * @title Sourcefabric.org feed reader
 */
class SourcefabricFeed extends FeedWidget
{
    protected $title = 'Sourcefabric.org News reader';

    protected $url = 'http://feeds.feedburner.com/SourcefabricNews?format=xml';

    /**
     * @setting
     * @label Number
     */
    protected $count = 5;

    public function __construct()
    {
        $this->title = getGS('Sourcefabric.org News reader');
    }
}
