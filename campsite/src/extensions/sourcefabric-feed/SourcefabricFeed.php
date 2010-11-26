<?php
/**
 * @title Sourcefabric.org feed reader
 */
class SourcefabricFeed extends FeedWidget
{
    protected $url = 'http://www.sourcefabric.org/en/?tpl=259';

    /**
     * @setting
     * @label Number
     */
    protected $count = 5;
}
