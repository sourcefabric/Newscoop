<?php

namespace spec\Newscoop\Installer\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BootstrapServiceSpec extends ObjectBehavior
{
    function let($die)
    {
        $this->mustBeWritable = array('assets');
        $this->basePath = realpath(__DIR__.'/../../../');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Installer\Services\BootstrapService');
        $this->mustBeWritable->shouldBeEqualTo(array('assets'));
    }

    function it_checks_if_it_can_make_directory_writable()
    {
        $this->makeDirectoriesWritable()->shouldReturn(true);
    }

    function it_checks_if_directory_is_writable()
    {
        $this->checkDirectories()->shouldReturn(true);
    }
}
