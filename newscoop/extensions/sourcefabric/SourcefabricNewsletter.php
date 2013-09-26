<?php
/**
 * @title Sourcefabric.org newsletter subscription
 */
class SourcefabricNewsletter extends Widget
{
    public function __construct()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Sourcefabric.org newsletter subscription', array(), 'extensions');
    }

    public function render()
    {
        include_once dirname(__FILE__) . '/newsletterbox.phtml';
    }
}
