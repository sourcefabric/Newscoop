<?php
/**
 * @title Sourcefabric.org newsletter subscription
 */
class SourcefabricNewsletter extends Widget
{
    public function render()
    {
        include_once dirname(__FILE__) . '/newsletterbox.phtml';
    }
}
