<?php
/**
 * @title Sourcefabric.org newsletter subscription
 */
class SourcefabricNewsletter extends Widget
{
    public function __construct()
    {
        $this->title = getGS('Sourcefabric.org newsletter subscription');
    }

    public function render()
    {
        include_once dirname(__FILE__) . '/newsletterbox.phtml';
    }
}
