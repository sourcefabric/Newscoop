<?php
/**
 * @title Sourcefabric.org dev feed reader
 */
class SourcefabricDevFeed extends FeedWidget
{
    protected $title = 'Sourcefabric.org blog reader';

    protected $url = 'http://feeds.feedburner.com/SourcefabricBlogs?format=xml';

    /**
     * @setting
     * @label Number
     */
    protected $count = 5;

    public function __construct()
    {
        $this->title = getGS('Sourcefabric.org blog reader');
    }
}
