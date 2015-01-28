<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RelatedArticlesServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\RelatedArticlesService');
    }

    function it_should_get_related_articles()
    {
        $this->getRelatedArticles();
    }

    function it_should_remove_related_article()
    {
        $this->removeRelatedArticle();
    }

    function it_should_add_related_article()
    {
        $this->addArticle();
    }
}
