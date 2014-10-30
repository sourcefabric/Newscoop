<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ThemesServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\ThemesService');
    }
}
