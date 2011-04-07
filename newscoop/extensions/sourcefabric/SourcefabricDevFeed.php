<?php
/**
 * @title Sourcefabric.org dev feed reader
 */
class SourcefabricDevFeed extends FeedWidget
{
    protected $title = 'Sourcefabric.org dev reader';

    protected $url = 'http://feeds.feedburner.com/SourcefabricDevPrNews?format=xml';

    /**
     * @setting
     * @label Number
     */
    protected $count = 5;
}
