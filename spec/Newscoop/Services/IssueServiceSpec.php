<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\Entity\Issue;
use Newscoop\Entity\Language;
use Newscoop\Services\IssueService;
use Newscoop\Services\CacheService;
use Newscoop\Services\PublicationService;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Repository\IssueRepository;
use Newscoop\Entity\Publication;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\ORM\AbstractQuery;

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
        AbstractQuery $query,
        CacheService $cacheService)
    {
        $em
            ->getRepository('Newscoop\Entity\Issue')
            ->willReturn($repository);

        $publicationService->getPublication()->willReturn($publication);

        $this->beConstructedWith($em, $publicationService, $cacheService);
    }

    public function it_resolves_issue_from_request_data(
        Request $request,
        Issue $issue,
        ParameterBag $attributes,
        $repository,
        $publication,
        $query)
    {
        $repository->findOneBy(array(
            'publication' => $publication,
            'shortName' => 'may2014',
        ))->willReturn($issue);
        $repository->getIssue('en', $publication, 'may2014')->willReturn($query);
        $query->getOneOrNullResult()->willReturn($issue);

        $request->getRequestUri()->willReturn('/en/may2014/60/test-article.htm');
        $issue->getId()->willReturn(1);
        $issue->getNumber()->willReturn(10);
        $issue->getName()->willReturn("May 2014");
        $issue->getShortName()->willReturn("may2014");
        $language = new Language();
        $language->setId(1);
        $language->setCode("en");
        $issue->getLanguage()->willReturn($language);
        $issue->getLanguageId()->willReturn("1");
        $request->attributes = $attributes;
        $this->issueResolver($request)->shouldReturn($issue);
        $this->getIssueMetadata()->shouldBeLike(array(
            "id" => 1,
            "number" => 10,
            "name" => "May 2014",
            "shortName" => "may2014",
            "code_default_language" => "en",
            "id_default_language" => "1",
        ));
    }

    public function it_gets_the_latest_issue_for_current_publication(
        Issue $issue,
        Publication $publication,
        $query,
        $repository,
        $em)
    {
        $publication->getId()->willReturn(1);
        $em
            ->getReference('Newscoop\Entity\Issue', 7)
            ->willReturn($issue);
        $repository->getLastPublishedByPublication(1)->willReturn($query);
        $issueResult = array(array(
            'id' => 7,
            'number' => 12,
            'workflowStatus' => 'Y',
        ));
        $query->getArrayResult()->willReturn($issueResult);

        $this->getLatestPublishedIssue()->shouldReturn($issue);
    }
}
