<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\Entity\Issue;
use Newscoop\Services\IssueService;
use Newscoop\Services\PublicationService;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Repository\IssueRepository;
use Newscoop\Entity\Publication;
use Symfony\Component\HttpFoundation\ParameterBag;

class IssueServiceSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\IssueService');
        $this->shouldImplement('Newscoop\IssueServiceInterface');
    }

    public function let(EntityManager $em, PublicationService $publicationService, IssueRepository $repository, Issue $issue, Publication $publication)
    {
        $em
            ->getRepository('Newscoop\Entity\Issue')
            ->willReturn($repository);

        $publicationService->getPublication()->willReturn($publication);

        $repository->findOneBy(array(
            'publication' => $publication,
            'shortName' => 'may2014'
        ))->willReturn($issue);

        $this->beConstructedWith($em, $publicationService);
    }

    public function it_resolves_issue_from_request_data(Request $request, Issue $issue, ParameterBag $attributes)
    {
        $issue->getId()->willReturn(1);
        $issue->getNumber()->willReturn(10);
        $issue->getName()->willReturn("May 2014");
        $issue->getShortName()->willReturn("may2014");
        $request->getRequestUri()->willReturn('/en/may2014/60/test-article.htm');
        $request->attributes = $attributes;
        $request->attributes->set('_newscoop_issue_metadata', array(
            'id' => $issue->getId(),
            'number' => $issue->getNumber(),
            'name' => $issue->getName(),
            'shortName' => $issue->getShortName()
        ));

        $this->issueResolver($request)->shouldReturn($issue);
    }

    public function it_gets_current_issue_meta_data(ParameterBag $attributes, Issue $issue)
    {
        $this->getIssueMetadata()->shouldReturn(array());
    }
}
