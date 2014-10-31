<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Newscoop\IssueServiceInterface;
use Newscoop\Services\CacheService;
use Newscoop\Services\PublicationService;
use Newscoop\Entity\Issue;
use Newscoop\Entity\Publication;

class ThemesServiceSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\ThemesService');
        $this->shouldImplement('Newscoop\ThemesServiceInterface');
    }

    public function let(
        IssueServiceInterface $issueService,
        Issue $issue,
        CacheService $cacheService,
        PublicationService $publicationService,
        Publication $publication)
    {
        $issueService->getIssue()->willReturn($issue);
        $publicationService->getPublication()->willReturn($publication);
        $this->beConstructedWith($issueService, $cacheService, $publicationService);
    }

    public function it_gets_theme_path(Issue $issue)
    {
        $issue->getId()->willReturn(1);
        $issue->getNumber()->willReturn(10);
        $issue->getName()->willReturn("May 2014");
        $issue->getShortName()->willReturn("may2014");
        $issue->getLanguageId()->willReturn(1);
        $this->getThemePath()->shouldBeString();
    }
}
