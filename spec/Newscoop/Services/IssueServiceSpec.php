<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\Entity\Issue;
use Newscoop\Services\IssueService;
use Newscoop\Services\CacheService;
use Newscoop\Services\PublicationService;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Repository\IssueRepository;
use Newscoop\Entity\Publication;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\Common\Collections\ArrayCollection;

class IssueServiceSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\IssueService');
        $this->shouldImplement('Newscoop\IssueServiceInterface');
    }

    public function let(
        EntityManager $em,
        PublicationService $publicationService,
        IssueRepository $repository,
        Issue $issue,
        Publication $publication,
        CacheService $cacheService)
    {
        $em
            ->getRepository('Newscoop\Entity\Issue')
            ->willReturn($repository);

        $publicationService->getPublication()->willReturn($publication);

        $repository->findOneBy(array(
            'publication' => $publication,
            'shortName' => 'may2014'
        ))->willReturn($issue);

        $this->beConstructedWith($em, $publicationService, $cacheService);
    }

    public function it_resolves_issue_from_request_data(Request $request, Issue $issue, ParameterBag $attributes)
    {
        $request->getRequestUri()->willReturn('/en/may2014/60/test-article.htm');
        $issue->getId()->willReturn(1);
        $issue->getNumber()->willReturn(10);
        $issue->getName()->willReturn("May 2014");
        $issue->getShortName()->willReturn("may2014");
        $request->attributes = $attributes;
        $this->issueResolver($request)->shouldReturn($issue);
        $this->getIssueMetadata()->shouldBeLike(array("id" => 1, "number" => 10, "name" => "May 2014", "shortName" => "may2014"));
    }

    public function it_gets_the_latest_issue_for_current_publication(Issue $issue, Publication $publication)
    {
        $pub = new Publication();
        $pub->setId(1);
        $issue = new Issue(1, $pub);
        $issue->setWorkflowStatus('Y');
        $publication->getIssues()->willReturn(new ArrayCollection(array($issue)));
        $this->getLatestPublishedIssue()->shouldReturn($issue);
    }
}
