<?php
/**
 * @title Sourcefabric.org dev feed reader
 */
class SourcefabricDevFeed extends FeedWidget
{
    protected $url = 'http://www.sourcefabric.org/en/?tpl=425';

    /**
     * @setting
     * @label Number
     */
    protected $count = 5;
}
