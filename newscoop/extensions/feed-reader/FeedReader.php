<?php
/**
 * @multi
 */
class FeedReader extends FeedWidget
{
    /**
     * @setting
     * @label Title
     */
    protected $title = 'Feed reader';

    /**
     * @setting
     * @label Feed url
     */
    protected $url = '';

    /**
     * @setting
     * @label Number
     */
    protected $count = 5;

    public function __construct()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Feed reader', array(), 'extensions');
    }
}
