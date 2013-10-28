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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Sourcefabric.org blog reader', array(), 'extensions');
    }
}
