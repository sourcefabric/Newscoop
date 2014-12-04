<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\EntityManager;

class ArticleServiceSpec extends ObjectBehavior
{
    public function let(
        EntityManager $em
    ){
        $this->beConstructedWith($em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\ArticleService');

    }
}
