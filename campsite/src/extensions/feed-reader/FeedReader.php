<?php
/**
 * @title Feed reader
 * @multi
 */
class FeedReader extends FeedWidget
{
    /**
     * @setting
     * @label Feed url
     */
    protected $url = 'http://www.sourcefabric.org/en/?tpl=259';

    /**
     * @setting
     * @label Number
     */
    protected $count = 5;
}
