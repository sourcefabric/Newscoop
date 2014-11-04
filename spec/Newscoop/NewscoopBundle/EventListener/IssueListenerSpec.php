<?php

namespace spec\Newscoop\NewscoopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Newscoop\Services\IssueService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;

class IssueListenerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\NewscoopBundle\EventListener\IssueListener');
    }

    public function let(IssueService $issueService, Request $request)
    {
        $this->beConstructedWith($issueService);
    }

    public function it_calls_issue_resolver_on_request(GetResponseEvent $event, Request $request)
    {
        $event->getRequest()->willReturn($request);
        $this->onRequest($event)->shouldReturn(null);
    }
}
